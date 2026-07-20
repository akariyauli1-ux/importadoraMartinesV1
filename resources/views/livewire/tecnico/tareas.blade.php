<div>
    @section('title', 'Mis Reparaciones Asignadas')

    @if($activasCount >= 4)
        <div class="alert alert-danger" style="margin-bottom: 25px;">
            <span>⚠️ <strong>ATENCIÓN:</strong> Has alcanzado tu límite máximo de 4 órdenes activas. No se te asignarán más equipos hasta que liberes o finalices tus tareas actuales (Regla #1).</span>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
        
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

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Formulario de Diagnóstico</h4>
            </div>
            <div class="card-body">
                @if($selectedOrderId && $selectedOrderData)
                    <div style="background-color: #f8f9fa; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 3px solid var(--color-red);">
                        <strong style="color: var(--color-text-dark);">Equipo Seleccionado:</strong> {{ $selectedOrderData['marca'] }} {{ $selectedOrderData['modelo'] }} (Ticket: {{ $selectedOrderData['numero_ticket'] }})
                        <br><span style="font-size: 0.85rem; color: var(--color-text-light-muted);">Problema reportado: {{ $selectedOrderData['problema_reportado'] }}</span>
                    </div>

                    @if($selectedOrderData['estado'] === 'por_diagnosticar')
                        <form wire:submit.prevent="saveDiagnostico">
                            
                            <h5 style="color: var(--color-text-dark); margin-bottom: 15px; border-bottom: 1px solid var(--border-light); padding-bottom: 5px; font-weight: 700;">
                                Checklist Técnico (Categoría: {{ strtoupper($selectedOrderData['categoria']) }})
                            </h5>

                            <!-- Dynamic radio fields -->
                            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 25px;">
                                @foreach($diagFields as $field => $value)
                                    <div class="form-group" style="margin-bottom: 0; padding: 10px; background-color: #fafafa; border-radius: 6px; border: 1px solid var(--border-light);">
                                        <label class="form-label" style="color: var(--color-text-dark); text-transform: capitalize; font-weight: 600; margin-bottom: 6px;">
                                            {{ str_replace('_', ' ', $field) }}
                                        </label>
                                        <div style="display: flex; gap: 15px; margin-top: 4px; flex-wrap: wrap;">
                                            <label style="display: flex; align-items: center; gap: 4px; cursor: pointer; font-weight: 400; font-size: 0.85rem;">
                                                <input type="radio" wire:model="diagFields.{{ $field }}" value="buen_estado">
                                                <span style="color: #28a745;">Buen estado</span>
                                            </label>
                                            <label style="display: flex; align-items: center; gap: 4px; cursor: pointer; font-weight: 400; font-size: 0.85rem;">
                                                <input type="radio" wire:model="diagFields.{{ $field }}" value="mal_estado">
                                                <span style="color: #dc3545;">Mal estado</span>
                                            </label>
                                            <label style="display: flex; align-items: center; gap: 4px; cursor: pointer; font-weight: 400; font-size: 0.85rem;">
                                                <input type="radio" wire:model="diagFields.{{ $field }}" value="no_corresponde">
                                                <span style="color: #6c757d;">No corresponde</span>
                                            </label>
                                        </div>
                                        @error("diagFields.{$field}") <span class="error-message" style="margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-group">
                                <label class="form-label" style="color: var(--color-text-dark); font-weight: 600;">Diagnóstico Técnico y Observaciones Extras</label>
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
                                <button type="button" wire:click="generarReporte" class="btn btn-secondary" style="flex-grow: 1; background-color: #6c757d;">
                                    📋 Generar Reporte de Detalle Técnico
                                </button>
                                <button type="submit" class="btn btn-primary" style="flex-grow: 1;">
                                    💾 Guardar Diagnóstico y Enviar Presupuesto
                                </button>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-top: 12px; padding: 8px 12px; background-color: #f8f9fa; border-radius: 6px;">
                                <input type="checkbox" wire:model="enviarWhatsappDiagnostico" id="chk-whatsapp-diag" style="width: 16px; height: 16px; cursor: pointer;">
                                <label for="chk-whatsapp-diag" style="font-size: 0.8rem; cursor: pointer; color: var(--color-text-dark); margin: 0;">
                                    Enviar presupuesto por WhatsApp al cliente
                                </label>
                            </div>
                        </form>

                        @if($mostrarReporte)
                            <div style="margin-top: 25px; border: 2px solid var(--color-red); border-radius: 8px; overflow: hidden;">
                                <div style="background-color: var(--color-red); color: #fff; padding: 12px 16px; font-weight: 700; display: flex; justify-content: space-between; align-items: center;">
                                    <span>📋 Reporte de Detalle Técnico</span>
                                    <span style="font-size: 0.8rem; font-weight: 400;">Ticket: {{ $selectedOrderData['numero_ticket'] }}</span>
                                </div>
                                <div style="padding: 16px;">
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                                        <thead>
                                            <tr style="border-bottom: 2px solid #e9ecef;">
                                                <th style="text-align: left; padding: 8px 6px; color: #495057; width: 40%;">Componente</th>
                                                <th style="text-align: left; padding: 8px 6px; color: #495057;">Diagnóstico</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $malEstado = array_keys(array_filter($diagFields, function($v) { return $v === 'mal_estado'; })); @endphp
                                            @foreach($diagFields as $f => $v)
                                                <tr style="border-bottom: 1px solid #f1f3f5;">
                                                    <td style="padding: 8px 6px; font-weight: 600; text-transform: capitalize;">{{ str_replace('_', ' ', $f) }}</td>
                                                    <td style="padding: 8px 6px;">
                                                        @if($v === 'buen_estado')
                                                            <span style="background-color: #d4edda; color: #155724; padding: 3px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">✅ Buen estado</span>
                                                        @elseif($v === 'mal_estado')
                                                            <span style="background-color: #f8d7da; color: #721c24; padding: 3px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">❌ Mal estado</span>
                                                        @elseif($v === 'no_corresponde')
                                                            <span style="background-color: #e2e3e5; color: #383d41; padding: 3px 10px; border-radius: 4px; font-size: 0.8rem;">⊘ No corresponde</span>
                                                        @else
                                                            <span style="color: #999; font-style: italic;">Sin evaluar</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    @if(count($malEstado) > 0)
                                        <div class="alert alert-danger" style="margin-top: 15px; font-size: 0.85rem;">
                                            <strong>⚠️ Componentes con mal estado detectados:</strong> {{ implode(', ', array_map(function($f) { return str_replace('_', ' ', $f); }, $malEstado)) }}
                                        </div>
                                    @endif

                                    <div style="margin-top: 15px; padding: 12px; background-color: #f8f9fa; border-radius: 6px;">
                                        <strong style="font-size: 0.85rem; color: var(--color-text-dark); display: block; margin-bottom: 4px;">Observaciones Técnicas:</strong>
                                        <p style="font-size: 0.85rem; color: #495057; margin: 0; line-height: 1.5;">{{ $observaciones ?: 'Sin observaciones registradas.' }}</p>
                                    </div>

                                    <div style="margin-top: 12px; display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 0.82rem; color: var(--color-text-light-muted);">Resumen del diagnóstico</span>
                                        <span style="font-weight: 700; font-size: 1.1rem; color: var(--color-red);">Presupuesto: Bs. {{ number_format($costo_estimado, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- View Only Mode for Already Diagnosed -->
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <h5 style="color: var(--color-text-dark); font-weight: 700;">Detalles de Diagnóstico Guardado</h5>
                            
                            <table class="table" style="background-color: #fafafa;">
                                <tbody>
                                    @foreach($diagFields as $key => $val)
                                        <tr>
                                            <td style="font-weight: 600; text-transform: capitalize;">{{ str_replace('_', ' ', $key) }}</td>
                                            <td>
                                                @if($val === 'buen_estado')
                                                    <span style="background-color: #d4edda; color: #155724; padding: 3px 10px; border-radius: 4px; font-size: 0.82rem; font-weight: 600;">✅ Buen estado</span>
                                                @elseif($val === 'mal_estado')
                                                    <span style="background-color: #f8d7da; color: #721c24; padding: 3px 10px; border-radius: 4px; font-size: 0.82rem; font-weight: 600;">❌ Mal estado</span>
                                                @elseif($val === 'no_corresponde')
                                                    <span style="background-color: #e2e3e5; color: #383d41; padding: 3px 10px; border-radius: 4px; font-size: 0.82rem;">⊘ No corresponde</span>
                                                @else
                                                    {{ $val }}
                                                @endif
                                            </td>
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
                            
                            @if($selectedOrderData['estado'] === 'esperando_aprobacion')
                                <div class="alert alert-warning" style="margin-top: 10px;">
                                    <span>Presupuesto enviado. Esperando que el cliente firme la aprobación digital.</span>
                                </div>
                            @elseif($selectedOrderData['estado'] === 'en_reparacion')
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
