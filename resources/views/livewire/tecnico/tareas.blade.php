<div>
    @section('title', 'Mis Reparaciones Asignadas')

    <!-- Saturation Warning -->
    @if($activasCount >= 4)
        <div class="alert alert-danger" style="margin-bottom: 25px;">
            <span>⚠️ <strong>ATENCIÓN:</strong> Has alcanzado tu límite máximo de 4 órdenes activas. No se te asignarán más equipos hasta que liberes o finalices tus tareas actuales (Regla #1).</span>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
        
        <!-- Left: Tasks List -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Equipos Asignados ({{ $activasCount }} / 4 Activos)</h4>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Equipo</th>
                                <th>Estado</th>
                                <th style="text-align: right;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tareas as $tar)
                                <tr style="{{ $tar->id === $selectedOrderId ? 'background-color: var(--color-red-light);' : '' }}">
                                    <td style="font-weight: 700; color: var(--color-red);">{{ $tar->numero_ticket }}</td>
                                    <td>
                                        <span style="font-weight: 600; display: block;">{{ ucfirst($tar->categoria) }}</span>
                                        <span style="font-size: 0.8rem; color: var(--color-text-light-muted);">{{ $tar->marca }} {{ $tar->modelo }}</span>
                                    </td>
                                    <td>
                                        @switch($tar->state ?? $tar->estado)
                                            @case('por_diagnosticar')
                                                <span class="badge badge-black">Por Diagnosticar</span>
                                                @break
                                            @case('diagnosticado')
                                                <span class="badge badge-yellow">Diagnosticado</span>
                                                @break
                                            @case('esperando_aprobacion')
                                                <span class="badge badge-yellow" style="color: #ff9800; background-color: rgba(255, 152, 0, 0.1);">Esperando Cliente</span>
                                                @break
                                            @case('en_reparacion')
                                                <span class="badge badge-red">En Reparación</span>
                                                @break
                                            @case('reparado')
                                                <span class="badge badge-green">Reparado</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td style="text-align: right;">
                                        @if($tar->estado === 'por_diagnosticar')
                                            <button wire:click="selectOrden({{ $tar->id }})" class="btn btn-primary" style="padding: 6px 12px; width: auto; font-size: 0.82rem;">
                                                🔍 Diagnosticar
                                            </button>
                                        @elseif($tar->estado === 'en_reparacion')
                                            <button wire:click="marcarReparado({{ $tar->id }})" class="btn btn-secondary" style="padding: 6px 12px; width: auto; font-size: 0.82rem; background-color: #28a745;">
                                                ✅ Reparado
                                            </button>
                                        @else
                                            <button wire:click="selectOrden({{ $tar->id }})" class="btn btn-light" style="padding: 6px 12px; width: auto; font-size: 0.82rem;">
                                                👁️ Ficha
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--color-text-light-muted); padding: 30px;">
                                        No tienes tareas asignadas actualmente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Diagnostic Form -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Formulario de Diagnóstico Obligatorio</h4>
            </div>
            <div class="card-body">
                @if($selectedOrderId)
                    @php
                        $ordSel = \App\Models\OrdenReparacion::find($selectedOrderId);
                    @endphp
                    <div style="background-color: #f8f9fa; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 3px solid var(--color-red);">
                        <strong style="color: var(--color-text-dark);">Equipo Seleccionado:</strong> {{ $ordSel->marca }} {{ $ordSel->modelo }} (Ticket: {{ $ordSel->numero_ticket }})
                        <br><span style="font-size: 0.85rem; color: var(--color-text-light-muted);">Problema reportado: {{ $ordSel->problema_reportado }}</span>
                    </div>

                    @if($ordSel->estado === 'por_diagnosticar')
                        <form wire:submit.prevent="saveDiagnostico">
                            
                            <h5 style="color: var(--color-text-dark); margin-bottom: 15px; border-bottom: 1px solid var(--border-light); padding-bottom: 5px; font-weight: 700;">
                                Checklist Técnico (Categoría: {{ strtoupper($ordSel->categoria) }})
                            </h5>

                            <!-- Dynamic fields -->
                            <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 25px;">
                                @foreach($diagFields as $field => $value)
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label class="form-label" style="color: var(--color-text-dark); text-transform: capitalize; font-weight: 500;">
                                            {{ str_replace('_', ' ', $field) }}
                                        </label>
                                        <input wire:model.defer="diagFields.{{ $field }}" type="text" class="form-control form-control-light" placeholder="Ej. Buen estado, rayado, no responde..." required>
                                        @error("diagFields.{$field}") <span class="error-message">{{ $message }}</span> @enderror
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-group">
                                <label class="form-label" style="color: var(--color-text-dark); font-weight: 600;">Diagnóstico Técnico y Observaciones Generales</label>
                                <textarea wire:model.defer="observaciones" class="form-control form-control-light" rows="3" placeholder="Detalle técnico detallado de la avería..." required></textarea>
                                @error('observaciones') <span class="error-message">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label" style="color: var(--color-text-dark); font-weight: 600;">Presupuesto Estimado (Bs.)</label>
                                <input wire:model.defer="costo_estimado" type="number" step="0.01" class="form-control form-control-light" placeholder="Costo de repuestos y mano de obra" required>
                                <span style="font-size: 0.78rem; color: var(--color-text-light-muted); display: block; margin-top: 4px;">
                                    El equipo se pondrá en espera de la aprobación formal firmada del cliente antes de poder iniciar reparaciones (Regla #11).
                                </span>
                                @error('costo_estimado') <span class="error-message">{{ $message }}</span> @enderror
                            </div>

                            <div style="display: flex; gap: 10px; margin-top: 25px; border-top: 1px solid var(--border-light); padding-top: 20px;">
                                <button type="submit" class="btn btn-primary" style="flex-grow: 1;">
                                    💾 Guardar Diagnóstico y Enviar Presupuesto
                                </button>
                            </div>
                        </form>
                    @else
                        <!-- View Only Mode for Already Diagnosed -->
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <h5 style="color: var(--color-text-dark); font-weight: 700;">Detalles de Diagnóstico Guardado</h5>
                            
                            <table class="table" style="background-color: #fafafa;">
                                <tbody>
                                    @foreach($diagFields as $key => $val)
                                        <tr>
                                            <td style="font-weight: 600; text-transform: capitalize;">{{ str_replace('_', ' ', $key) }}</td>
                                            <td>{{ $val }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td style="font-weight: 600;">Observaciones Técnicas</td>
                                        <td>{{ $observaciones }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600; color: var(--color-red);">Costo Presupuestado</td>
                                        <td style="font-weight: 700; color: var(--color-red);">Bs. {{ number_format($costo_estimado, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            @if($ordSel->estado === 'esperando_aprobacion')
                                <div class="alert alert-warning" style="margin-top: 10px;">
                                    <span>Presupuesto enviado. Esperando que el cliente firme la aprobación digital.</span>
                                </div>
                            @elseif($ordSel->estado === 'en_reparacion')
                                <div class="alert alert-success" style="margin-top: 10px;">
                                    <span>Presupuesto aprobado por el cliente. En proceso de reparación activa.</span>
                                </div>
                            @endif
                        </div>
                    @endif
                @else
                    <div style="text-align: center; color: var(--color-text-light-muted); padding: 50px 20px;">
                        ⚙️ Selecciona una orden de la lista para completar su diagnóstico.
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
