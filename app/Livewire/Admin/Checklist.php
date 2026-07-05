<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ChecklistOperativoDiario;
use Illuminate\Support\Facades\Auth;

class Checklist extends Component
{
    public $uniformes_completos = false;
    public $limpieza_puestos = false;
    public $herramientas_ordenadas = false;
    public $observaciones = '';
    
    public $checklistDeHoy = null;

    public function mount()
    {
        $user = Auth::user();
        if (!$user->sucursal_id) {
            return;
        }

        // Find today's checklist
        $this->checklistDeHoy = ChecklistOperativoDiario::where('sucursal_id', $user->sucursal_id)
            ->whereDate('fecha', today())
            ->first();

        if ($this->checklistDeHoy) {
            $this->uniformes_completos = $this->checklistDeHoy->uniformes_completos;
            $this->limpieza_puestos = $this->checklistDeHoy->limpieza_puestos;
            $this->herramientas_ordenadas = $this->checklistDeHoy->herramientas_ordenadas;
            $this->observaciones = $this->checklistDeHoy->observaciones;
        }
    }

    public function save()
    {
        $user = Auth::user();
        if (!$user->sucursal_id) {
            session()->flash('error', 'Debe estar asignado a una sucursal para registrar un checklist.');
            return;
        }

        if ($this->checklistDeHoy) {
            session()->flash('error', 'El checklist operativo de hoy ya fue registrado.');
            return;
        }

        $this->checklistDeHoy = ChecklistOperativoDiario::create([
            'sucursal_id' => $user->sucursal_id,
            'fecha' => today(),
            'uniformes_completos' => $this->uniformes_completos,
            'limpieza_puestos' => $this->limpieza_puestos,
            'herramientas_ordenadas' => $this->herramientas_ordenadas,
            'observaciones' => $this->observaciones,
            'registrado_por' => $user->id,
        ]);

        session()->flash('success', 'Checklist operativo diario registrado con éxito.');
    }

    public function render()
    {
        return view('livewire.admin.checklist')
            ->layout('layouts.app');
    }
}
