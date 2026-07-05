<?php

namespace App\Livewire\Recepcion;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Cliente;
use App\Models\OrdenReparacion;
use App\Models\FotoOrden;
use App\Models\User;
use App\Models\MensajeWhatsappPendiente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Checkin extends Component
{
    use WithFileUploads;

    // Client Fields
    public $cliente_nombre = '';
    public $cliente_ci = '';
    public $cliente_telefono = '';
    public $cliente_email = '';
    public $cliente_direccion = '';
    
    // Order Fields
    public $categoria = 'celular'; // celular, laptop, electrico, cpu
    public $marca = '';
    public $modelo = '';
    public $serie = '';
    public $problema_reportado = '';
    
    // Evidence & Signature
    public $fotos_evidencia = [];
    public $firma_checkin = ''; // base64 string
    public $sin_fotos_motivo = false; // Waiver/rejection checkbox

    protected $rules = [
        'cliente_nombre' => 'required|string|max:150',
        'cliente_ci' => 'required|string|max:20',
        'cliente_telefono' => 'required|string|max:20',
        'cliente_email' => 'nullable|email|max:150',
        'cliente_direccion' => 'nullable|string|max:250',
        'categoria' => 'required|string|in:celular,laptop,electrico,cpu',
        'marca' => 'required|string|max:100',
        'modelo' => 'required|string|max:100',
        'serie' => 'nullable|string|max:100',
        'problema_reportado' => 'required|string',
        'fotos_evidencia.*' => 'nullable|image|max:2048',
    ];

    public function updatedClienteCi()
    {
        // Search if client already exists to auto-fill details
        $cliente = Cliente::where('carnet_identidad', $this->cliente_ci)->first();
        if ($cliente) {
            $this->cliente_nombre = $cliente->nombre;
            $this->cliente_telefono = $cliente->telefono;
            $this->cliente_email = $cliente->email ?? '';
            $this->cliente_direccion = $cliente->direccion ?? '';
            session()->flash('info_cliente', 'Cliente recurrente encontrado. Datos autocompletados.');
        }
    }

    public function save()
    {
        $this->validate();

        $recepcionist = Auth::user();
        if (!$recepcionist->sucursal_id) {
            session()->flash('error', 'Debe estar asignado a una sucursal para recibir equipos.');
            return;
        }

        // Rule #6: At least 1 photo or signed waiver/rejection
        if (empty($this->fotos_evidencia) && !$this->sin_fotos_motivo) {
            $this->addError('fotos_evidencia', 'Debe cargar al menos 1 foto del equipo, o marcar el rechazo firmado por el cliente.');
            return;
        }

        // Must have signature
        if (empty($this->firma_checkin)) {
            $this->addError('firma_checkin', 'La firma digital del cliente es obligatoria para el Check-in (Regla #3).');
            return;
        }

        DB::beginTransaction();
        try {
            // 1. Create/Update Client
            $cliente = Cliente::updateOrCreate(
                ['carnet_identidad' => $this->cliente_ci],
                [
                    'nombre' => $this->cliente_nombre,
                    'telefono' => $this->cliente_telefono,
                    'email' => $this->cliente_email ?: null,
                    'direccion' => $this->cliente_direccion ?: null,
                ]
            );

            // 2. Ticket ID Generation (IM-YYYYMMDD-RAND)
            $ticketNumber = 'IM-' . now()->format('Ymd') . '-' . rand(1000, 9999);

            // 3. Smart Technician Assignment Algorithm (Least active orders count < 4 in local branch)
            $localTechnicians = User::role('Técnico')
                ->where('sucursal_id', $recepcionist->sucursal_id)
                ->get()
                ->map(function ($tec) {
                    $tec->active_count = OrdenReparacion::where('tecnico_id', $tec->id)
                        ->whereIn('estado', ['por_diagnosticar', 'diagnosticado', 'esperando_aprobacion', 'en_reparacion'])
                        ->count();
                    return $tec;
                })
                ->filter(function ($tec) {
                    return $tec->active_count < 4; // Rule #1 limit
                })
                ->sortBy('active_count'); // Get the one with least load

            $assignedTechnicianId = $localTechnicians->first() ? $localTechnicians->first()->id : null;

            // 4. Create Order
            $orden = OrdenReparacion::create([
                'numero_ticket' => $ticketNumber,
                'cliente_id' => $cliente->id,
                'sucursal_id' => $recepcionist->sucursal_id,
                'categoria' => $this->categoria,
                'marca' => $this->marca,
                'modelo' => $this->modelo,
                'serie' => $this->serie ?: null,
                'problema_reportado' => $this->problema_reportado,
                'tecnico_id' => $assignedTechnicianId,
                'recepcionista_id' => $recepcionist->id,
                'estado' => 'por_diagnosticar',
                'firma_checkin_base64' => $this->firma_checkin,
            ]);

            // 5. Initialize diagnostic row depending on category
            $diagData = ['orden_reparacion_id' => $orden->id];
            switch ($this->categoria) {
                case 'celular':
                    DB::table('detalle_revision_celular')->insert($diagData);
                    break;
                case 'laptop':
                    DB::table('detalle_revision_laptop')->insert($diagData);
                    break;
                case 'electrico':
                    DB::table('detalle_revision_electrico')->insert($diagData);
                    break;
                case 'cpu':
                    DB::table('detalle_revision_cpu')->insert($diagData);
                    break;
            }

            // 6. Save Evidence Photos
            if (!empty($this->fotos_evidencia)) {
                foreach ($this->fotos_evidencia as $photo) {
                    $path = $photo->store('equipos', 'public');
                    FotoOrden::create([
                        'orden_reparacion_id' => $orden->id,
                        'foto_path' => $path,
                        'tipo' => 'checkin',
                    ]);
                }
            }

            // 7. Enqueue WhatsApp notification
            $waMessage = "Hola {$cliente->nombre}, tu equipo {$this->marca} {$this->modelo} ha sido recibido en IMPORTADORA MARTINEZ. " .
                "Número de ticket: {$ticketNumber}. Puedes seguir el estado y aprobar el presupuesto en tiempo real aquí: " . 
                route('cliente.orden', $ticketNumber);
            
            MensajeWhatsappPendiente::create([
                'orden_reparacion_id' => $orden->id,
                'telefono' => $cliente->telefono,
                'mensaje' => $waMessage,
            ]);

            DB::commit();

            session()->flash('success', "Orden {$ticketNumber} registrada con éxito. Notificación de WhatsApp encolada.");
            $this->resetForm();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar la orden: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->cliente_nombre = '';
        $this->cliente_ci = '';
        $this->cliente_telefono = '';
        $this->cliente_email = '';
        $this->cliente_direccion = '';
        $this->categoria = 'celular';
        $this->marca = '';
        $this->modelo = '';
        $this->serie = '';
        $this->problema_reportado = '';
        $this->fotos_evidencia = [];
        $this->firma_checkin = '';
        $this->sin_fotos_motivo = false;
        
        $this->dispatch('clear-sig-pad'); // Dispatch event to clear frontend canvas
    }

    public function render()
    {
        return view('livewire.recepcion.checkin')
            ->layout('layouts.app');
    }
}
