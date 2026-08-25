<?php
// =======================================================
// Controlador: ClienteController (Portal de Pasajeros)
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/Models/Viaje.php';
require_once ROOT_PATH . '/app/Models/Ruta.php';
require_once ROOT_PATH . '/app/Models/Muelle.php';
require_once ROOT_PATH . '/app/Models/Boleto.php';
require_once ROOT_PATH . '/app/Models/Carga.php';

class ClienteController extends Controller {
    private Viaje $viajeModel;
    private Ruta $rutaModel;
    private Muelle $muelleModel;
    private Boleto $boletoModel;
    private Carga $cargaModel;

    public function __construct() {
        AuthHelper::requireAuth();
        $this->viajeModel = new Viaje();
        $this->rutaModel = new Ruta();
        $this->muelleModel = new Muelle();
        $this->boletoModel = new Boleto();
        $this->cargaModel = new Carga();
    }

    public function portal(): void {
        $muelles = $this->muelleModel->getActivos();
        
        $origenId = (int)($_GET['origen_id'] ?? 0);
        $destinoId = (int)($_GET['destino_id'] ?? 0);
        $fecha = $_GET['fecha'] ?? '';

        $filtros = [];
        if ($origenId > 0) $filtros['origen_id'] = $origenId;
        if ($destinoId > 0) $filtros['destino_id'] = $destinoId;
        if (!empty($fecha)) $filtros['fecha'] = $fecha;

        $viajes = $this->viajeModel->getViajesDisponibles($filtros);

        $this->render('cliente/portal', [
            'pageTitle'  => 'Explorar Rutas y Horarios Fluviales - ' . APP_NAME,
            'muelles'    => $muelles,
            'viajes'     => $viajes,
            'filtros'    => [
                'origen_id'  => $origenId,
                'destino_id' => $destinoId,
                'fecha'      => $fecha
            ]
        ]);
    }

    public function comprar(): void {
        $viajeId = (int)($_GET['viaje_id'] ?? 0);
        $viaje = $this->viajeModel->findWithDetails($viajeId);

        if (!$viaje || (int)$viaje['cupos_disponibles'] <= 0 || !in_array($viaje['estado'], ['programado', 'en_embarque'])) {
            SessionHelper::setFlash('warning', 'El viaje seleccionado no tiene cupos disponibles o no se encuentra activo.');
            $this->redirect('/portal');
        }

        $usuario = AuthHelper::user();

        $this->render('cliente/comprar', [
            'pageTitle' => 'Comprar Tiquete Fluvial - ' . APP_NAME,
            'viaje'     => $viaje,
            'usuario'   => $usuario
        ]);
    }

    public function procesarCompra(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/portal');
        }

        $viajeId = (int)($_POST['viaje_id'] ?? 0);
        $doc = trim($_POST['pasajero_documento'] ?? '');
        $nombre = trim($_POST['pasajero_nombre'] ?? '');
        $tel = trim($_POST['pasajero_telefono'] ?? '');
        $metodo = $_POST['metodo_pago'] ?? 'transferencia';
        $precio = (float)($_POST['precio_pagado'] ?? 0);
        $usuario = AuthHelper::user();

        if ($viajeId === 0 || empty($doc) || empty($nombre)) {
            SessionHelper::setFlash('danger', 'Por favor complete todos los datos del pasajero.');
            $this->redirect('/cliente/comprar?viaje_id=' . $viajeId);
        }

