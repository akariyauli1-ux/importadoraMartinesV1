<?php

namespace App\Livewire\Almacen;

use Livewire\Component;
use App\Models\SolicitudRepuesto;
use Illuminate\Support\Facades\Auth;

class Solicitudes extends Component
{
    public $filtroEstado = 'todas';
    public $selectedSolicitudId = null;

    public $observaciones_respuesta = '';

    public function selectSolicitud($id)
    {
        $this->selectedSolicitudId = $id;
        $solicitud = SolicitudRepuesto::findOrFail($id);
        $this->observaciones_respuesta = $solicitud->observaciones_almacenista ?? '';
    }

    public function responder($estado)
    {
        if (!in_array($estado, ['enviado', 'agotado', 'no_existe'])) {
            return;
        }

        $solicitud = SolicitudRepuesto::findOrFail($this->selectedSolicitudId);
        $solicitud->update([
            'estado' => $estado,
            'almacenista_id' => Auth::id(),
            'observaciones_almacenista' => $this->observaciones_respuesta,
            'fecha_respuesta' => now(),
            'leido_por_solicitante' => false,
            'confirmado_recibido' => false,
        ]);

        session()->flash('success', 'Solicitud respondida correctamente.');
        $this->selectedSolicitudId = null;
        $this->observaciones_respuesta = '';
    }

    public function render()
    {
        $almacenista = Auth::user();
        $sucursalId = $almacenista->sucursal_id;

        $query = SolicitudRepuesto::with(['solicitante', 'almacenista', 'ordenReparacion'])
            ->where('sucursal_id', $sucursalId);

        if ($this->filtroEstado !== 'todas') {
            $query->where('estado', $this->filtroEstado);
        }

        $solicitudes = $query->orderBy('created_at', 'desc')->get();

        $enviados = SolicitudRepuesto::where('sucursal_id', $sucursalId)->where('estado', 'enviado')->count();
        $recibidos = SolicitudRepuesto::where('sucursal_id', $sucursalId)->where('estado', 'enviado')->where('confirmado_recibido', true)->count();
        $agotados = SolicitudRepuesto::where('sucursal_id', $sucursalId)->where('estado', 'agotado')->count();
        $noExisten = SolicitudRepuesto::where('sucursal_id', $sucursalId)->where('estado', 'no_existe')->count();
        $pendientes = SolicitudRepuesto::where('sucursal_id', $sucursalId)->where('estado', 'pendiente')->count();

        $topNoExisten = SolicitudRepuesto::selectRaw('nombre_repuesto, COUNT(*) as total')
            ->where('sucursal_id', $sucursalId)
            ->where('estado', 'no_existe')
            ->groupBy('nombre_repuesto')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $topEnviados = SolicitudRepuesto::selectRaw('nombre_repuesto, SUM(cantidad) as total')
            ->where('sucursal_id', $sucursalId)
            ->where('estado', 'enviado')
            ->groupBy('nombre_repuesto')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('livewire.almacen.solicitudes', [
            'solicitudes' => $solicitudes,
            'enviados' => $enviados,
            'recibidos' => $recibidos,
            'agotados' => $agotados,
            'noExisten' => $noExisten,
            'pendientes' => $pendientes,
            'topNoExisten' => $topNoExisten,
            'topEnviados' => $topEnviados,
        ])->layout('layouts.app');
    }
}
