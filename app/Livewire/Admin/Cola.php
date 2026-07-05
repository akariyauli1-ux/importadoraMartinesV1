<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\OrdenReparacion;
use App\Models\User;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;

class Cola extends Component
{
    public $selectedOrderId = null;
    public $tecnicoId = '';
    public $motivoDerivacion = '';
    public $mostrarModalAsignacion = false;

    public function mount()
    {
        // Check if admin is assigned to a branch
        if (!Auth::user()->sucursal_id) {
            session()->flash('error', 'Debe estar asignado a una sucursal para supervisar la cola de trabajo.');
        }
    }

    public function abrirAsignacion($orderId)
    {
        $this->selectedOrderId = $orderId;
        $order = OrdenReparacion::findOrFail($orderId);
        $this->tecnicoId = $order->tecnico_id ?? '';
        $this->motivoDerivacion = $order->motivo_derivacion ?? '';
        $this->mostrarModalAsignacion = true;
    }

    public function asignar()
    {
        $this->validate([
            'tecnicoId' => 'required|exists:users,id',
            'motivoDerivacion' => 'nullable|string|max:500',
        ]);

        $order = OrdenReparacion::findOrFail($this->selectedOrderId);
        $tecnico = User::findOrFail($this->tecnicoId);

        // Rule #1: Technician cannot have more than 4 active orders
        // Active states: por_diagnosticar (when assigned/working), diagnosticado, esperando_aprobacion, en_reparacion
        $activeCount = OrdenReparacion::where('tecnico_id', $tecnico->id)
            ->whereIn('estado', ['por_diagnosticar', 'diagnosticado', 'esperando_aprobacion', 'en_reparacion'])
            ->where('id', '!=', $order->id)
            ->count();

        if ($activeCount >= 4) {
            session()->flash('error', 'El técnico ' . $tecnico->nombre_completo . ' ya tiene ' . $activeCount . ' órdenes activas (Tope máximo de 4).');
            return;
        }

        // Rule #2: Check if cross-branch assignment (Cross-Sede)
        $esCruzada = $tecnico->sucursal_id !== Auth::user()->sucursal_id;
        
        if ($esCruzada && empty(trim($this->motivoDerivacion))) {
            $this->addError('motivoDerivacion', 'Debe especificar el motivo de derivación externa (Cross-Sede).');
            return;
        }

        $order->update([
            'tecnico_id' => $tecnico->id,
            'es_asignacion_cruzada' => $esCruzada,
            'motivo_derivacion' => $esCruzada ? $this->motivoDerivacion : null,
            // If it was in waiting state, we reset it appropriately
            'estado' => $order->estado === 'por_diagnosticar' ? 'por_diagnosticar' : $order->estado,
        ]);

        session()->flash('success', 'Orden asignada con éxito al técnico ' . $tecnico->nombre_completo . '.');
        $this->cerrarAsignacion();
    }

    public function cerrarAsignacion()
    {
        $this->selectedOrderId = null;
        $this->tecnicoId = '';
        $this->motivoDerivacion = '';
        $this->mostrarModalAsignacion = false;
    }

    public function render()
    {
        $admin = Auth::user();
        
        // Orders in the admin's branch or orders assigned cross-branch to technicians of this branch
        $tecnicoIdsLocal = User::role('Técnico')->where('sucursal_id', $admin->sucursal_id)->pluck('id');
        
        $ordenes = OrdenReparacion::with(['cliente', 'tecnico', 'sucursal'])
            ->where(function($query) use ($admin, $tecnicoIdsLocal) {
                $query->where('sucursal_id', $admin->sucursal_id)
                      ->orWhereIn('tecnico_id', $tecnicoIdsLocal);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Get technicians for assignment (Local and other branches)
        // Only active branch technicians can be assigned
        $tecnicos = User::role('Técnico')
            ->with(['sucursal', 'ordenesTecnico' => function($q) {
                $q->whereIn('estado', ['por_diagnosticar', 'diagnosticado', 'esperando_aprobacion', 'en_reparacion']);
            }])
            ->get()
            ->map(function($tec) {
                $tec->active_orders_count = $tec->ordenesTecnico->count();
                return $tec;
            });

        return view('livewire.admin.cola', [
            'ordenes' => $ordenes,
            'tecnicos' => $tecnicos
        ])->layout('layouts.app');
    }
}