        try {
            $boletoId = $this->boletoModel->emitir([
                'viaje_id'           => $viajeId,
                'usuario_id'         => $usuario['id'],
                'pasajero_documento' => $doc,
                'pasajero_nombre'    => $nombre,
                'pasajero_telefono'  => $tel,
                'precio_pagado'      => $precio,
                'metodo_pago'        => $metodo,
                'vendido_por_id'     => $usuario['id']
            ]);

            SessionHelper::setFlash('success', '¡Tiquete comprado con éxito! Ya puedes ver tu ruta y tiquete.');
            $this->redirect('/cliente/ver-ruta?id=' . $boletoId);
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', 'Error al procesar la compra: ' . $e->getMessage());
            $this->redirect('/cliente/comprar?viaje_id=' . $viajeId);
        }
    }

    public function misBoletos(): void {
        $usuario = AuthHelper::user();
        $boletos = $this->boletoModel->getByUsuario((int)$usuario['id']);

        $this->render('cliente/mis_boletos', [
            'pageTitle' => 'Mis Tiquetes y Rutas Compradas - ' . APP_NAME,
            'boletos'   => $boletos
        ]);
    }

    public function verRuta(): void {
        $boletoId = (int)($_GET['id'] ?? 0);
        $usuario = AuthHelper::user();

        $boleto = $this->boletoModel->findWithDetails($boletoId);

        if (!$boleto) {
            SessionHelper::setFlash('danger', 'El tiquete solicitado no fue encontrado.');
            $this->redirect('/cliente/mis-boletos');
        }

        // Si es cliente, verificar que el boleto le pertenezca o coincida con su documento
        if (AuthHelper::isCliente()) {
            if (!empty($boleto['usuario_id']) && (int)$boleto['usuario_id'] !== (int)$usuario['id']) {
                if ($boleto['pasajero_documento'] !== ($usuario['documento'] ?? '')) {
                    SessionHelper::setFlash('danger', 'No tiene permisos para ver este tiquete.');
                    $this->redirect('/cliente/mis-boletos');
                }
            }
        }

        $this->render('cliente/ver_ruta', [
            'pageTitle' => 'Detalle de Mi Ruta y Viaje - ' . $boleto['codigo_boleto'],
            'boleto'    => $boleto
        ]);
    }

    public function encomiendaCrear(): void {
        $viajesDisponibles = $this->viajeModel->getViajesDisponibles();
        $usuario = AuthHelper::user();

        $this->render('cliente/encomienda_crear', [
            'pageTitle'         => 'Enviar Encomienda Fluvial - ' . APP_NAME,
            'viajesDisponibles' => $viajesDisponibles,
            'usuario'           => $usuario
        ]);
    }

    public function guardarEncomienda(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cliente/mis-encomiendas');
        }

        $viajeId = (int)($_POST['viaje_id'] ?? 0);
        $remitente = trim($_POST['remitente_nombre'] ?? '');
        $remTel = trim($_POST['remitente_telefono'] ?? '');
        $destinatario = trim($_POST['destinatario_nombre'] ?? '');
        $destTel = trim($_POST['destinatario_telefono'] ?? '');
        $desc = trim($_POST['descripcion_carga'] ?? '');
        $peso = (float)($_POST['peso_kg'] ?? 0);
        $valorDeclarado = (float)($_POST['valor_declarado'] ?? 0);
        $valorFlete = (float)($_POST['valor_flete'] ?? 0);
        $usuario = AuthHelper::user();

        if ($viajeId === 0 || empty($remitente) || empty($destinatario) || $peso <= 0) {
            SessionHelper::setFlash('danger', 'Por favor complete todos los datos requeridos para el envío.');
            $this->redirect('/cliente/enviar-encomienda');
        }

        try {
            $this->cargaModel->registrar([
                'viaje_id'              => $viajeId,
                'usuario_id'            => $usuario['id'],
                'remitente_nombre'      => $remitente,
                'remitente_telefono'    => $remTel,
                'destinatario_nombre'   => $destinatario,
                'destinatario_telefono' => $destTel,
                'descripcion_carga'     => $desc,
                'peso_kg'               => $peso,
                'valor_declarado'       => $valorDeclarado,
                'valor_flete'           => $valorFlete,
                'registrado_por_id'     => $usuario['id']
            ]);

            SessionHelper::setFlash('success', '¡Guía de encomienda solicitada con éxito!');
            $this->redirect('/cliente/mis-encomiendas');
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', 'Error al procesar la encomienda: ' . $e->getMessage());
            $this->redirect('/cliente/enviar-encomienda');
        }
    }

    public function misEncomiendas(): void {
        $usuario = AuthHelper::user();
        $encomiendas = $this->cargaModel->getByUsuario((int)$usuario['id']);

        $this->render('cliente/mis_encomiendas', [
            'pageTitle'   => 'Mis Encomiendas y Cargas Fluviales - ' . APP_NAME,
            'encomiendas' => $encomiendas
        ]);
    }
}
