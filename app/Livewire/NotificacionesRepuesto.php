<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SolicitudRepuesto;
use Illuminate\Support\Facades\Auth;

class NotificacionesRepuesto extends Component
{
    public $abierto = false;

    public function toggle()
    {
        $this->abierto = !$this->abierto;
    }

    public function cerrar()
    {
        $this->abierto = false;
    }

    public function marcarLeido($id)
    {
        $solicitud = SolicitudRepuesto::where('solicitante_id', Auth::id())->findOrFail($id);
        $solicitud->update(['leido_por_solicitante' => true]);
    }

    public function confirmarRecepcion($id)
    {
        $solicitud = SolicitudRepuesto::where('solicitante_id', Auth::id())
            ->where('estado', 'enviado')
            ->findOrFail($id);

        $solicitud->update([
            'confirmado_recibido' => true,
            'fecha_confirmacion' => now(),
            'leido_por_solicitante' => true,
        ]);

        session()->flash('success', 'Recepción de repuesto "' . $solicitud->nombre_repuesto . '" confirmada.');
    }

    public function render()
    {
        $user = Auth::user();

        $notificaciones = SolicitudRepuesto::with('almacenista')
            ->where('solicitante_id', $user->id)
            ->whereIn('estado', ['enviado', 'agotado', 'no_existe'])
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        $noLeidas = $notificaciones->where('leido_por_solicitante', false)->count();

        return view('livewire.notificaciones-repuesto', [
            'notificaciones' => $notificaciones,
            'noLeidas' => $noLeidas,
        ]);
    }
}
