<?php
// src/Controllers/RendicionesController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Rendicion;
use App\Services\RendicionService;
use App\Helpers\MoneyHelper;

class RendicionesController extends Controller
{
    private Rendicion $model;
    private RendicionService $service;

    public function __construct()
    {
        $this->model   = new Rendicion();
        $this->service = new RendicionService();
    }

    // GET /admin/rendiciones
    public function index(): void
    {
        $this->requireRole('admin');

        $estado = Request::get('estado', 'pendiente');
        $rendiciones = $this->model->getAdminListado($estado);
        $totales     = $this->model->getTotalesPorEstado();

        $this->view('admin/rendiciones', compact('rendiciones', 'estado', 'totales'));
    }

    // GET /admin/rendiciones/{id}
    public function show(array $params): void
    {
        $this->requireRole('admin');

        $rendicion = $this->model->getConPagos((int) $params['id']);
        if (!$rendicion) Response::abort(404, 'Rendición no encontrada.');

        $this->view('admin/rendicion_detalle', compact('rendicion'));
    }

    // POST /admin/rendiciones/{id}/confirmar
    public function confirmar(array $params): void
    {
        $this->validateCsrf();
        $this->requireRole('admin');

        $montoRecibido = (float) str_replace(',', '.', Request::post('monto_recibido', '0'));

        if ($montoRecibido <= 0) {
            Session::flash('error', 'Ingresá el monto recibido.');
            Response::redirect('/admin/rendiciones/' . $params['id']);
        }

        try {
            $this->service->confirmar((int) $params['id'], $montoRecibido, Auth::id());

            $diff = $montoRecibido - $this->model->find((int)$params['id'])['monto_declarado'];
            if (abs($diff) > 0.01) {
                $signo = $diff > 0 ? '+' : '';
                Session::flash('info', "Rendición confirmada. Diferencia: {$signo}" . MoneyHelper::format($diff));
            } else {
                Session::flash('success', 'Rendición confirmada sin diferencias. ✅');
            }

            Response::redirect('/admin/rendiciones');
        } catch (\DomainException $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect('/admin/rendiciones/' . $params['id']);
        }
    }

    // GET /admin/rendiciones/{id}/pdf
    public function pdf(array $params): void
    {
        $this->requireRole('admin');

        $rendicion = $this->model->getConPagos((int) $params['id']);
        if (!$rendicion) Response::abort(404, 'Rendición no encontrada.');

        $totalEfectivo      = 0.0;
        $totalTransferencia = 0.0;
        foreach ($rendicion['pagos'] as $p) {
            if ($p['metodo_pago'] === 'transferencia') {
                $totalTransferencia += (float)$p['monto'];
            } else {
                $totalEfectivo += (float)$p['monto'];
            }
        }

        ob_start();
        extract(compact('rendicion', 'totalEfectivo', 'totalTransferencia'));
        require ROOT_PATH . '/views/pdf/rendicion.php';
        $html = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf();
        $options = $dompdf->getOptions();
        $options->setDefaultFont('Helvetica');
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'rendicion-' . $rendicion['id'] . '-' . $rendicion['fecha'] . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    // POST /admin/rendiciones/{id}/rechazar
    public function rechazar(array $params): void
    {
        $this->validateCsrf();
        $this->requireRole('admin');

        $motivo = trim(Request::post('motivo', ''));
        if (!$motivo) {
            Session::flash('error', 'Ingresá el motivo del rechazo.');
            Response::redirect('/admin/rendiciones/' . $params['id']);
        }

        $this->model->update((int) $params['id'], [
            'estado' => 'rechazada',
            'observaciones' => $motivo,
        ]);

        Session::flash('success', 'Rendición rechazada. El cobrador podrá volver a cerrar caja.');
        Response::redirect('/admin/rendiciones');
    }
}
