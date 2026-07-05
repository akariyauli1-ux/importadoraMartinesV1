<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Services\CaptchaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class Login extends Component
{
    public $carnet_identidad = '';
    public $password = '';
    public $captcha = '';
    public $captchaId = '';

    protected $rules = [
        'carnet_identidad' => 'required',
        'password' => 'required',
        'captcha' => 'required',
    ];

    public function mount()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $this->refreshCaptcha();
    }

    public function refreshCaptcha()
    {
        $this->captchaId = uniqid(); // Change this state to reload the image in frontend
        $this->captcha = '';
    }

    private function getLockKey()
    {
        return 'login_lock_' . md5($this->carnet_identidad . '_' . request()->ip());
    }

    private function getAttemptsKey()
    {
        return 'login_attempts_' . md5($this->carnet_identidad . '_' . request()->ip());
    }

    public function login()
    {
        $this->validate();

        $lockKey = $this->getLockKey();
        $attemptsKey = $this->getAttemptsKey();

        // 1. Check if blocked
        if (Cache::has($lockKey)) {
            $secondsLeft = Cache::get($lockKey) - time();
            if ($secondsLeft > 0) {
                $minutes = ceil($secondsLeft / 60);
                $this->addError('carnet_identidad', "Acceso bloqueado por seguridad. Intente en {$minutes} minutos.");
                $this->refreshCaptcha();
                return;
            } else {
                Cache::forget($lockKey);
                Cache::forget($attemptsKey);
            }
        }

        // 2. Verify Captcha
        if (!CaptchaService::check($this->captcha)) {
            $this->handleFailure();
            $this->addError('captcha', 'Código CAPTCHA incorrecto o vencido.');
            $this->refreshCaptcha();
            return;
        }

        // 3. Find User
        $user = User::where('carnet_identidad', $this->carnet_identidad)->first();

        if (!$user || !Hash::check($this->password, $user->password)) {
            $this->handleFailure();
            $this->addError('carnet_identidad', 'El carnet o la contraseña son incorrectos.');
            $this->refreshCaptcha();
            return;
        }

        // Check if user is active (if their branch is active)
        if ($user->sucursal && !$user->sucursal->activa) {
            $this->addError('carnet_identidad', 'Su sucursal asignada se encuentra inactiva.');
            $this->refreshCaptcha();
            return;
        }

        // 4. Success: Clear attempts and login
        Cache::forget($lockKey);
        Cache::forget($attemptsKey);

        Auth::login($user);

        // Rule #13: Force password change on first login
        if (!$user->password_changed) {
            return redirect()->route('change-password');
        }

        return redirect()->route('dashboard');
    }

    private function handleFailure()
    {
        $attemptsKey = $this->getAttemptsKey();
        $lockKey = $this->getLockKey();

        $attempts = Cache::get($attemptsKey, 0) + 1;
        Cache::put($attemptsKey, $attempts, 300); // 5 minutes lifetime

        if ($attempts >= 3) {
            // Block for 5 minutes
            Cache::put($lockKey, time() + 300, 300);
            $this->addError('carnet_identidad', 'Ha fallado 3 intentos. Acceso bloqueado por 5 minutos.');
        }
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.auth');
    }
}
