<?php

namespace App\Livewire\Recepcion;

use Livewire\Component;
use App\Models\OrdenReparacion;
use App\Models\MensajeWhatsappPendiente;
use Illuminate\Support\Facades\Auth;

class Ordenes extends Component
{
    public $search = '';
    public $statusFilter = '';
    
    // Check-out fields
    public $selectedOrderId = null;
    public $firma_checkout = '';
    public $mostrarModalCheckout = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function abrirCheckout($orderId)
    {
        $this->selectedOrderId = $orderId;
        $this->firma_checkout = '';
        $this->mostrarModalCheckout = true;
    }

    public function entregar()
    {
        $this->validate([
            'firma_checkout' => 'required|string',
        ]);

        $order = OrdenReparacion::findOrFail($this->selectedOrderId);
        
        // Rule #3 check
        if (empty($this->firma_checkout)) {
            $this->addError('firma_checkout', 'La firma digital del cliente es requerida para retirar el equipo.');
            return;
        }

        $order->update([
            'estado' => 'entregado',
            'firma_checkout_base64' => $this->firma_checkout,
        ]);

        // WhatsApp notification
        $cliente = $order->cliente;
        $waMessage = "Estimado/a {$cliente->nombre}, su equipo {$order->marca} {$order->modelo} (Ticket {$order->numero_ticket}) ha sido entregado de conformidad. ¡Gracias por confiar en IMPORTADORA MARTINEZ!";
        
        MensajeWhatsappPendiente::create([
            'orden_reparacion_id' => $order->id,
            'telefono' => $cliente->telefono,
            'mensaje' => $waMessage,
        ]);

        session()->flash('success', "Orden {$order->numero_ticket} entregada con éxito.");
        $this->cerrarCheckout();
    }

    public function cerrarCheckout()
    {
        $this->selectedOrderId = null;
        $this->firma_checkout = '';
        $this->mostrarModalCheckout = false;
        $this->dispatch('clear-checkout-sig-pad');
    }

    public function render()
    {
        $user = Auth::user();
        if (!$user->sucursal_id) {
            return view('livewire.recepcion.ordenes', [
                'ordenes' => collect()
            ])->layout('layouts.app');
        }

        $ordenes = OrdenReparacion::with(['cliente', 'tecnico'])
            ->where('sucursal_id', $user->sucursal_id)
            ->when($this->statusFilter, function($query) {
                return $query->where('estado', $this->statusFilter);
            })
            ->when($this->search, function($query) {
                return $query->where(function($q) {
                    $q->where('numero_ticket', 'like', '%' . $this->search . '%')
                      ->orWhereHas('cliente', function($c) {
                          $c->where('nombre', 'like', '%' . $this->search . '%')
                            ->orWhere('carnet_identidad', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.recepcion.ordenes', [
            'ordenes' => $ordenes
        ])->layout('layouts.app');
    }
}
