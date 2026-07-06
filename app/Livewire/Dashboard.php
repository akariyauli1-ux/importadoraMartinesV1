<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OrdenReparacion;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Cliente;
use App\Models\SolicitudRepuesto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $stats = [];
        $charts = [];

        $statesList = ['por_diagnosticar', 'diagnosticado', 'esperando_aprobacion', 'en_reparacion', 'reparado', 'entregado', 'rechazado'];
        foreach($statesList as $state) {
            $stats['ordenes_por_estado'][$state] = 0;
        }

        if ($user->hasRole('Gerente')) {
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

            $charts['ordenes_por_sucursal'] = Sucursal::withCount('ordenes')->get()
                ->map(fn($s) => ['label' => $s->nombre, 'value' => $s->ordenes_count]);

            $charts['ordenes_por_categoria'] = OrdenReparacion::selectRaw('categoria, count(*) as total')
                ->groupBy('categoria')->get()
                ->map(fn($r) => ['label' => ucfirst($r->categoria), 'value' => $r->total]);

            $charts['categorias_labels'] = ['Celular', 'Laptop', 'Electrico', 'CPU'];
            $charts['categorias_entregados'] = [
                OrdenReparacion::where('categoria', 'celular')->where('estado', 'entregado')->count(),
                OrdenReparacion::where('categoria', 'laptop')->where('estado', 'entregado')->count(),
                OrdenReparacion::where('categoria', 'electrico')->where('estado', 'entregado')->count(),
                OrdenReparacion::where('categoria', 'cpu')->where('estado', 'entregado')->count(),
            ];
            $charts['categorias_reparando'] = [
                OrdenReparacion::where('categoria', 'celular')->whereIn('estado', ['en_reparacion', 'diagnosticado', 'esperando_aprobacion'])->count(),
                OrdenReparacion::where('categoria', 'laptop')->whereIn('estado', ['en_reparacion', 'diagnosticado', 'esperando_aprobacion'])->count(),
                OrdenReparacion::where('categoria', 'electrico')->whereIn('estado', ['en_reparacion', 'diagnosticado', 'esperando_aprobacion'])->count(),
                OrdenReparacion::where('categoria', 'cpu')->whereIn('estado', ['en_reparacion', 'diagnosticado', 'esperando_aprobacion'])->count(),
            ];

            $charts['ingresos_por_mes'] = $this->ingresosUltimosMeses();
        } else {
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

            $charts['categorias_labels'] = ['Celular', 'Laptop', 'Electrico', 'CPU'];
            $charts['categorias_total'] = [
                OrdenReparacion::where('sucursal_id', $sucursalId)->where('categoria', 'celular')->count(),
                OrdenReparacion::where('sucursal_id', $sucursalId)->where('categoria', 'laptop')->count(),
                OrdenReparacion::where('sucursal_id', $sucursalId)->where('categoria', 'electrico')->count(),
                OrdenReparacion::where('sucursal_id', $sucursalId)->where('categoria', 'cpu')->count(),
            ];
                
            if ($user->hasRole('Técnico')) {
                $stats['mis_activas'] = OrdenReparacion::where('tecnico_id', $user->id)
                    ->whereIn('estado', ['en_reparacion', 'diagnosticado', 'esperando_aprobacion'])
                    ->count();
                $stats['mis_reparadas'] = OrdenReparacion::where('tecnico_id', $user->id)
                    ->where('estado', 'reparado')
                    ->count();
                $stats['mis_entregadas'] = OrdenReparacion::where('tecnico_id', $user->id)
                    ->where('estado', 'entregado')
                    ->count();
                $stats['mis_pendientes'] = OrdenReparacion::where('tecnico_id', $user->id)
                    ->where('estado', 'por_diagnosticar')
                    ->count();

                $charts['tecnico_tareas_labels'] = ['Pendientes', 'Activas', 'Reparadas', 'Entregadas'];
                $charts['tecnico_tareas_valores'] = [
                    $stats['mis_pendientes'],
                    $stats['mis_activas'],
                    $stats['mis_reparadas'],
                    $stats['mis_entregadas'],
                ];
                $charts['tecnico_tareas_colores'] = ['#6c757d', '#ff9800', '#28a745', '#17a2b8'];

                $charts['tecnico_mensual'] = $this->tareasTecnicoPorMes($user->id);
            }

            if ($user->hasRole('Recepcionista')) {
                $stats['mis_ingresos_hoy'] = OrdenReparacion::where('recepcionista_id', $user->id)
                    ->whereDate('created_at', today())
                    ->count();

                $charts['recepcion_semanal'] = $this->ingresosRecepcionistaSemana($user->id);
            }

            if ($user->hasRole('Administrador')) {
                $charts['tecnicos_carga'] = $this->cargaPorTecnico($sucursalId);
            }

            if ($user->hasRole('Almacenista')) {
                $stats['solicitudes_enviadas'] = SolicitudRepuesto::where('sucursal_id', $sucursalId)->where('estado', 'enviado')->count();
                $stats['solicitudes_pendientes'] = SolicitudRepuesto::where('sucursal_id', $sucursalId)->where('estado', 'pendiente')->count();
                $stats['solicitudes_agotadas'] = SolicitudRepuesto::where('sucursal_id', $sucursalId)->where('estado', 'agotado')->count();
                $stats['solicitudes_no_existen'] = SolicitudRepuesto::where('sucursal_id', $sucursalId)->where('estado', 'no_existe')->count();

                $charts['solicitudes_labels'] = ['Enviados', 'Pendientes', 'Agotados', 'No existen'];
                $charts['solicitudes_valores'] = [
                    $stats['solicitudes_enviadas'],
                    $stats['solicitudes_pendientes'],
                    $stats['solicitudes_agotadas'],
                    $stats['solicitudes_no_existen'],
                ];
                $charts['solicitudes_colores'] = ['#28a745', '#ffc107', '#fd7e14', '#dc3545'];

                $charts['top_repuestos_labels'] = SolicitudRepuesto::selectRaw('nombre_repuesto, COUNT(*) as total')
                    ->where('sucursal_id', $sucursalId)
                    ->groupBy('nombre_repuesto')
                    ->orderByDesc('total')
                    ->limit(8)
                    ->get()
                    ->map(fn($r) => $r->nombre_repuesto);

                $charts['top_repuestos_valores'] = SolicitudRepuesto::selectRaw('nombre_repuesto, COUNT(*) as total')
                    ->where('sucursal_id', $sucursalId)
                    ->groupBy('nombre_repuesto')
                    ->orderByDesc('total')
                    ->limit(8)
                    ->get()
                    ->map(fn($r) => $r->total);
            }
        }

        $statesLabels = ['Por diagnosticar', 'Diagnosticado', 'Esperando aprob.', 'En reparación', 'Reparado', 'Entregado', 'Rechazado'];
        
        return view('livewire.dashboard', [
            'stats' => $stats,
            'charts' => $charts,
            'statesLabels' => $statesLabels,
            'statesValues' => array_values($stats['ordenes_por_estado']),
        ])->layout('layouts.app');
    }

    private function ingresosUltimosMeses()
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $total = OrdenReparacion::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $entregados = OrdenReparacion::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('estado', 'entregado')
                ->count();
            $data['labels'][] = $date->translatedFormat('M');
            $data['total'][] = $total;
            $data['entregados'][] = $entregados;
        }
        return $data;
    }

    private function tareasTecnicoPorMes($tecnicoId)
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $total = OrdenReparacion::where('tecnico_id', $tecnicoId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $completadas = OrdenReparacion::where('tecnico_id', $tecnicoId)
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->whereIn('estado', ['reparado', 'entregado'])
                ->count();
            $data['labels'][] = $date->translatedFormat('M');
            $data['total'][] = $total;
            $data['completadas'][] = $completadas;
        }
        return $data;
    }

    private function ingresosRecepcionistaSemana($recepcionistaId)
    {
        $labels = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $valores = [];
        $start = now()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            $valores[] = OrdenReparacion::where('recepcionista_id', $recepcionistaId)
                ->whereDate('created_at', $date)
                ->count();
        }
        return ['labels' => $labels, 'valores' => $valores];
    }

    private function cargaPorTecnico($sucursalId)
    {
        $tecnicos = User::role('Técnico')->where('sucursal_id', $sucursalId)->get();
        $result = ['labels' => [], 'activas' => [], 'completadas' => []];
        foreach ($tecnicos as $t) {
            $result['labels'][] = $t->name;
            $result['activas'][] = OrdenReparacion::where('tecnico_id', $t->id)
                ->whereIn('estado', ['por_diagnosticar', 'diagnosticado', 'esperando_aprobacion', 'en_reparacion'])
                ->count();
            $result['completadas'][] = OrdenReparacion::where('tecnico_id', $t->id)
                ->whereIn('estado', ['reparado', 'entregado'])
                ->count();
        }
        return $result;
    }
}
