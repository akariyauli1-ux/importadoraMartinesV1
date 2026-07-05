<div>
    @section('title', 'Cola de Trabajo y Asignaciones')

    <!-- Main Queue Card -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Supervisión de Cola de Reparaciones</h4>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Cliente</th>
                            <th>Equipo</th>
                            <th>Técnico Asignado</th>
                            <th>Estado</th>
                            <th>Derivación</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordenes as $ord)
                            <tr>
                                <td style="font-weight: 700; color: var(--color-red);">{{ $ord->numero_ticket }}</td>
                                <td>{{ $ord->cliente->nombre }}</td>
                                <td>
                                    <span style="font-weight: 600; display: block;">{{ ucfirst($ord->categoria) }}</span>
                                    <span style="font-size: 0.8rem; color: var(--color-text-light-muted);">{{ $ord->marca }} {{ $ord->modelo }}</span>
                                </td>
                                <td>
                                    @if($ord->tecnico)
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $ord->tecnico->ordenesTecnico()->whereIn('estado', ['por_diagnosticar', 'diagnosticado', 'esperando_aprobacion', 'en_reparacion'])->count() >= 4 ? 'var(--color-red)' : '#28a745' }}"></div>
                                            <span>{{ $ord->tecnico->nombre_completo }}</span>
                                        </div>
                                    @else
                                        <span style="color: var(--color-red); font-weight: 600; font-size: 0.85rem;">⚠️ SIN ASIGNAR</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($ord->estado)
                                        @case('por_diagnosticar')
                                            <span class="badge badge-black">Por Diagnosticar</span>
                                            @break
                                        @case('diagnosticado')
                                            <span class="badge badge-yellow">Diagnosticado</span>
                                            @break
                                        @case('esperando_aprobacion')
                                            <span class="badge badge-yellow">Esperando Aprobación</span>
                                            @break
                                        @case('en_reparacion')
                                            <span class="badge badge-red">En Reparación</span>
                                            @break
                                        @case('reparado')
                                            <span class="badge badge-green">Reparado</span>
                                            @break
                                        @case('entregado')
                                            <span class="badge badge-green" style="background-color: #e2f0d9; color: #385723;">Entregado</span>
                                            @break
                                        @case('rechazado')
                                            <span class="badge badge-black" style="background-color: #f2f2f2; color: #7f7f7f;">Rechazado</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @if($ord->es_asignacion_cruzada)
                                        <span class="badge badge-red" style="font-size: 0.72rem;" title="Motivo: {{ $ord->motivo_derivacion }}">🔀 Cross-Sede</span>
                                    @else
                                        <span class="badge badge-black" style="font-size: 0.72rem; background-color: #f1f3f5;">🏠 Local</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <button wire:click="abrirAsignacion({{ $ord->id }})" class="btn btn-primary" style="padding: 6px 12px; width: auto; font-size: 0.85rem;">
                                        🔄 Asignar Técnico
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--color-text-light-muted); padding: 30px;">
                                    No hay órdenes activas en cola en esta sucursal.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Allocation Modal (Pure CSS overlay responsive layout) -->
    @if($mostrarModalAsignacion)
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.65); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px;">
            <div class="card" style="width: 100%; max-width: 550px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); border-top: 4px solid var(--color-red); margin-bottom: 0;">
                <div class="card-header">
                    <h4 class="card-title">Asignar Orden de Trabajo</h4>
                    <button wire:click="cerrarAsignacion" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--color-text-dark);">✕</button>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="asignar">
                        
                        <!-- Technician List with Load info -->
                        <div class="form-group">
                            <label class="form-label" style="color: var(--color-text-dark); font-weight: 600;">Seleccionar Técnico Asignado</label>
                            <select wire:model="tecnicoId" class="form-control form-control-light" required style="padding: 10px;">
                                <option value="">-- Seleccione un Técnico --</option>
                                @foreach($tecnicos as $tec)
                                    @php
                                        $isFull = $tec->active_orders_count >= 4;
                                        $isLocal = $tec->sucursal_id === auth()->user()->sucursal_id;
                                    @endphp
                                    <option value="{{ $tec->id }}" {{ $isFull ? 'disabled' : '' }} style="{{ $isFull ? 'color: #ccc;' : '' }}">
                                        {{ $tec->nombre_completo }} 
                                        [{{ $isLocal ? 'LOCAL' : 'CROSS-SEDE: ' . ($tec->sucursal->nombre ?? 'N/A') }}] 
                                        — ({{ $tec->active_orders_count }} / 4 activas)
                                        {{ $isFull ? ' [LLENO]' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tecnicoId') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <!-- Conditionally show derivation reason -->
                        @php
                            $selectedTec = \App\Models\User::find($tecnicoId);
                            $mostrarMotivo = $selectedTec && $selectedTec->sucursal_id !== auth()->user()->sucursal_id;
                        @endphp

                        @if($mostrarMotivo)
                            <div class="form-group" style="background-color: rgba(229, 9, 20, 0.05); padding: 15px; border-radius: 8px; border: 1px solid rgba(229, 9, 20, 0.15);">
                                <label class="form-label" style="color: var(--color-red); font-weight: 600;">Motivo de Derivación Externa (Obligatorio)</label>
                                <textarea wire:model.defer="motivoDerivacion" class="form-control form-control-light" rows="3" placeholder="Escriba la especialidad requerida o la razón de saturación local..." required></textarea>
                                @error('motivoDerivacion') <span class="error-message" style="margin-top: 5px;">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px;">
                            <button type="button" wire:click="cerrarAsignacion" class="btn btn-secondary" style="width: auto;">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" style="width: auto;">
                                Confirmar Asignación
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
