<?php

namespace App\Livewire\Gerente;

use Livewire\Component;
use App\Models\OrdenReparacion;
use App\Models\User;

class Seguimiento extends Component
{
    public $filtroDias = 30;
    public $tecnicoExpandido = null;

    public function toggleTecnico($id)
    {
        $this->tecnicoExpandido = $this->tecnicoExpandido == $id ? null : $id;
    }

    public function render()
    {
        $fechaDesde = now()->subDays($this->filtroDias);
        $umbralRetraso = 7;
        $fechaRetraso = now()->subDays($umbralRetraso);

        $estadosActivos = ['por_diagnosticar', 'diagnosticado', 'esperando_aprobacion', 'en_reparacion', 'reparado'];
        $estadosNoEntregados = array_merge($estadosActivos, []);

        $totalActivas = OrdenReparacion::whereIn('estado', $estadosActivos)->count();
        $totalRetrasadas = OrdenReparacion::whereIn('estado', $estadosActivos)
            ->where('created_at', '<', $fechaRetraso)
            ->count();
        $totalEntregadas = OrdenReparacion::where('estado', 'entregado')
            ->where('updated_at', '>=', $fechaDesde)
            ->count();

        $tecnicos = User::role('Técnico')->with('sucursal')->get();

        $seguimientoTecnicos = [];
        foreach ($tecnicos as $t) {
            $activas = OrdenReparacion::where('tecnico_id', $t->id)
                ->whereIn('estado', $estadosActivos)
                ->with('cliente')
                ->orderBy('created_at')
                ->get()
                ->map(function ($orden) use ($umbralRetraso) {
                    $dias = (int) $orden->created_at->diffInDays(now());
                    $orden->dias_transcurridos = $dias;
                    $orden->es_retrasado = $dias > $umbralRetraso;
                    return $orden;
                });

            $retrasadasCount = $activas->where('es_retrasado', true)->count();

            $entregadas = OrdenReparacion::where('tecnico_id', $t->id)
                ->where('estado', 'entregado')
                ->where('updated_at', '>=', $fechaDesde)
                ->with('cliente')
                ->orderByDesc('updated_at')
                ->get()
                ->map(function ($orden) {
                    $orden->dias_hasta_entrega = (int) $orden->created_at->diffInDays($orden->updated_at);
                    return $orden;
                });

            $seguimientoTecnicos[] = [
                'tecnico' => $t,
                'activas' => $activas,
                'activas_count' => $activas->count(),
                'retrasadas_count' => $retrasadasCount,
                'entregadas' => $entregadas,
                'entregadas_count' => $entregadas->count(),
            ];
        }

        $retrasadasGlobal = OrdenReparacion::whereIn('estado', $estadosActivos)
            ->where('created_at', '<', $fechaRetraso)
            ->with('cliente', 'tecnico', 'sucursal')
            ->orderBy('created_at')
            ->get()
            ->map(function ($orden) {
                $orden->dias_transcurridos = (int) $orden->created_at->diffInDays(now());
                return $orden;
            });

        $entregadasRecientes = OrdenReparacion::where('estado', 'entregado')
            ->where('updated_at', '>=', $fechaDesde)
            ->with('cliente', 'tecnico', 'sucursal')
            ->orderByDesc('updated_at')
            ->limit(50)
            ->get()
            ->map(function ($orden) {
                $orden->dias_hasta_entrega = (int) $orden->created_at->diffInDays($orden->updated_at);
                return $orden;
            });

        return view('livewire.gerente.seguimiento', [
            'totalActivas' => $totalActivas,
            'totalRetrasadas' => $totalRetrasadas,
            'totalEntregadas' => $totalEntregadas,
            'umbralRetraso' => $umbralRetraso,
            'seguimientoTecnicos' => $seguimientoTecnicos,
            'retrasadasGlobal' => $retrasadasGlobal,
            'entregadasRecientes' => $entregadasRecientes,
        ])->layout('layouts.app');
    }
}
