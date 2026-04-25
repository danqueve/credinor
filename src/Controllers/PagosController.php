<?php
// src/Controllers/PagosController.php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Cuota;
use App\Models\Credito;
use App\Models\Pago;
use App\Services\PagoService;
use Dompdf\Dompdf;
use Dompdf\Options;

class PagosController extends Controller
{
    private PagoService $service;

    public function __construct()
    {
        $this->service = new PagoService();
    }

    // GET /cobrador/pago/{credito_id}/{cuota_id}
    public function form(array $params): void
    {
        $this->requireStaff();

        $cuotaId   = (int) $params['cuota_id'];
        $creditoId = (int) $params['credito_id'];

        $credito = (new Credito())->getConDetalles($creditoId);
        if (!$credito || (int)$credito['cobrador_id'] !== Auth::id()) {
            Response::abort(403, 'No tenés acceso a este crédito.');
        }

        $cuotas   = (new Cuota())->getByCreditoOrdenadas($creditoId);
        // Cuota seleccionada
        $cuota = array_values(array_filter($cuotas, fn($c) => (int)$c['id'] === $cuotaId))[0] ?? null;
        if (!$cuota) Response::abort(404, 'Cuota no encontrada.');

        // Saldo de la cuota
        $saldo = (float)$cuota['monto'] - (float)($cuota['monto_pagado'] ?? 0);

        $pagosAnteriores = (new Pago())->getByCuota($cuotaId);

        $this->view('cobrador/pago_form', compact(
            'credito', 'cuota', 'cuotas', 'saldo', 'pagosAnteriores'
        ));
    }

    // POST /cobrador/pago/{credito_id}/{cuota_id}
    public function store(array $params): void
    {
        $this->validateCsrf();
        $this->requireStaff();

        $cuotaId   = (int) $params['cuota_id'];
        $creditoId = (int) $params['credito_id'];

        $monto     = (float) str_replace(',', '.', Request::post('monto', '0'));
        $montoMora = (float) str_replace(',', '.', Request::post('monto_mora', '0'));
        
        $metodoPago = Request::post('metodo_pago', 'efectivo');
        if (!in_array($metodoPago, ['efectivo','transferencia'], true)) {
            $metodoPago = 'efectivo';
        }

        try {
            $pagoId = $this->service->registrar($cuotaId, $monto, $montoMora, $metodoPago);
            Session::flash('success', 'Pago registrado correctamente.');

            // Redirigir según opción del form
            if (Request::post('accion') === 'recibo') {
                Response::redirect('/cobrador/pago/' . $pagoId . '/recibo');
            }
            Response::redirect('/dashboard');
        } catch (\DomainException $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect('/cobrador/pago/' . $creditoId . '/' . $cuotaId);
        }
    }

    // GET /cobrador/pago/{pago_id}/recibo
    public function recibo(array $params): void
    {
        $this->requireRole(['admin', 'cobrador', 'vendedor']);
        $pagoId = (int) $params['pago_id'];

        $pago = (new Pago())->getPagoConDetalles($pagoId);
        if (!$pago) Response::abort(404, 'Pago no encontrado.');

        // Generar PDF con DomPDF
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);

        ob_start();
        require ROOT_PATH . '/views/cobrador/recibo_pdf.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, 226.77, 600], 'portrait'); // ~80mm de ancho
        $dompdf->render();

        $dompdf->stream('recibo-' . $pagoId . '.pdf', ['Attachment' => false]);
        exit;
    }
}
