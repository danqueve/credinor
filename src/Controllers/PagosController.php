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
        if (!\in_array($metodoPago, ['efectivo','transferencia'], true)) {
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

    // GET /admin/creditos/{credito_id}/pago/{cuota_id}
    public function adminForm(array $params): void
    {
        $this->requireRole('admin');

        $cuotaId   = (int) $params['cuota_id'];
        $creditoId = (int) $params['credito_id'];

        $credito = (new Credito())->getConDetalles($creditoId);
        if (!$credito) Response::abort(404, 'Crédito no encontrado.');
        if ($credito['estado'] !== 'activo') Response::abort(403, 'El crédito no está activo.');

        $cuotas = (new Cuota())->getByCreditoOrdenadas($creditoId);
        $cuota  = array_values(array_filter($cuotas, fn($c) => (int)$c['id'] === $cuotaId))[0] ?? null;
        if (!$cuota) Response::abort(404, 'Cuota no encontrada.');
        if ($cuota['estado'] === 'pagada') Response::abort(403, 'La cuota ya está pagada.');

        $saldo           = (float)$cuota['monto'] - (float)($cuota['monto_pagado'] ?? 0);
        $pagosAnteriores = (new Pago())->getByCuota($cuotaId);

        $this->view('admin/pago_form', compact('credito', 'cuota', 'cuotas', 'saldo', 'pagosAnteriores'));
    }

    // POST /admin/creditos/{credito_id}/pago/{cuota_id}
    public function adminStore(array $params): void
    {
        $this->validateCsrf();
        $this->requireRole('admin');

        $cuotaId   = (int) $params['cuota_id'];
        $creditoId = (int) $params['credito_id'];

        $credito = (new Credito())->getConDetalles($creditoId);
        if (!$credito) Response::abort(404);
        if ($credito['estado'] !== 'activo') Response::abort(403);

        $monto     = (float) str_replace(',', '.', Request::post('monto', '0'));
        $montoMora = (float) str_replace(',', '.', Request::post('monto_mora', '0'));

        $metodoPago = Request::post('metodo_pago', 'efectivo');
        if (!\in_array($metodoPago, ['efectivo', 'transferencia'], true)) {
            $metodoPago = 'efectivo';
        }

        try {
            // Pago registrado por admin: se atribuye al cobrador del crédito,
            // queda confirmado directamente (sin pasar por rendición).
            $cobradorId = (int) $credito['cobrador_id'];
            $this->service->registrar($cuotaId, $monto, $montoMora, $metodoPago, $cobradorId, 'confirmado');

            Session::flash('success', 'Pago registrado correctamente.');
            Response::redirect('/creditos/' . $creditoId);
        } catch (\DomainException $e) {
            Session::flash('error', $e->getMessage());
            Response::redirect('/admin/creditos/' . $creditoId . '/pago/' . $cuotaId);
        }
    }

    // GET /admin/api/creditos/{credito_id}/proxima-cuota  (JSON)
    public function proximaCuotaJson(array $params): void
    {
        $this->requireRole('admin');
        $creditoId = (int) $params['credito_id'];

        $credito = (new Credito())->getConDetalles($creditoId);
        if (!$credito) {
            $this->json(['error' => 'Crédito no encontrado.'], 404);
            return;
        }
        if ($credito['estado'] !== 'activo') {
            $this->json(['error' => 'El crédito no está activo.'], 422);
            return;
        }

        $cuotas    = (new Cuota())->getByCreditoOrdenadas($creditoId);
        $pendiente = null;
        foreach ($cuotas as $cu) {
            if (\in_array($cu['estado'], ['pendiente', 'parcial', 'vencida'], true)) {
                $pendiente = $cu;
                break;
            }
        }

        if (!$pendiente) {
            $this->json(['error' => 'No hay cuotas pendientes en este crédito.'], 422);
            return;
        }

        $saldo = (float)$pendiente['monto'] - (float)($pendiente['monto_pagado'] ?? 0);
        $mora  = (float)$credito['mora_acumulada'] - (float)$credito['mora_pagada'];

        $this->json([
            'cuota'          => $pendiente,
            'saldo'          => round($saldo, 2),
            'mora'           => round($mora, 2),
            'cobrador_nombre'=> $credito['cobrador_nombre'] ?? '',
        ]);
    }

    // POST /admin/api/creditos/{credito_id}/pago/{cuota_id}  (JSON)
    public function adminStoreJson(array $params): void
    {
        $this->requireRole('admin');

        // Verificar CSRF (viene en el body del fetch)
        $token = $_POST['_csrf'] ?? '';
        if (!\App\Core\Session::verifyCsrf($token)) {
            $this->json(['error' => 'Token de seguridad inválido.'], 403);
            return;
        }

        $cuotaId   = (int) $params['cuota_id'];
        $creditoId = (int) $params['credito_id'];

        $credito = (new Credito())->getConDetalles($creditoId);
        if (!$credito || $credito['estado'] !== 'activo') {
            $this->json(['error' => 'Crédito no disponible.'], 422);
            return;
        }

        $monto      = (float) str_replace(',', '.', Request::post('monto', '0'));
        $montoMora  = (float) str_replace(',', '.', Request::post('monto_mora', '0'));
        $metodoPago = Request::post('metodo_pago', 'efectivo');
        if (!\in_array($metodoPago, ['efectivo', 'transferencia'], true)) {
            $metodoPago = 'efectivo';
        }

        try {
            $cobradorId = (int) $credito['cobrador_id'];
            $this->service->registrar($cuotaId, $monto, $montoMora, $metodoPago, $cobradorId, 'confirmado');
            $this->json(['success' => true, 'mensaje' => 'Pago registrado correctamente.']);
        } catch (\DomainException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    // GET /admin/pagos
    public function adminListado(): void
    {
        $this->requireRole('admin');
        $db = \App\Core\Database::getInstance();

        $pagos = $db->query(
            "SELECT p.*, cu.numero_cuota, cl.nombre AS cliente_nombre,
                    cr.id AS credito_id, u_cob.nombre AS cobrador_nombre,
                    u_anul.nombre AS anulado_por_nombre
             FROM pagos p
             JOIN cuotas cu ON p.cuota_id = cu.id
             JOIN creditos cr ON cu.credito_id = cr.id
             JOIN clientes cl ON cr.cliente_id = cl.id
             JOIN usuarios u_cob ON p.cobrador_id = u_cob.id
             LEFT JOIN usuarios u_anul ON p.anulado_por = u_anul.id
             ORDER BY p.created_at DESC
             LIMIT 200"
        )->fetchAll();

        $this->view('admin/pagos', compact('pagos'));
    }

    // POST /admin/pagos/{id}/anular
    public function anular(array $params): void
    {
        $this->validateCsrf();
        $this->requireRole('admin');

        $motivo = trim(Request::post('motivo', ''));
        if ($motivo === '') {
            Session::flash('error', 'El motivo de anulación es obligatorio.');
            Response::redirect('/admin/pagos');
        }

        try {
            $this->service->anular((int)$params['id'], Auth::id(), $motivo);
            Session::flash('success', 'Pago anulado correctamente.');
        } catch (\DomainException $e) {
            Session::flash('error', $e->getMessage());
        }
        Response::redirect('/admin/pagos');
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
