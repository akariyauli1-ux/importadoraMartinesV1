<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">IMPORTADORA MARTINEZ</h1>
            <p class="auth-subtitle">Gestión de Taller - Acceso Personal</p>
        </div>

        @if (session()->has('error'))
            <div class="alert alert-danger">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="alert alert-warning">
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        <form wire:submit.prevent="login">
            <div class="form-group">
                <label class="form-label" for="carnet_identidad">Carnet de Identidad (CI)</label>
                <input wire:model.defer="carnet_identidad" type="text" id="carnet_identidad" class="form-control" placeholder="Ingrese su C.I." required>
                @error('carnet_identidad') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Contraseña</label>
                <input wire:model.defer="password" type="password" id="password" class="form-control" placeholder="••••••••" required>
                @error('password') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="captcha">Código de Seguridad (CAPTCHA)</label>
                <div class="captcha-container">
                    <img src="{{ url('/captcha-image') }}?id={{ $captchaId }}" class="captcha-img" alt="CAPTCHA" wire:click="refreshCaptcha" title="Haga clic para actualizar">
                    <button type="button" class="btn-refresh" wire:click="refreshCaptcha" title="Actualizar CAPTCHA">
                        🔄
                    </button>
                    <input wire:model.defer="captcha" type="text" id="captcha" class="form-control" style="flex-grow: 1;" placeholder="Escriba el texto" maxlength="5" required autocomplete="off">
                </div>
                @error('captcha') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">
                Ingresar al Sistema
            </button>
        </form>
    </div>
</div>
