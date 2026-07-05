<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenReparacion;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PdfController extends Controller
{
    /**
     * Generate and stream the repair order diagnostic PDF.
     */
    public function download($numero_ticket)
    {
        // Rule #8: Only generate PDF if diagnosis is complete (esperando_aprobacion or later states)
        $order = OrdenReparacion::with(['cliente', 'sucursal'])
            ->where('numero_ticket', $numero_ticket)
            ->firstOrFail();

        $incompleteStates = ['por_diagnosticar'];
        if (in_array($order->estado, $incompleteStates)) {
            abort(403, 'El PDF de diagnóstico no se puede generar hasta que el técnico complete la revisión inicial.');
        }

        // Rule #10: Hide the technician name, show the branch Administrator as responsible.
        $adminResponsable = User::role('Administrador')
            ->where('sucursal_id', $order->sucursal_id)
            ->first();

        // Generate QR code as base64 PNG image
        // To be 100% safe in PDF renders, we generate PNG QR
        $urlTracking = route('cliente.orden', $order->numero_ticket);
        
        // Generate QR code binary PNG
        $qrPng = QrCode::format('png')
            ->size(120)
            ->margin(1)
            ->generate($urlTracking);
            
        $qrBase64 = base64_encode($qrPng);

        // Fetch diagnostic details
        $revision = $order->getRevision();
        $detalles = [];
        if ($revision) {
            $fields = $revision->getAttributes();
            // Filter out system attributes
            unset($fields['id'], $fields['orden_reparacion_id'], $fields['created_at'], $fields['updated_at']);
            $detalles = $fields;
        }

        // Render PDF
        $pdf = Pdf::loadView('pdf.diagnostico', [
            'order' => $order,
            'admin' => $adminResponsable,
            'qrCode' => $qrBase64,
            'detalles' => $detalles,
        ]);

        return $pdf->stream("diagnostico_{$order->numero_ticket}.pdf");
    }
}
