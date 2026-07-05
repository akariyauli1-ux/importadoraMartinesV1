<div>
    @section('title', 'Gestión de Personal (RRHH)')

    <div class="rrhh-layout">
        
        <!-- Form Column -->
        <div class="card rrhh-form">
            <div class="card-header">
                <h4 class="card-title">{{ $isEdit ? 'Editar Empleado' : 'Registrar Nuevo Empleado' }}</h4>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label" style="color: var(--color-text-dark);">Nombre(s)</label>
                        <input wire:model.defer="name" type="text" class="form-control form-control-light" placeholder="Ej. Juan Carlos" required>
                        @error('name') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="color: var(--color-text-dark);">Apellido Paterno</label>
                        <input wire:model.defer="apellido_paterno" type="text" class="form-control form-control-light" placeholder="Ej. Martinez" required>
                        @error('apellido_paterno') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="color: var(--color-text-dark);">Apellido Materno</label>
                        <input wire:model.defer="apellido_materno" type="text" class="form-control form-control-light" placeholder="Ej. Miranda">
                        @error('apellido_materno') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="color: var(--color-text-dark);">Carnet de Identidad (C.I.)</label>
                        <input wire:model.defer="carnet_identidad" type="text" class="form-control form-control-light" placeholder="Ej. 123456" required>
                        @error('carnet_identidad') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="color: var(--color-text-dark);">Correo Electrónico (Opcional)</label>
                        <input wire:model.defer="email" type="email" class="form-control form-control-light" placeholder="Ej. juan@correo.com">
                        @error('email') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="color: var(--color-text-dark);">Rol en el Taller</label>
                        <select wire:model="rol" class="form-control form-control-light" required style="padding: 10px;">
                            <option value="">Seleccione un Rol</option>
                            <option value="Gerente">Gerente</option>
                            <option value="Administrador">Administrador (Sede)</option>
                            <option value="Técnico">Técnico (Reparador)</option>
                            <option value="Recepcionista">Recepcionista</option>
                        </select>
                        @error('rol') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    @if($rol && $rol !== 'Gerente')
                        <div class="form-group">
                            <label class="form-label" style="color: var(--color-text-dark);">Sucursal Asignada</label>
                            <select wire:model="sucursal_id" class="form-control form-control-light" required style="padding: 10px;">
                                <option value="">Seleccione una Sucursal</option>
                                @foreach($sucursales as $suc)
                                    <option value="{{ $suc->id }}">{{ $suc->nombre }}</option>
                                @endforeach
                            </select>
                            <span style="font-size: 0.78rem; color: var(--color-text-light-muted); display: block; margin-top: 4px;">
                                Límites por sucursal: Máx. 10 Técnicos y 2 Recepcionistas.
                            </span>
                            @error('sucursal_id') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label" style="color: var(--color-text-dark);">Fotografía del Empleado</label>
                        <input wire:model="foto" type="file" class="form-control form-control-light" style="padding: 6px;" accept="image/*">
                        <span style="font-size: 0.78rem; color: var(--color-text-light-muted); display: block; margin-top: 4px;">
                            Solo JPG/PNG. Máximo 2MB (Regla #14).
                        </span>
                        @error('foto') <span class="error-message">{{ $message }}</span> @enderror

                        @if ($foto)
                            <div style="margin-top: 15px; text-align: center;">
                                <img src="{{ $foto->temporaryUrl() }}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-red);">
                                <span style="display: block; font-size: 0.8rem; color: var(--color-text-light-muted);">Vista previa de subida</span>
                            </div>
                        @elseif($currentFotoPath)
                            <div style="margin-top: 15px; text-align: center;">
                                <img src="{{ asset('storage/' . $currentFotoPath) }}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-red);">
                                <span style="display: block; font-size: 0.8rem; color: var(--color-text-light-muted);">Foto actual</span>
                            </div>
                        @endif
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 25px;">
                        <button type="submit" class="btn btn-primary" style="width: auto; flex-grow: 1;">
                            {{ $isEdit ? 'Actualizar' : 'Registrar Empleado' }}
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
        <div class="card rrhh-list">
            <div class="card-header">
                <h4 class="card-title">Personal Registrado</h4>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nombre Completo</th>
                                <th>C.I.</th>
                                <th>Rol</th>
                                <th>Sucursal</th>
                                <th style="text-align: right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($empleados as $emp)
                                <tr>
                                    <td>
                                        @if($emp->foto_path)
                                            <img src="{{ asset('storage/' . $emp->foto_path) }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--color-red);">
                                        @else
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #f1f3f5; border: 1.5px solid var(--color-red); display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700; color: var(--color-text-dark);">
                                                {{ substr($emp->name, 0, 1) }}{{ substr($emp->apellido_paterno, 0, 1) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="font-weight: 600;">
                                        {{ $emp->name }} {{ $emp->apellido_paterno }} {{ $emp->apellido_materno }}
                                    </td>
                                    <td>{{ $emp->carnet_identidad }}</td>
                                    <td>
                                        <span class="badge {{ $emp->hasRole('Gerente') ? 'badge-red' : ($emp->hasRole('Administrador') ? 'badge-black' : 'badge-yellow') }}">
                                            {{ $emp->getRoleNames()->first() ?? 'Sin Rol' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $emp->sucursal->nombre ?? 'N/A (Gerencia)' }}
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="display: inline-flex; gap: 8px;">
                                            <button wire:click="edit({{ $emp->id }})" class="btn btn-light" style="padding: 6px 12px; width: auto; font-size: 0.85rem;">
                                                ✏️ Editar
                                            </button>
                                            @if($emp->id !== auth()->id())
                                                <button onclick="confirm('¿Está seguro de eliminar a este empleado?') || event.stopImmediatePropagation()" wire:click="delete({{ $emp->id }})" class="btn btn-primary" style="padding: 6px 12px; width: auto; font-size: 0.85rem; background-color: #000;">
                                                    🗑️ Eliminar
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--color-text-light-muted); padding: 30px;">
                                        No hay empleados registrados.
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
