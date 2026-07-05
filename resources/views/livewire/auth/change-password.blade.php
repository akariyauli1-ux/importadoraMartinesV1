<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">Nueva Contraseña</h1>
            <p class="auth-subtitle">Debe cambiar su contraseña inicial para continuar</p>
        </div>

        <div class="alert alert-warning">
            <span>Por motivos de seguridad, es obligatorio cambiar la contraseña asignada por defecto (su apellido paterno) por una contraseña personalizada.</span>
        </div>

        <form wire:submit.prevent="save">
            <div class="form-group">
                <label class="form-label" for="password">Nueva Contraseña</label>
                <input wire:model.defer="password" type="password" id="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
                @error('password') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirmar Contraseña</label>
                <input wire:model.defer="password_confirmation" type="password" id="password_confirmation" class="form-control" placeholder="Repita la contraseña" required>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">
                Guardar y Acceder
            </button>
        </form>
    </div>
</div>
