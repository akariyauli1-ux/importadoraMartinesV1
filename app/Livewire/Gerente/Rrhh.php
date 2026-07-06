<?php

namespace App\Livewire\Gerente;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Sucursal;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class Rrhh extends Component
{
    use WithFileUploads;

    public $name = '';
    public $apellido_paterno = '';
    public $apellido_materno = '';
    public $carnet_identidad = '';
    public $email = '';
    public $sucursal_id = '';
    public $rol = '';
    public $foto;
    public $currentFotoPath = null;
    
    public $empleadoId = null;
    public $isEdit = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'carnet_identidad' => 'required|string|max:20|unique:users,carnet_identidad,' . $this->empleadoId,
            'email' => 'nullable|email|max:150|unique:users,email,' . $this->empleadoId,
            'sucursal_id' => $this->rol !== 'Gerente' ? 'required|exists:sucursales,id' : 'nullable',
            'rol' => 'required|string|in:Gerente,Administrador,Técnico,Recepcionista,Almacenista',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // JPG/PNG and <2MB
        ];
    }

    public function save()
    {
        $this->validate();

        // Rule #9: Capacity validation (1-10 technicians, 1-2 receptionists)
        if ($this->rol !== 'Gerente') {
            if ($this->rol === 'Técnico') {
                $tecnicosCount = User::role('Técnico')
                    ->where('sucursal_id', $this->sucursal_id)
                    ->when($this->isEdit, function ($query) {
                        return $query->where('id', '!=', $this->empleadoId);
                    })->count();
                if ($tecnicosCount >= 10) {
                    $this->addError('sucursal_id', 'Límite alcanzado: Esta sucursal ya tiene 10 técnicos registrados.');
                    return;
                }
            }

            if ($this->rol === 'Recepcionista') {
                $recepcionistasCount = User::role('Recepcionista')
                    ->where('sucursal_id', $this->sucursal_id)
                    ->when($this->isEdit, function ($query) {
                        return $query->where('id', '!=', $this->empleadoId);
                    })->count();
                if ($recepcionistasCount >= 2) {
                    $this->addError('sucursal_id', 'Límite alcanzado: Esta sucursal ya tiene 2 recepcionistas registradas.');
                    return;
                }
            }
        }

        // Handle profile photo upload
        $fotoPath = $this->currentFotoPath;
        if ($this->foto) {
            // Delete old photo if editing
            if ($this->isEdit && $this->currentFotoPath) {
                Storage::disk('public')->delete($this->currentFotoPath);
            }
            $fotoPath = $this->foto->store('empleados', 'public');
        }

        if ($this->isEdit) {
            $user = User::find($this->empleadoId);
            $user->update([
                'name' => $this->name,
                'apellido_paterno' => $this->apellido_paterno,
                'apellido_materno' => $this->apellido_materno,
                'carnet_identidad' => $this->carnet_identidad,
                'email' => $this->email ?: null,
                'sucursal_id' => $this->rol === 'Gerente' ? null : $this->sucursal_id,
                'foto_path' => $fotoPath,
            ]);

            // Sync role
            $user->syncRoles([$this->rol]);

            session()->flash('success', 'Empleado actualizado con éxito.');
        } else {
            // Rule #12: Father's last name in lowercase as initial password
            $initialPassword = strtolower(trim($this->apellido_paterno));

            $user = User::create([
                'name' => $this->name,
                'apellido_paterno' => $this->apellido_paterno,
                'apellido_materno' => $this->apellido_materno,
                'carnet_identidad' => $this->carnet_identidad,
                'email' => $this->email ?: null,
                'password' => Hash::make($initialPassword),
                'password_changed' => false,
                'sucursal_id' => $this->rol === 'Gerente' ? null : $this->sucursal_id,
                'foto_path' => $fotoPath,
            ]);

            $user->assignRole($this->rol);

            session()->flash('success', 'Empleado registrado con éxito. Contraseña inicial: ' . $initialPassword);
        }

        $this->resetInputFields();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->empleadoId = $user->id;
        $this->name = $user->name;
        $this->apellido_paterno = $user->apellido_paterno;
        $this->apellido_materno = $user->apellido_materno;
        $this->carnet_identidad = $user->carnet_identidad;
        $this->email = $user->email ?? '';
        $this->sucursal_id = $user->sucursal_id ?? '';
        $this->rol = $user->roles->first()->name ?? '';
        $this->currentFotoPath = $user->foto_path;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'No puede eliminarse a sí mismo.');
            return;
        }

        if ($user->foto_path) {
            Storage::disk('public')->delete($user->foto_path);
        }
        $user->delete();
        session()->flash('success', 'Empleado eliminado con éxito.');
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->apellido_paterno = '';
        $this->apellido_materno = '';
        $this->carnet_identidad = '';
        $this->email = '';
        $this->sucursal_id = '';
        $this->rol = '';
        $this->foto = null;
        $this->currentFotoPath = null;
        $this->empleadoId = null;
        $this->isEdit = false;
    }

    public function render()
    {
        return view('livewire.gerente.rrhh', [
            'empleados' => User::with('sucursal')->get(),
            'sucursales' => Sucursal::where('activa', true)->get(),
        ])->layout('layouts.app');
    }
}
