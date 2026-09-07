<?php
// =======================================================
// Controlador: BoletosController
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/Models/Boleto.php';
require_once ROOT_PATH . '/app/Models/Viaje.php';

class BoletosController extends Controller {
    private Boleto $boletoModel;
    private Viaje $viajeModel;

    public function __construct() {
        AuthHelper::requireAuth();
        $this->boletoModel = new Boleto();
        $this->viajeModel = new Viaje();
    }

    public function index(): void {
        AuthHelper::requireStaff();
        $deptScope = AuthHelper::getDepartmentFilter();
        $boletos = $this->boletoModel->allWithViaje($deptScope);
        $this->render('boletos/index', [
            'pageTitle' => 'Control de Boletería y Pasajes - ' . APP_NAME,
            'boletos'   => $boletos,
            'deptScope' => $deptScope
        ]);
    }

    public function create(): void {
        AuthHelper::requireStaff();
        $deptScope = AuthHelper::getDepartmentFilter();
        // Obtener viajes disponibles con cupos filtrados por departamento si aplica
        $viajes = $this->viajeModel->allWithDetails('programado', $deptScope);
        $viajesEmbarque = $this->viajeModel->allWithDetails('en_embarque', $deptScope);
        $viajesDisponibles = array_merge($viajes, $viajesEmbarque);

        $this->render('boletos/create', [
            'pageTitle'         => 'Emitir Boleto de Pasaje - ' . APP_NAME,
            'viajesDisponibles' => $viajesDisponibles,
            'deptScope'         => $deptScope
        ]);
    }

    public function store(): void {
        AuthHelper::requireStaff();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/boletos');
        }

        $viajeId = (int)($_POST['viaje_id'] ?? 0);
        $doc = trim($_POST['pasajero_documento'] ?? '');
        $nombre = trim($_POST['pasajero_nombre'] ?? '');
        $tel = trim($_POST['pasajero_telefono'] ?? '');
        $precio = (float)($_POST['precio_pagado'] ?? 0);
        $metodo = $_POST['metodo_pago'] ?? 'efectivo';
        $user = AuthHelper::user();

        if ($viajeId === 0 || empty($doc) || empty($nombre)) {
            SessionHelper::setFlash('danger', 'Complete los datos obligatorios del pasajero y seleccione un viaje.');
            $this->redirect('/boletos/crear');
        }

        $deptScope = AuthHelper::getDepartmentFilter();
        if (!empty($deptScope)) {
            $viaje = $this->viajeModel->findWithDetails($viajeId);
            if ($viaje && ($viaje['origen_depto'] ?? '') !== $deptScope && ($viaje['destino_depto'] ?? '') !== $deptScope) {
                SessionHelper::setFlash('danger', "No tiene permisos para emitir boletos para viajes fuera de su departamento ({$deptScope}).");
                $this->redirect('/boletos/crear');
            }
        }

        try {
            $boletoId = $this->boletoModel->emitir([
                'viaje_id'           => $viajeId,
                'pasajero_documento' => $doc,
                'pasajero_nombre'    => $nombre,
                'pasajero_telefono'  => $tel,
                'precio_pagado'      => $precio,
                'allow_custom_price' => true,
                'metodo_pago'        => $this->sanitizePago($metodo),
                'vendido_por_id'     => $user['id'] ?? null
            ]);

            SessionHelper::setFlash('success', 'Boleto emitido exitosamente.');
            $this->redirect('/boletos/ticket?id=' . $boletoId);
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', $this->userErrorMessage($e, 'Error al emitir el boleto.'));
            $this->redirect('/boletos/crear');
        }
    }

    public function ticket(): void {
        $id = (int)($_GET['id'] ?? 0);
        $boleto = $this->boletoModel->findWithDetails($id);

        if (!$boleto) {
            SessionHelper::setFlash('danger', 'El boleto no fue encontrado.');
            $dest = AuthHelper::isCliente() ? '/cliente/mis-boletos' : '/boletos';
            $this->redirect($dest);
        }

        $user = AuthHelper::user();
        if (!AuthHelper::isStaff() && (int)($boleto['usuario_id'] ?? 0) !== (int)($user['id'] ?? 0)) {
            SessionHelper::setFlash('danger', 'No tiene permisos para ver este boleto.');
            $this->redirect('/portal');
        }

        $this->renderSingle('boletos/ticket', [
            'pageTitle' => 'Tiquete de Pasaje - ' . $boleto['codigo_boleto'],
            'boleto'    => $boleto
        ]);
    }

    public function factura(): void {
        $id = (int)($_GET['id'] ?? 0);
        $boleto = $this->boletoModel->findWithDetails($id);

        if (!$boleto) {
            SessionHelper::setFlash('danger', 'El boleto no fue encontrado.');
            $dest = AuthHelper::isCliente() ? '/cliente/mis-boletos' : '/boletos';
            $this->redirect($dest);
        }

        $user = AuthHelper::user();
        if (!AuthHelper::isStaff() && (int)($boleto['usuario_id'] ?? 0) !== (int)($user['id'] ?? 0)) {
            SessionHelper::setFlash('danger', 'No tiene permisos para ver o descargar esta factura.');
            $this->redirect('/portal');
        }

        $this->renderSingle('cliente/factura_boleto', [
            'pageTitle' => 'Factura Digital de Boleto - ' . $boleto['codigo_boleto'],
            'boleto'    => $boleto
        ]);
    }
}
