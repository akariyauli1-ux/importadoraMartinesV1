<?php

namespace App\Http\Controllers;

use App\Models\OrdenReparacion;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    public function download($numero_ticket)
    {
        $order = OrdenReparacion::with(['cliente', 'sucursal'])
            ->where('numero_ticket', $numero_ticket)
            ->firstOrFail();

        $incompleteStates = ['por_diagnosticar'];
        if (in_array($order->estado, $incompleteStates)) {
            return new Response(
                '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>No disponible</title>'
                . '<style>body{font-family:sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;background:#f8f9fa;color:#333}'
                . '.card{text-align:center;background:#fff;padding:40px 50px;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.08);max-width:480px}'
                . 'h2{color:#e50914;margin-bottom:10px}p{color:#555;line-height:1.5;font-size:15px}'
                . '.ticket{font-weight:700;color:#000}</style></head><body>'
                . '<div class="card"><h2>Ficha no disponible</h2>'
                . '<p>La orden <span class="ticket">' . e($order->numero_ticket) . '</span> aun esta en revision.'
                . ' El tecnico debe completar el diagnostico antes de generar la ficha tecnica.</p>'
                . '<p style="font-size:13px;color:#999">Si necesitas asistencia, contacta al tecnico asignado.</p></div></body></html>',
                200,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }

        $adminResponsable = User::role('Administrador')
            ->where('sucursal_id', $order->sucursal_id)
            ->first();

        $revision = $order->getRevision();
        $detalles = [];
        if ($revision) {
            $fields = $revision->getAttributes();
            unset($fields['id'], $fields['orden_reparacion_id'], $fields['created_at'], $fields['updated_at']);
            $detalles = $fields;
        }

        $pdf = Pdf::loadView('pdf.diagnostico', [
            'order' => $order,
            'admin' => $adminResponsable,
            'detalles' => $detalles,
        ]);

        return $pdf->download("diagnostico_{$order->numero_ticket}.pdf");
    }
}
