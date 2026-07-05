<div>
    @section('title', 'Gestión de Sucursales')

    <div class="sucursales-layout">
        
        <!-- Form Column -->
        <div class="card sucursales-form">
            <div class="card-header">
                <h4 class="card-title">{{ $isEdit ? 'Editar Sucursal' : 'Registrar Nueva Sucursal' }}</h4>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="form-group">
                        <label class="form-label" style="color: var(--color-text-dark);">Nombre de la Sucursal</label>
                        <input wire:model.defer="nombre" type="text" class="form-control form-control-light" placeholder="Ej. Sucursal Norte" required>
                        @error('nombre') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="color: var(--color-text-dark);">Dirección</label>
                        <input wire:model.defer="direccion" type="text" class="form-control form-control-light" placeholder="Ej. Calle Murillo #450" required>
                        @error('direccion') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="color: var(--color-text-dark);">Teléfono</label>
                        <input wire:model.defer="telefono" type="text" class="form-control form-control-light" placeholder="Ej. 22445566" required>
                        @error('telefono') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                        <input wire:model.defer="activa" type="checkbox" id="activa" style="width: 18px; height: 18px; cursor: pointer;">
                        <label for="activa" style="cursor: pointer; font-weight: 500; font-size: 0.95rem; color: var(--color-text-dark);">Sucursal Activa</label>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 25px;">
                        <button type="submit" class="btn btn-primary" style="width: auto; flex-grow: 1;">
                            {{ $isEdit ? 'Actualizar' : 'Registrar' }}
                        </button>
                        @if($isEdit)
                            <button type="button" wire:click="resetInputFields" class="btn btn-secondary" style="width: auto;">
                                Cancelar
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- List Column -->
        <div class="card sucursales-list">
            <div class="card-header">
                <h4 class="card-title">Sucursales del Taller</h4>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th style="text-align: right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sucursales as $suc)
                                <tr>
                                    <td style="font-weight: 600;">{{ $suc->nombre }}</td>
                                    <td>{{ $suc->direccion }}</td>
                                    <td>{{ $suc->telefono }}</td>
                                    <td>
                                        @if($suc->activa)
                                            <span class="badge badge-green">Activa</span>
                                        @else
                                            <span class="badge badge-red">Inactiva</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="display: inline-flex; gap: 8px;">
                                            <button wire:click="edit({{ $suc->id }})" class="btn btn-light" style="padding: 6px 12px; width: auto; font-size: 0.85rem;">
                                                ✏️ Editar
                                            </button>
                                            <button wire:click="toggleStatus({{ $suc->id }})" class="btn {{ $suc->activa ? 'btn-secondary' : 'btn-primary' }}" style="padding: 6px 12px; width: auto; font-size: 0.85rem;">
                                                {{ $suc->activa ? '🚫 Desactivar' : '✅ Activar' }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--color-text-light-muted); padding: 30px;">
                                        No hay sucursales registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
