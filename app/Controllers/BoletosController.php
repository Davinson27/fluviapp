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
        $boletos = $this->boletoModel->allWithViaje();
        $this->render('boletos/index', [
            'pageTitle' => 'Control de Boletería y Pasajes - ' . APP_NAME,
            'boletos'   => $boletos
        ]);
    }

    public function create(): void {
        // Obtener viajes disponibles con cupos
        $viajes = $this->viajeModel->allWithDetails('programado');
        $viajesEmbarque = $this->viajeModel->allWithDetails('en_embarque');
        $viajesDisponibles = array_merge($viajes, $viajesEmbarque);

        $this->render('boletos/create', [
            'pageTitle'         => 'Emitir Boleto de Pasaje - ' . APP_NAME,
            'viajesDisponibles' => $viajesDisponibles
        ]);
    }

    public function store(): void {
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

        try {
            $boletoId = $this->boletoModel->emitir([
                'viaje_id'           => $viajeId,
                'pasajero_documento' => $doc,
                'pasajero_nombre'    => $nombre,
                'pasajero_telefono'  => $tel,
                'precio_pagado'      => $precio,
                'metodo_pago'        => $metodo,
                'vendido_por_id'     => $user['id'] ?? null
            ]);

            SessionHelper::setFlash('success', 'Boleto emitido exitosamente.');
            $this->redirect('/boletos/ticket?id=' . $boletoId);
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', 'Error al emitir el boleto: ' . $e->getMessage());
            $this->redirect('/boletos/crear');
        }
    }

    public function ticket(): void {
        $id = (int)($_GET['id'] ?? 0);
        $boleto = $this->boletoModel->findWithDetails($id);

        if (!$boleto) {
            SessionHelper::setFlash('danger', 'El boleto no fue encontrado.');
            $this->redirect('/boletos');
        }

        $this->renderSingle('boletos/ticket', [
            'pageTitle' => 'Tiquete de Pasaje - ' . $boleto['codigo_boleto'],
            'boleto'    => $boleto
        ]);
    }
}
