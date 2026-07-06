<?php

namespace App\Livewire\Gerente;

use Livewire\Component;
use App\Models\OrdenReparacion;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Reportes extends Component
{
    public $filtroDias = 30;

    public function render()
    {
        $fechaDesde = now()->subDays($this->filtroDias);

        // === RENDIMIENTO POR SUCURSAL ===
        $sucursales = Sucursal::where('activa', true)->get();
        $rendimientoSucursales = [];

        foreach ($sucursales as $suc) {
            $total = OrdenReparacion::where('sucursal_id', $suc->id)
                ->where('created_at', '>=', $fechaDesde)->count();
            $entregadas = OrdenReparacion::where('sucursal_id', $suc->id)
                ->where('created_at', '>=', $fechaDesde)->where('estado', 'entregado')->count();
            $reparadas = OrdenReparacion::where('sucursal_id', $suc->id)
                ->where('created_at', '>=', $fechaDesde)->where('estado', 'reparado')->count();
            $enProgreso = OrdenReparacion::where('sucursal_id', $suc->id)
                ->whereIn('estado', ['por_diagnosticar', 'diagnosticado', 'esperando_aprobacion', 'en_reparacion'])->count();
            $presupuestado = OrdenReparacion::where('sucursal_id', $suc->id)
                ->where('created_at', '>=', $fechaDesde)->sum('costo_estimado');
            $tasaExito = $total > 0 ? round(($entregadas / $total) * 100, 1) : 0;
            $tecnicosCount = User::role('Técnico')->where('sucursal_id', $suc->id)->count();

            $rendimientoSucursales[] = [
                'id' => $suc->id,
                'nombre' => $suc->nombre,
                'direccion' => $suc->direccion,
                'total' => $total,
                'entregadas' => $entregadas,
                'reparadas' => $reparadas,
                'en_progreso' => $enProgreso,
                'presupuestado' => $presupuestado,
                'tasa_exito' => $tasaExito,
                'tecnicos' => $tecnicosCount,
            ];
        }

        usort($rendimientoSucursales, fn($a, $b) => $b['entregadas'] <=> $a['entregadas']);

        // === RENDIMIENTO POR TÉCNICO ===
        $tecnicos = User::role('Técnico')->with('sucursal')->get();
        $rendimientoTecnicos = [];

        foreach ($tecnicos as $t) {
            $asignadas = OrdenReparacion::where('tecnico_id', $t->id)
                ->where('created_at', '>=', $fechaDesde)->count();
            $completadas = OrdenReparacion::where('tecnico_id', $t->id)
                ->where('created_at', '>=', $fechaDesde)
                ->whereIn('estado', ['reparado', 'entregado'])->count();
            $entregadas = OrdenReparacion::where('tecnico_id', $t->id)
                ->where('created_at', '>=', $fechaDesde)->where('estado', 'entregado')->count();
            $enProgreso = OrdenReparacion::where('tecnico_id', $t->id)
                ->whereIn('estado', ['por_diagnosticar', 'diagnosticado', 'esperando_aprobacion', 'en_reparacion'])->count();
            $pendientesDiagnosticar = OrdenReparacion::where('tecnico_id', $t->id)
                ->where('estado', 'por_diagnosticar')->count();
            $presupuestado = OrdenReparacion::where('tecnico_id', $t->id)
                ->where('created_at', '>=', $fechaDesde)->sum('costo_estimado');
            $tasaExito = $asignadas > 0 ? round(($completadas / $asignadas) * 100, 1) : 0;

            $promedioDias = OrdenReparacion::where('tecnico_id', $t->id)
                ->whereIn('estado', ['reparado', 'entregado'])
                ->where('created_at', '>=', $fechaDesde)
                ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
                ->value('avg_days');

            $rendimientoTecnicos[] = [
                'id' => $t->id,
                'nombre' => $t->nombre_completo,
                'sucursal' => $t->sucursal->nombre ?? 'N/A',
                'asignadas' => $asignadas,
                'completadas' => $completadas,
                'entregadas' => $entregadas,
                'en_progreso' => $enProgreso,
                'pendientes_diag' => $pendientesDiagnosticar,
                'presupuestado' => $presupuestado,
                'tasa_exito' => $tasaExito,
                'promedio_dias' => $promedioDias ? round($promedioDias, 1) : null,
            ];
        }

        usort($rendimientoTecnicos, fn($a, $b) => $b['completadas'] <=> $a['completadas']);

        // === DATOS PARA GRÁFICOS ===
        $chartSucursalesLabels = [];
        $chartSucursalesEntregadas = [];
        $chartSucursalesEnProgreso = [];
        $chartSucursalesTotal = [];
        foreach ($rendimientoSucursales as $s) {
            $chartSucursalesLabels[] = $s['nombre'];
            $chartSucursalesEntregadas[] = $s['entregadas'];
            $chartSucursalesEnProgreso[] = $s['en_progreso'];
            $chartSucursalesTotal[] = $s['total'];
        }

        $chartTecnicosLabels = [];
        $chartTecnicosCompletadas = [];
        $chartTecnicosEnProgreso = [];
        foreach (array_slice($rendimientoTecnicos, 0, 15) as $t) {
            $chartTecnicosLabels[] = $t['nombre'];
            $chartTecnicosCompletadas[] = $t['completadas'];
            $chartTecnicosEnProgreso[] = $t['en_progreso'];
        }

        $chartSucursalTasaLabels = [];
        $chartSucursalTasaValores = [];
        foreach ($rendimientoSucursales as $s) {
            $chartSucursalTasaLabels[] = $s['nombre'];
            $chartSucursalTasaValores[] = $s['tasa_exito'];
        }

        return view('livewire.gerente.reportes', [
            'rendimientoSucursales' => $rendimientoSucursales,
            'rendimientoTecnicos' => $rendimientoTecnicos,
            'chartSucursalesLabels' => $chartSucursalesLabels,
            'chartSucursalesEntregadas' => $chartSucursalesEntregadas,
            'chartSucursalesEnProgreso' => $chartSucursalesEnProgreso,
            'chartSucursalesTotal' => $chartSucursalesTotal,
            'chartTecnicosLabels' => $chartTecnicosLabels,
            'chartTecnicosCompletadas' => $chartTecnicosCompletadas,
            'chartTecnicosEnProgreso' => $chartTecnicosEnProgreso,
            'chartSucursalTasaLabels' => $chartSucursalTasaLabels,
            'chartSucursalTasaValores' => $chartSucursalTasaValores,
            'filtroDias' => $this->filtroDias,
        ])->layout('layouts.app');
    }
}
