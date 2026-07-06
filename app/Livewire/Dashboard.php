<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OrdenReparacion;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Cliente;
use App\Models\SolicitudRepuesto;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $stats = [];

        // Common default counts for state array to prevent index errors in view
        $statesList = ['por_diagnosticar', 'diagnosticado', 'esperando_aprobacion', 'en_reparacion', 'reparado', 'entregado', 'rechazado'];
        foreach($statesList as $state) {
            $stats['ordenes_por_estado'][$state] = 0;
        }

        if ($user->hasRole('Gerente')) {
            // Global Metrics for Manager
            $stats['sucursales_count'] = Sucursal::count();
            $stats['empleados_count'] = User::count();
            $stats['clientes_count'] = Cliente::count();
            $stats['ordenes_totales'] = OrdenReparacion::count();
            
            $dbStates = OrdenReparacion::selectRaw('estado, count(*) as count')
                ->groupBy('estado')
                ->pluck('count', 'estado')
                ->toArray();
            
            foreach ($dbStates as $state => $count) {
                $stats['ordenes_por_estado'][$state] = $count;
            }
                
            $stats['presupuestos_estimados'] = OrdenReparacion::sum('costo_estimado');
            $stats['ordenes_entregadas_totales'] = OrdenReparacion::where('estado', 'entregado')->count();
        } else {
            // Branch Metrics for Branch Admins, Technicians, Receptionists
            $sucursalId = $user->sucursal_id;
            
            $stats['ordenes_totales'] = OrdenReparacion::where('sucursal_id', $sucursalId)->count();
            
            $dbStates = OrdenReparacion::where('sucursal_id', $sucursalId)
                ->selectRaw('estado, count(*) as count')
                ->groupBy('estado')
                ->pluck('count', 'estado')
                ->toArray();
                
            foreach ($dbStates as $state => $count) {
                $stats['ordenes_por_estado'][$state] = $count;
            }
                
            if ($user->hasRole('Técnico')) {
                // Technician specific load
                $stats['mis_activas'] = OrdenReparacion::where('tecnico_id', $user->id)
                    ->whereIn('estado', ['en_reparacion', 'diagnosticado'])
                    ->count();
                $stats['mis_reparadas'] = OrdenReparacion::where('tecnico_id', $user->id)
                    ->where('estado', 'reparado')
                    ->count();
            }

            if ($user->hasRole('Recepcionista')) {
                $stats['mis_ingresos_hoy'] = OrdenReparacion::where('recepcionista_id', $user->id)
                    ->whereDate('created_at', today())
                    ->count();
            }

            if ($user->hasRole('Almacenista')) {
                $stats['solicitudes_enviadas'] = SolicitudRepuesto::where('sucursal_id', $sucursalId)->where('estado', 'enviado')->count();
                $stats['solicitudes_pendientes'] = SolicitudRepuesto::where('sucursal_id', $sucursalId)->where('estado', 'pendiente')->count();
                $stats['solicitudes_agotadas'] = SolicitudRepuesto::where('sucursal_id', $sucursalId)->where('estado', 'agotado')->count();
                $stats['solicitudes_no_existen'] = SolicitudRepuesto::where('sucursal_id', $sucursalId)->where('estado', 'no_existe')->count();
            }
        }

        return view('livewire.dashboard', [
            'stats' => $stats
        ])->layout('layouts.app');
    }
}
