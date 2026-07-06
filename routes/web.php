<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ChangePassword;
use App\Livewire\Dashboard;
use App\Services\CaptchaService;
use Illuminate\Support\Facades\Auth;

// 1. CAPTCHA Route
Route::get('/captcha-image', function () {
    return CaptchaService::generate();
})->name('captcha-image');

// 2. Authentication Routes
Route::get('/login', Login::class)->name('login');

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// 3. Protected Routes
Route::middleware(['auth'])->group(function () {
    
    // Change Password (force checked by middleware itself)
    Route::get('/change-password', ChangePassword::class)->name('change-password');
    
    // Checked if password is changed
    Route::middleware(['check.password.changed'])->group(function () {
        
        Route::get('/', Dashboard::class)->name('dashboard');

        // Gerente Routes
        Route::middleware(['role:Gerente'])->group(function () {
            Route::get('/gerente/sucursales', \App\Livewire\Gerente\Sucursales::class)->name('gerente.sucursales');
            Route::get('/gerente/rrhh', \App\Livewire\Gerente\Rrhh::class)->name('gerente.rrhh');
            Route::get('/gerente/branding', \App\Livewire\Gerente\Branding::class)->name('gerente.branding');
            Route::get('/gerente/reportes', \App\Livewire\Gerente\Reportes::class)->name('gerente.reportes');
        });

        // Administrador Routes
        Route::middleware(['role:Administrador'])->group(function () {
            Route::get('/admin/checklist', \App\Livewire\Admin\Checklist::class)->name('admin.checklist');
            Route::get('/admin/cola', \App\Livewire\Admin\Cola::class)->name('admin.cola');
        });

        // Recepcionista Routes
        Route::middleware(['role:Recepcionista'])->group(function () {
            Route::get('/recepcion/checkin', \App\Livewire\Recepcion\Checkin::class)->name('recepcion.checkin');
            Route::get('/recepcion/ordenes', \App\Livewire\Recepcion\Ordenes::class)->name('recepcion.ordenes');
        });

        // Técnico Routes
        Route::middleware(['role:Técnico'])->group(function () {
            Route::get('/tecnico/tareas', \App\Livewire\Tecnico\Tareas::class)->name('tecnico.tareas');
        });

        // Almacenista Routes
        Route::middleware(['role:Almacenista'])->group(function () {
            Route::get('/almacen/solicitudes', \App\Livewire\Almacen\Solicitudes::class)->name('almacen.solicitudes');
        });

    });
});

// 4. Public Routes (Client view for tracking and approving quotes)
Route::get('/cliente/orden/{numero_ticket}', \App\Livewire\Cliente\Orden::class)->name('cliente.orden');
Route::get('/cliente/orden/{numero_ticket}/pdf', [\App\Http\Controllers\PdfController::class, 'download'])->name('cliente.orden.pdf');
