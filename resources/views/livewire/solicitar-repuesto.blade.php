<div>
    <!-- Floating button to open modal -->
    <button wire:click="abrirModal" 
        style="position: fixed; bottom: 25px; right: 25px; z-index: 999; width: 56px; height: 56px; border-radius: 50%; background-color: var(--color-red); color: #fff; border: none; cursor: pointer; font-size: 1.3rem; box-shadow: 0 4px 15px rgba(229,9,20,0.4); display: flex; align-items: center; justify-content: center; transition: transform 0.2s;"
        title="Solicitar repuesto al almacén">
        📦
    </button>

    <!-- Modal -->
    @if($mostrarModal)
        <div style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center;" 
             wire:click.self="cerrarModal">
            <div style="position: absolute; inset: 0; background-color: rgba(0,0,0,0.5);"></div>
            <div style="background-color: #fff; border-radius: 12px; padding: 25px; max-width: 500px; width: 90%; position: relative; z-index: 1; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--color-red); padding-bottom: 10px;">
                    <h4 style="margin: 0; font-weight: 700; color: var(--color-text-dark);">📦 Solicitar Repuesto</h4>
                    <button wire:click="cerrarModal" style="background: none; border: none; font-size: 1.3rem; cursor: pointer; color: var(--color-text-light-muted);">&times;</button>
                </div>

                <form wire:submit.prevent="solicitar" style="display: flex; flex-direction: column; gap: 12px;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600;">Nombre del Repuesto *</label>
                        <input wire:model.defer="nombre_repuesto" type="text" class="form-control form-control-light" 
                            placeholder="Ej. Pantalla LCD iPhone 11, Batería HP Pavilion..." required>
                        @error('nombre_repuesto') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600;">Cantidad</label>
                            <input wire:model.defer="cantidad" type="number" min="1" max="999" class="form-control form-control-light" required>
                            @error('cantidad') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600;">Urgencia</label>
                            <select wire:model.defer="urgencia" class="form-control form-control-light">
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                            </select>
                            @error('urgencia') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600;">Observaciones (opcional)</label>
                        <textarea wire:model.defer="observaciones_solicitante" class="form-control form-control-light" rows="2" 
                            placeholder="Detalles adicionales, número de parte, especificaciones..."></textarea>
                        @error('observaciones_solicitante') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <button type="button" wire:click="cerrarModal" class="btn btn-secondary" style="flex-grow: 1;">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" style="flex-grow: 1; background-color: var(--color-red);">
                            📤 Enviar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
