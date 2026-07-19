<div>
    @section('title', 'Identidad Corporativa y Branding')

    <div style="max-width: 600px; margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Configurar Logotipo y Branding</h4>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label" style="color: var(--color-text-dark);">Nombre Comercial de la Empresa</label>
                        <input wire:model.defer="nombre_comercial" type="text" class="form-control form-control-light" placeholder="Ej. IMPORTADORA MARTINEZ" required>
                        @error('nombre_comercial') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="color: var(--color-text-dark);">Logotipo Corporativo (aparece en el Sistema)</label>
                        <input wire:model="logo" type="file" class="form-control form-control-light" style="padding: 6px;" accept="image/*">
                        <span style="font-size: 0.78rem; color: var(--color-text-light-muted); display: block; margin-top: 4px;">
                            Subir archivo JPG o PNG de peso menor a 2MB (Regla #14).
                        </span>
                        @error('logo') <span class="error-message">{{ $message }}</span> @enderror

                        @if ($logo)
                            <div style="margin-top: 20px; border: 1px solid var(--border-light); padding: 15px; border-radius: 8px; text-align: center; background-color: #f8f9fa;">
                                <span style="display: block; font-size: 0.8rem; color: var(--color-text-light-muted); margin-bottom: 10px;">Vista previa de nuevo logotipo:</span>
                                <img src="{{ $logo->temporaryUrl() }}" style="max-height: 100px; max-width: 100%; object-fit: contain;">
                            </div>
                        @elseif ($currentLogoPath)
                            <div style="margin-top: 20px; border: 1px solid var(--border-light); padding: 15px; border-radius: 8px; text-align: center; background-color: #f8f9fa;">
                                <span style="display: block; font-size: 0.8rem; color: var(--color-text-light-muted); margin-bottom: 10px;">Logotipo actual:</span>
                                <img src="{{ asset('storage/' . $currentLogoPath) }}" style="max-height: 100px; max-width: 100%; object-fit: contain;">
                            </div>
                        @else
                            <div style="margin-top: 20px; border: 1px dashed var(--border-light); padding: 25px; border-radius: 8px; text-align: center; color: var(--color-text-light-muted);">
                                Sin logotipo registrado. Se mostrará el texto por defecto en la cabecera.
                            </div>
                        @endif
                    </div>

                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">
                            Guardar Cambios de Branding
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
