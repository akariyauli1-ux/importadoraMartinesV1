<?php

namespace App\Livewire\Cliente;

use Livewire\Component;
use App\Models\OrdenReparacion;
use App\Models\MensajeWhatsappPendiente;
use Illuminate\Support\Facades\DB;

class Orden extends Component
{
    public $numero_ticket;
    public $firma_aprobacion = '';

    public function mount($numero_ticket)
    {
        $this->numero_ticket = $numero_ticket;
    }

    public function aprobarPresupuesto()
    {
        $this->validate([
            'firma_aprobacion' => 'required|string',
        ]);

        $order = OrdenReparacion::where('numero_ticket', $this->numero_ticket)->firstOrFail();

        if ($order->estado !== 'esperando_aprobacion') {
            session()->flash('error', 'Esta orden no está en espera de aprobación de presupuesto.');
            return;
        }

        if (empty($this->firma_aprobacion)) {
            $this->addError('firma_aprobacion', 'Su firma digital es necesaria para autorizar la reparación del equipo.');
            return;
        }

        DB::beginTransaction();
        try {
            $order->update([
                'estado' => 'en_reparacion',
                'firma_aprobacion_presupuesto_base64' => $this->firma_aprobacion,
            ]);

            // Notify workshop and queue client WhatsApp acknowledgment
            $cliente = $order->cliente;
            $waMessage = "Gracias {$cliente->nombre}, has aprobado el presupuesto de Bs. {$order->costo_estimado} para tu equipo {$order->marca} {$order->modelo} (Ticket {$order->numero_ticket}). Hemos iniciado la reparación activa.";

            MensajeWhatsappPendiente::create([
                'orden_reparacion_id' => $order->id,
                'telefono' => $cliente->telefono,
                'mensaje' => $waMessage,
            ]);

            DB::commit();

            session()->flash('success', 'Presupuesto aprobado con éxito. El taller ha sido notificado para iniciar la reparación.');
            $this->firma_aprobacion = '';

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Ocurrió un error al autorizar la orden: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $order = OrdenReparacion::with(['cliente', 'sucursal', 'tecnico'])
            ->where('numero_ticket', $this->numero_ticket)
            ->firstOrFail();

        $empConfig = \App\Models\ConfiguracionEmpresa::first();

        return view('livewire.cliente.orden', [
            'order' => $order,
            'empConfig' => $empConfig
        ])->layout('layouts.auth'); // Using layouts.auth since it's a public simple full-width layout
    }
}
