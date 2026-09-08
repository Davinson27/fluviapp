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
require_once ROOT_PATH . '/app/Services/AsistenteFluvialIA.php';

class ClienteController extends Controller {
    private Viaje $viajeModel;
    private Ruta $rutaModel;
    private Muelle $muelleModel;
    private Boleto $boletoModel;
    private Carga $cargaModel;
    private AsistenteFluvialIA $asistenteIA;

    public function __construct() {
        AuthHelper::requireCliente();
        $this->viajeModel = new Viaje();
        $this->rutaModel = new Ruta();
        $this->muelleModel = new Muelle();
        $this->boletoModel = new Boleto();
        $this->cargaModel = new Carga();
        $this->asistenteIA = new AsistenteFluvialIA();
    }

    public function portal(): void {
        $usuario = AuthHelper::user();
        $userDepto = $usuario['departamento'] ?? '';
        
        $origenId = (int)($_GET['origen_id'] ?? 0);
        $destinoId = (int)($_GET['destino_id'] ?? 0);
        $fecha = $_GET['fecha'] ?? '';

        $filtros = [];
        if (!empty($userDepto)) {
            $muelles = $this->muelleModel->getByDepartamento($userDepto);
            $filtros['departamento'] = $userDepto;
        } else {
            $muelles = $this->muelleModel->getActivos();
        }

        if ($origenId > 0) $filtros['origen_id'] = $origenId;
        if ($destinoId > 0) $filtros['destino_id'] = $destinoId;
        if (!empty($fecha)) $filtros['fecha'] = $fecha;

        $viajes = $this->viajeModel->getViajesDisponibles($filtros);

        // Si no hay viajes y el usuario aplicó filtros de búsqueda (especialmente con fecha seleccionada)
        $sugerenciaIA = null;
        $busquedaRealizada = ($origenId > 0 || $destinoId > 0 || !empty($fecha));

        if (empty($viajes) && $busquedaRealizada) {
            $origenMuelle = ($origenId > 0) ? $this->muelleModel->find($origenId) : null;
            $destinoMuelle = ($destinoId > 0) ? $this->muelleModel->find($destinoId) : null;

            // Verificar si la ruta geográfica existe
            $existeRuta = false;
            if ($origenId > 0 && $destinoId > 0) {
                $rutaObj = $this->rutaModel->findByMuelles($origenId, $destinoId);
                $existeRuta = ($rutaObj !== null);
            }

            // Buscar viajes alternativos en otras fechas para esta ruta o muelles
            $alternativas = $this->viajeModel->getFechasAlternativasConViajes(
                $origenId,
                $destinoId,
                $fecha,
                $userDepto
            );

            // Generar la respuesta inteligente por IA
            $sugerenciaIA = $this->asistenteIA->generarSugerencia(
                $usuario,
                $fecha,
                $origenMuelle,
                $destinoMuelle,
                $alternativas,
                $existeRuta
            );
        }

        $this->render('cliente/portal', [
            'pageTitle'          => 'Explorar Rutas y Horarios Fluviales - ' . APP_NAME,
            'muelles'            => $muelles,
            'viajes'             => $viajes,
            'userDepto'          => $userDepto,
            'usuario'            => $usuario,
            'busquedaRealizada'  => $busquedaRealizada,
            'sugerenciaIA'       => $sugerenciaIA,
            'filtros'            => [
                'origen_id'   => $origenId,
                'destino_id'  => $destinoId,
                'fecha'       => $fecha
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
        $userDepto = $usuario['departamento'] ?? '';

        if (!empty($userDepto)) {
            if (($viaje['origen_depto'] ?? '') !== $userDepto && ($viaje['destino_depto'] ?? '') !== $userDepto) {
                SessionHelper::setFlash('danger', "El viaje seleccionado no pertenece a su departamento registrado ({$userDepto}). Solo puede viajar en rutas de su departamento.");
                $this->redirect('/portal');
            }
        }

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
        $metodo = $this->sanitizePago($_POST['metodo_pago'] ?? 'transferencia');
        $usuario = AuthHelper::user();
        $userDepto = $usuario['departamento'] ?? '';

        if ($viajeId === 0 || empty($doc) || empty($nombre)) {
            SessionHelper::setFlash('danger', 'Por favor complete todos los datos del pasajero.');
            $this->redirect('/cliente/comprar?viaje_id=' . $viajeId);
        }

        if (!empty($userDepto)) {
            $viaje = $this->viajeModel->findWithDetails($viajeId);
            if ($viaje && ($viaje['origen_depto'] ?? '') !== $userDepto && ($viaje['destino_depto'] ?? '') !== $userDepto) {
                SessionHelper::setFlash('danger', "No puede comprar pasajes para rutas fuera de su departamento ({$userDepto}).");
                $this->redirect('/portal');
            }
        }

        try {
            $boletoId = $this->boletoModel->emitir([
                'viaje_id'           => $viajeId,
                'usuario_id'         => $usuario['id'],
                'pasajero_documento' => $doc,
                'pasajero_nombre'    => $nombre,
                'pasajero_telefono'  => $tel,
                'metodo_pago'        => $metodo,
                'vendido_por_id'     => $usuario['id']
            ]);

            SessionHelper::setFlash('success', '¡Tiquete comprado con éxito! Ya puedes ver tu ruta y tiquete.');
            $this->redirect('/cliente/ver-ruta?id=' . $boletoId);
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', $this->userErrorMessage($e, 'Error al procesar la compra.'));
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

        if (!$boleto || (int)($boleto['usuario_id'] ?? 0) !== (int)$usuario['id']) {
            SessionHelper::setFlash('danger', 'No tiene permisos para ver este tiquete.');
            $this->redirect('/cliente/mis-boletos');
        }

        $this->render('cliente/ver_ruta', [
            'pageTitle' => 'Detalle de Mi Ruta y Viaje - ' . $boleto['codigo_boleto'],
            'boleto'    => $boleto
        ]);
    }

    public function encomiendaCrear(): void {
        $usuario = AuthHelper::user();
        $userDepto = $usuario['departamento'] ?? '';

        $filtros = [];
        if (!empty($userDepto)) {
            $filtros['departamento'] = $userDepto;
        }

        $viajesDisponibles = $this->viajeModel->getViajesDisponibles($filtros);

        $this->render('cliente/encomienda_crear', [
            'pageTitle'         => 'Enviar Encomienda Fluvial - ' . APP_NAME,
            'viajesDisponibles' => $viajesDisponibles,
            'usuario'           => $usuario,
            'userDepto'         => $userDepto
        ]);
    }

    public function guardarEncomienda(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cliente/mis-encomiendas');
        }

        $viajeId = (int)($_POST['viaje_id'] ?? 0);
        $remitente = trim($_POST['remitente_nombre'] ?? '');
        $remTel = trim($_POST['remitente_telefono'] ?? '');
        $remEmail = trim($_POST['remitente_email'] ?? ($usuario['email'] ?? ''));
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

        $userDepto = $usuario['departamento'] ?? '';
        if (!empty($userDepto)) {
            $viaje = $this->viajeModel->findWithDetails($viajeId);
            if ($viaje && ($viaje['origen_depto'] ?? '') !== $userDepto && ($viaje['destino_depto'] ?? '') !== $userDepto) {
                SessionHelper::setFlash('danger', "No puede enviar encomiendas en viajes fuera de su departamento registrado ({$userDepto}).");
                $this->redirect('/cliente/enviar-encomienda');
            }
        }

        try {
            $this->cargaModel->registrar([
                'viaje_id'              => $viajeId,
                'usuario_id'            => $usuario['id'],
                'remitente_nombre'      => $remitente,
                'remitente_telefono'    => $remTel,
                'remitente_email'       => !empty($remEmail) ? $remEmail : null,
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
            SessionHelper::setFlash('danger', $this->userErrorMessage($e, 'Error al procesar la encomienda.'));
            $this->redirect('/cliente/enviar-encomienda');
        }
    }

    public function reportarIncidencia(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cliente/mis-encomiendas');
        }

        require_once ROOT_PATH . '/app/Models/Soporte.php';
        $usuario = AuthHelper::user();
        $soporteModel = new Soporte();

        $cargaId = (int)($_POST['carga_id'] ?? 0);
        $guiaNumero = trim($_POST['guia_numero'] ?? '');
        $contactoNombre = trim($_POST['contacto_nombre'] ?? ($usuario['nombre'] ?? ''));
        $contactoTelefono = trim($_POST['contacto_telefono'] ?? ($usuario['telefono'] ?? ''));
        $contactoEmail = trim($_POST['contacto_email'] ?? ($usuario['email'] ?? ''));
        $asunto = trim($_POST['asunto'] ?? 'Encomienda no entregada / Novedad');
        $mensaje = trim($_POST['mensaje'] ?? '');

        if (empty($mensaje)) {
            SessionHelper::setFlash('danger', 'Debe detallar el motivo de su solicitud a soporte.');
            $this->redirect('/cliente/mis-encomiendas');
        }

        try {
            $soporteModel->crearIncidencia([
                'carga_id'          => $cargaId > 0 ? $cargaId : null,
                'usuario_id'        => (int)($usuario['id'] ?? 0),
                'guia_numero'       => $guiaNumero,
                'contacto_nombre'   => $contactoNombre,
                'contacto_telefono' => $contactoTelefono,
                'contacto_email'    => $contactoEmail,
                'asunto'            => $asunto,
                'mensaje'           => $mensaje
            ]);

            SessionHelper::setFlash('success', '¡Tu reporte de soporte fue radicado exitosamente! Un asesor de operaciones fluviales se comunicará contigo.');
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', $this->userErrorMessage($e, 'No fue posible registrar la solicitud de soporte.'));
        }

        $this->redirect('/cliente/mis-encomiendas');
    }

    public function misEncomiendas(): void {
        $usuario = AuthHelper::user();
        $encomiendas = $this->cargaModel->getByUsuario((int)$usuario['id']);

        $this->render('cliente/mis_encomiendas', [
            'pageTitle'   => 'Mis Encomiendas y Cargas Fluviales - ' . APP_NAME,
            'encomiendas' => $encomiendas
        ]);
    }

    public function facturaBoleto(): void {
        $boletoId = (int)($_GET['id'] ?? 0);
        $usuario = AuthHelper::user();

        $boleto = $this->boletoModel->findWithDetails($boletoId);

        if (!$boleto || ((int)($boleto['usuario_id'] ?? 0) !== (int)$usuario['id'] && !AuthHelper::isStaff())) {
            SessionHelper::setFlash('danger', 'No tiene permisos para ver o descargar esta factura digital.');
            $this->redirect('/cliente/mis-boletos');
        }

        $this->renderSingle('cliente/factura_boleto', [
            'pageTitle' => 'Factura Digital de Pasaje Fluvial - ' . $boleto['codigo_boleto'],
            'boleto'    => $boleto
        ]);
    }

    public function facturaEncomienda(): void {
        $cargaId = (int)($_GET['id'] ?? 0);
        $usuario = AuthHelper::user();

        $carga = $this->cargaModel->findWithDetails($cargaId);

        if (!$carga || ((int)($carga['usuario_id'] ?? 0) !== (int)$usuario['id'] && !AuthHelper::isStaff())) {
            SessionHelper::setFlash('danger', 'No tiene permisos para ver o descargar esta factura de encomienda.');
            $this->redirect('/cliente/mis-encomiendas');
        }

        $this->renderSingle('cliente/factura_encomienda', [
            'pageTitle' => 'Factura Oficial de Flete Fluvial - ' . $carga['guia_numero'],
            'carga'     => $carga
        ]);
    }
}

