<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SolicitudRepuesto;
use Illuminate\Support\Facades\Auth;

class SolicitarRepuesto extends Component
{
    public $mostrarModal = false;
    public $orden_reparacion_id = null;
    public $nombre_repuesto = '';
    public $cantidad = 1;
    public $urgencia = 'media';
    public $observaciones_solicitante = '';

    public function abrirModal($ordenId = null)
    {
        $this->mostrarModal = true;
        $this->orden_reparacion_id = $ordenId;
        $this->reset(['nombre_repuesto', 'cantidad', 'urgencia', 'observaciones_solicitante']);
        $this->cantidad = 1;
        $this->urgencia = 'media';
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
    }

    public function solicitar()
    {
        $this->validate([
            'nombre_repuesto' => 'required|string|min:3|max:255',
            'cantidad' => 'required|integer|min:1|max:999',
            'urgencia' => 'required|in:baja,media,alta',
            'observaciones_solicitante' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        SolicitudRepuesto::create([
            'sucursal_id' => $user->sucursal_id,
            'solicitante_id' => $user->id,
            'orden_reparacion_id' => $this->orden_reparacion_id,
            'nombre_repuesto' => $this->nombre_repuesto,
            'cantidad' => $this->cantidad,
            'urgencia' => $this->urgencia,
            'estado' => 'pendiente',
            'observaciones_solicitante' => $this->observaciones_solicitante,
        ]);

        session()->flash('success', 'Solicitud de repuesto enviada al almacén correctamente.');
        $this->mostrarModal = false;
        $this->reset(['nombre_repuesto', 'cantidad', 'urgencia', 'observaciones_solicitante', 'orden_reparacion_id']);
        $this->cantidad = 1;
        $this->urgencia = 'media';
    }

    public function render()
    {
        return view('livewire.solicitar-repuesto');
    }
}
