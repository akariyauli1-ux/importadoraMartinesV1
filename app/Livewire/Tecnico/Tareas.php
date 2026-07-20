<?php

namespace App\Livewire\Tecnico;

use Livewire\Component;
use App\Models\OrdenReparacion;
use App\Models\DetalleRevisionCelular;
use App\Models\DetalleRevisionLaptop;
use App\Models\DetalleRevisionElectrico;
use App\Models\DetalleRevisionCpu;
use App\Models\MensajeWhatsappPendiente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Tareas extends Component
{
    public $selectedOrderId = null;
    public $selectedOrderData = null;
    public $costo_estimado = 0.00;
    
    public $diagFields = [];
    public $observaciones = '';
    public $mostrarReporte = false;
    public $enviarWhatsappDiagnostico = true;

    public function selectOrden($id)
    {
        $this->selectedOrderId = $id;
        $this->mostrarReporte = false;
        $order = OrdenReparacion::findOrFail($id);
        $this->selectedOrderData = [
            'marca' => $order->marca,
            'modelo' => $order->modelo,
            'numero_ticket' => $order->numero_ticket,
            'problema_reportado' => $order->problema_reportado,
            'estado' => $order->estado,
            'categoria' => $order->categoria,
        ];
        $this->costo_estimado = $order->costo_estimado;
        $this->diagFields = [];
        $this->observaciones = '';

        $revision = $order->getRevision();
        if ($revision) {
            $this->observaciones = $revision->observaciones ?? '';
            
            if ($order->categoria === 'celular') {
                $this->diagFields = [
                    'tactil' => $revision->tactil ?? '',
                    'pantalla' => $revision->pantalla ?? '',
                    'camaras' => $revision->camaras ?? '',
                    'conector_carga' => $revision->conector_carga ?? '',
                    'bateria' => $revision->bateria ?? '',
                    'botones' => $revision->botones ?? '',
                    'senal' => $revision->senal ?? '',
                    'altavoz' => $revision->altavoz ?? '',
                ];
            } elseif ($order->categoria === 'laptop') {
                $this->diagFields = [
                    'teclado' => $revision->teclado ?? '',
                    'pantalla' => $revision->pantalla ?? '',
                    'cargador' => $revision->cargador ?? '',
                    'disco_duro' => $revision->disco_duro ?? '',
                    'memoria_ram' => $revision->memoria_ram ?? '',
                    'bisagras' => $revision->bisagras ?? '',
                    'wifi' => $revision->wifi ?? '',
                    'encendido' => $revision->encendido ?? '',
                ];
            } elseif ($order->categoria === 'electrico') {
                $this->diagFields = [
                    'fuente_alimentacion' => $revision->fuente_alimentacion ?? '',
                    'placa_principal' => $revision->placa_principal ?? '',
                    'salidas_audio' => $revision->salidas_audio ?? '',
                    'entradas_video' => $revision->entradas_video ?? '',
                    'control_remoto' => $revision->control_remoto ?? '',
                    'botones_fisicos' => $revision->botones_fisicos ?? '',
                ];
            } elseif ($order->categoria === 'cpu') {
                $this->diagFields = [
                    'fuente_poder' => $revision->fuente_poder ?? '',
                    'tarjeta_madre' => $revision->tarjeta_madre ?? '',
                    'procesador' => $revision->procesador ?? '',
                    'memoria_ram' => $revision->memoria_ram ?? '',
                    'disco_duro' => $revision->disco_duro ?? '',
                    'tarjeta_video' => $revision->tarjeta_video ?? '',
                    'puertos_usb' => $revision->puertos_usb ?? '',
                    'refrigeracion' => $revision->refrigeracion ?? '',
                ];
            }
        }
    }

    public function updated($property)
    {
        if (str_starts_with($property, 'diagFields.') || $property === 'observaciones' || $property === 'costo_estimado') {
            $this->mostrarReporte = false;
        }
    }

    public function generarReporte()
    {
        $this->mostrarReporte = true;
    }

    public function estadoLabel($estado)
    {
        return match ($estado) {
            'buen_estado' => 'Buen estado',
            'mal_estado' => 'Mal estado',
            'no_corresponde' => 'No corresponde',
            default => $estado ?: 'Sin evaluar',
        };
    }

    public function saveDiagnostico()
    {
        $order = OrdenReparacion::findOrFail($this->selectedOrderId);
        
        $rules = [
            'costo_estimado' => 'required|numeric|min:1',
            'observaciones' => 'required|string|min:5',
        ];

        foreach ($this->diagFields as $key => $val) {
            $rules["diagFields.{$key}"] = 'nullable|string';
        }

        $this->validate($rules, [
            'costo_estimado.min' => 'Debe ingresar un presupuesto estimado mayor a Bs. 0.',
        ]);

        DB::beginTransaction();
        try {
            $revision = $order->getRevision();
            $updateData = array_merge($this->diagFields, ['observaciones' => $this->observaciones]);
            $revision->update($updateData);

            $order->update([
                'costo_estimado' => $this->costo_estimado,
                'estado' => 'esperando_aprobacion',
            ]);

            $cliente = $order->cliente;
            if ($this->enviarWhatsappDiagnostico) {
                $waMessage = "Hola {$cliente->nombre}, tu equipo {$order->marca} {$order->modelo} ya tiene un diagnóstico y presupuesto estimado de Bs. {$this->costo_estimado}. " .
                    "Por motivos legales, requerimos tu firma de aprobación digital para poder iniciar la reparación. Aprueba el presupuesto aquí: " . 
                    route('cliente.orden', $order->numero_ticket);

                MensajeWhatsappPendiente::create([
                    'orden_reparacion_id' => $order->id,
                    'telefono' => $cliente->telefono,
                    'mensaje' => $waMessage,
                ]);
            }

            DB::commit();

            session()->flash('success', 'Diagnóstico y presupuesto guardados. Notificación encolada al cliente.');
            $this->selectedOrderId = null;
            $this->selectedOrderData = null;
            $this->mostrarReporte = false;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar el diagnóstico: ' . $e->getMessage());
        }
    }

    public function marcarReparado($id)
    {
        $order = OrdenReparacion::findOrFail($id);
        
        if ($order->estado !== 'en_reparacion') {
            session()->flash('error', 'Solo puede marcar como reparado un equipo que esté en proceso de reparación.');
            return;
        }

        $order->update(['estado' => 'reparado']);

        $cliente = $order->cliente;
        $waMessage = "Buenas noticias {$cliente->nombre}, tu equipo {$order->marca} {$order->modelo} (Ticket {$order->numero_ticket}) ya se encuentra REPARADO y listo para ser retirado. Puedes pasar por nuestra sucursal. Costo final: Bs. {$order->costo_estimado}.";
        
        MensajeWhatsappPendiente::create([
            'orden_reparacion_id' => $order->id,
            'telefono' => $cliente->telefono,
            'mensaje' => $waMessage,
        ]);

        session()->flash('success', "Orden {$order->numero_ticket} marcada como reparada y cliente notificado.");
    }

    public function render()
    {
        $tecnico = Auth::user();
        
        $misTareas = OrdenReparacion::where('tecnico_id', $tecnico->id)
            ->whereIn('estado', ['por_diagnosticar', 'diagnosticado', 'esperando_aprobacion', 'en_reparacion', 'reparado'])
            ->orderBy('estado', 'asc')
            ->get();

        $activasCount = $misTareas->filter(function($t) {
            return in_array($t->estado, ['por_diagnosticar', 'diagnosticado', 'esperando_aprobacion', 'en_reparacion']);
        })->count();

        return view('livewire.tecnico.tareas', [
            'tareas' => $misTareas,
            'activasCount' => $activasCount
        ])->layout('layouts.app');
    }
}
