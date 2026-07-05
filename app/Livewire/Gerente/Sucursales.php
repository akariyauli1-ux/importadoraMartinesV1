<?php

namespace App\Livewire\Gerente;

use Livewire\Component;
use App\Models\Sucursal;

class Sucursales extends Component
{
    public $nombre = '';
    public $direccion = '';
    public $telefono = '';
    public $activa = true;
    public $sucursalId = null;
    public $isEdit = false;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'direccion' => 'required|string|max:255',
        'telefono' => 'required|string|max:50',
        'activa' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        if ($this->isEdit) {
            $sucursal = Sucursal::find($this->sucursalId);
            $sucursal->update([
                'nombre' => $this->nombre,
                'direccion' => $this->direccion,
                'telefono' => $this->telefono,
                'activa' => $this->activa,
            ]);
            session()->flash('success', 'Sucursal actualizada con éxito.');
        } else {
            Sucursal::create([
                'nombre' => $this->nombre,
                'direccion' => $this->direccion,
                'telefono' => $this->telefono,
                'activa' => $this->activa,
            ]);
            session()->flash('success', 'Sucursal creada con éxito.');
        }

        $this->resetInputFields();
    }

    public function edit($id)
    {
        $sucursal = Sucursal::findOrFail($id);
        $this->sucursalId = $sucursal->id;
        $this->nombre = $sucursal->nombre;
        $this->direccion = $sucursal->direccion;
        $this->telefono = $sucursal->telefono;
        $this->activa = $sucursal->activa;
        $this->isEdit = true;
    }

    public function toggleStatus($id)
    {
        $sucursal = Sucursal::findOrFail($id);
        $sucursal->activa = !$sucursal->activa;
        $sucursal->save();
        session()->flash('success', 'Estado de la sucursal actualizado.');
    }

    public function resetInputFields()
    {
        $this->nombre = '';
        $this->direccion = '';
        $this->telefono = '';
        $this->activa = true;
        $this->sucursalId = null;
        $this->isEdit = false;
    }

    public function render()
    {
        return view('livewire.gerente.sucursales', [
            'sucursales' => Sucursal::all()
        ])->layout('layouts.app');
    }
}
