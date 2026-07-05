<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePassword extends Component
{
    public $password = '';
    public $password_confirmation = '';

    protected $rules = [
        'password' => 'required|min:6|confirmed',
    ];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // If they already changed it, send them to dashboard
        if (Auth::user()->password_changed) {
            return redirect()->route('dashboard');
        }
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();
        $user->password = Hash::make($this->password);
        $user->password_changed = true;
        
        /** @var \App\Models\User $user */
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Contraseña cambiada con éxito. ¡Bienvenido!');
    }

    public function render()
    {
        return view('livewire.auth.change-password')
            ->layout('layouts.auth');
    }
}
