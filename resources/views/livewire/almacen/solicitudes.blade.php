<div>
    @section('title', 'Gestión de Repuestos - Almacén')

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
        
        <!-- Left: Solicitudes Table -->
        <div class="card" style="grid-row: span 2;">
            <div class="card-header">
                <h4 class="card-title">Solicitudes de Repuestos</h4>
            </div>
            <div class="card-body" style="padding: 0;">
                <!-- Filter tabs -->
                <div style="display: flex; gap: 5px; padding: 12px 15px; border-bottom: 1px solid var(--border-light); flex-wrap: wrap;">
                    @foreach([
                        'todas' => 'Todas',
                        'pendiente' => 'Pendientes',
                        'enviado' => 'Enviados',
                        'agotado' => 'Agotados',
                        'no_existe' => 'No existen',
                    ] as $val => $label)
                        <button wire:click="$set('filtroEstado', '{{ $val }}')"
                            style="padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; cursor: pointer; border: 1px solid {{ $filtroEstado === $val ? 'var(--color-red)' : '#dee2e6' }}; background-color: {{ $filtroEstado === $val ? 'var(--color-red)' : '#fff' }}; color: {{ $filtroEstado === $val ? '#fff' : '#495057' }};">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="font-size: 0.78rem;">Fecha</th>
                                <th style="font-size: 0.78rem;">Repuesto</th>
                                <th style="font-size: 0.78rem;">Solicitante</th>
                                <th style="font-size: 0.78rem;">Estado</th>
                                <th style="text-align: right; font-size: 0.78rem;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($solicitudes as $sol)
                                <tr style="{{ $sol->id === $selectedSolicitudId ? 'background-color: var(--color-red-light);' : '' }}">
                                    <td style="font-size: 0.82rem;">{{ $sol->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span style="font-weight: 600; display: block; font-size: 0.85rem;">{{ $sol->nombre_repuesto }}</span>
                                        <span style="font-size: 0.72rem; color: var(--color-text-light-muted);">Cant: {{ $sol->cantidad }} | {{ $sol->urgenciaLabel() }}</span>
                                    </td>
                                    <td style="font-size: 0.82rem;">{{ $sol->solicitante->nombre_completo }}</td>
                                    <td>
                                        @switch($sol->estado)
                                            @case('pendiente')
                                                <span class="badge badge-yellow">Pendiente</span>
                                                @break
                                            @case('enviado')
                                                <span class="badge badge-green">
                                                    {{ $sol->confirmado_recibido ? '✅ Recibido' : 'Enviado' }}
                                                </span>
                                                @break
                                            @case('agotado')
                                                <span class="badge badge-red">Agotado</span>
                                                @break
                                            @case('no_existe')
                                                <span class="badge badge-black">No existe</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td style="text-align: right;">
                                        @if($sol->estado === 'pendiente')
                                            <button wire:click="selectSolicitud({{ $sol->id }})" class="btn btn-primary" style="padding: 5px 10px; width: auto; font-size: 0.75rem;">
                                                Responder
                                            </button>
                                        @else
                                            <button wire:click="selectSolicitud({{ $sol->id }})" class="btn btn-light" style="padding: 5px 10px; width: auto; font-size: 0.75rem;">
                                                Ver
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--color-text-light-muted); padding: 30px;">
                                        No hay solicitudes de repuestos.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Response Form -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    @if($selectedSolicitudId)
                        @php $solSel = \App\Models\SolicitudRepuesto::find($selectedSolicitudId); @endphp
                        Responder Solicitud #{{ $selectedSolicitudId }}
                    @else
                        Detalle de Solicitud
                    @endif
                </h4>
            </div>
            <div class="card-body">
                @if($selectedSolicitudId)
                    @php $solSel = \App\Models\SolicitudRepuesto::with(['solicitante', 'ordenReparacion'])->find($selectedSolicitudId); @endphp
                    
                    <div style="background-color: #f8f9fa; padding: 12px; border-radius: 8px; margin-bottom: 15px; border-left: 3px solid var(--color-red);">
                        <strong style="display: block; font-size: 1rem;">{{ $solSel->nombre_repuesto }}</strong>
                        <span style="font-size: 0.8rem; color: var(--color-text-light-muted);">
                            Solicitado por: {{ $solSel->solicitante->nombre_completo }}
                            ({{ $solSel->solicitante->getRoleNames()->first() ?? 'N/A' }})
                        </span>
                        <br>
                        <span style="font-size: 0.8rem; color: var(--color-text-light-muted);">
                            Cantidad: {{ $solSel->cantidad }} | Urgencia: 
                            @switch($solSel->urgencia)
                                @case('alta') <span style="color: #dc3545; font-weight: 600;">Alta</span> @break
                                @case('media') <span style="color: #ff9800; font-weight: 600;">Media</span> @break
                                @case('baja') <span style="color: #6c757d;">Baja</span> @break
                            @endswitch
                        </span>
                        @if($solSel->ordenReparacion)
                            <br><span style="font-size: 0.78rem; color: var(--color-text-light-muted);">
                                Orden: {{ $solSel->ordenReparacion->numero_ticket }}
                            </span>
                        @endif
                    </div>

                    @if($solSel->observaciones_solicitante)
                        <div class="alert alert-info" style="font-size: 0.85rem;">
                            <strong>Nota del solicitante:</strong> {{ $solSel->observaciones_solicitante }}
                        </div>
                    @endif

                    @if($solSel->estado === 'pendiente')
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600;">Observaciones (opcional)</label>
                            <textarea wire:model="observaciones_respuesta" class="form-control form-control-light" rows="2" placeholder="Nota adicional sobre la respuesta..."></textarea>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 15px; border-top: 1px solid var(--border-light); padding-top: 15px;">
                            <button wire:click="responder('enviado')" class="btn" style="background-color: #28a745; color: #fff; width: 100%; padding: 10px; font-weight: 600;">
                                📦 Enviar - Hay en almacén
                            </button>
                            <button wire:click="responder('agotado')" class="btn" style="background-color: #ff9800; color: #fff; width: 100%; padding: 10px; font-weight: 600;">
                                📭 Agotado - Sin stock actual
                            </button>
                            <button wire:click="responder('no_existe')" class="btn" style="background-color: #dc3545; color: #fff; width: 100%; padding: 10px; font-weight: 600;">
                                🚫 No existe - No manejamos este repuesto
                            </button>
                        </div>
                    @else
                        <div style="margin-top: 15px;">
                            <table class="table" style="background-color: #fafafa;">
                                <tbody>
                                    <tr>
                                        <td style="font-weight: 600;">Estado</td>
                                        <td>{{ $solSel->estadoLabel() }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Respondido por</td>
                                        <td>{{ $solSel->almacenista ? $solSel->almacenista->nombre_completo : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Fecha respuesta</td>
                                        <td>{{ $solSel->fecha_respuesta ? $solSel->fecha_respuesta->format('d/m/Y H:i') : 'N/A' }}</td>
                                    </tr>
                                    @if($solSel->estado === 'enviado')
                                    <tr>
                                        <td style="font-weight: 600;">Recepcion confirmada</td>
                                        <td>
                                            @if($solSel->confirmado_recibido)
                                                <span style="color: #28a745; font-weight: 600;">✅ Confirmado</span>
                                                <span style="font-size: 0.75rem; color: var(--color-text-light-muted); display: block;">
                                                    {{ $solSel->fecha_confirmacion ? $solSel->fecha_confirmacion->format('d/m/Y H:i') : '' }}
                                                </span>
                                            @else
                                                <span style="color: #ff9800;">⏳ Pendiente de confirmación</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                            @if($solSel->observaciones_almacenista)
                                <div class="alert alert-info" style="font-size: 0.85rem; margin-top: 10px;">
                                    <strong>Nota del almacenista:</strong> {{ $solSel->observaciones_almacenista }}
                                </div>
                            @endif
                        </div>
                    @endif
                @else
                    <div style="text-align: center; color: var(--color-text-light-muted); padding: 30px 20px;">
                        📋 Selecciona una solicitud de la lista para ver detalles y responder.
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Bottom: Statistics -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Estadísticas de Almacén</h4>
            </div>
            <div class="card-body">
                <!-- Summary Cards -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
                    <div style="background-color: #d4edda; border-radius: 8px; padding: 15px; text-align: center;">
                        <span style="font-size: 1.5rem; font-weight: 800; color: #155724; display: block;">{{ $enviados }} / {{ $recibidos }}</span>
                        <span style="font-size: 0.78rem; color: #155724; font-weight: 600;">Enviados / Recibidos</span>
                    </div>
                    <div style="background-color: #f8d7da; border-radius: 8px; padding: 15px; text-align: center;">
                        <span style="font-size: 1.5rem; font-weight: 800; color: #721c24; display: block;">{{ $noExisten }}</span>
                        <span style="font-size: 0.78rem; color: #721c24; font-weight: 600;">No Existen en Almacén</span>
                    </div>
                    <div style="background-color: #fff3cd; border-radius: 8px; padding: 15px; text-align: center;">
                        <span style="font-size: 1.5rem; font-weight: 800; color: #856404; display: block;">{{ $pendientes }}</span>
                        <span style="font-size: 0.78rem; color: #856404; font-weight: 600;">Pendientes</span>
                    </div>
                    <div style="background-color: #ffeaa7; border-radius: 8px; padding: 15px; text-align: center;">
                        <span style="font-size: 1.5rem; font-weight: 800; color: #7c5e00; display: block;">{{ $agotados }}</span>
                        <span style="font-size: 0.78rem; color: #7c5e00; font-weight: 600;">Agotados</span>
                    </div>
                </div>

                <!-- Top Enviados -->
                <h5 style="font-weight: 700; font-size: 0.9rem; margin-bottom: 10px; border-bottom: 1px solid var(--border-light); padding-bottom: 5px;">
                    📦 Repuestos más enviados
                </h5>
                @if($topEnviados->count() > 0)
                    <table class="table" style="font-size: 0.82rem; background-color: #fafafa;">
                        <tbody>
                            @foreach($topEnviados as $item)
                                <tr>
                                    <td style="font-weight: 600;">{{ $item->nombre_repuesto }}</td>
                                    <td style="text-align: right;"><span class="badge badge-green">{{ $item->total }} uds.</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="color: var(--color-text-light-muted); font-size: 0.82rem;">Sin datos de envíos.</p>
                @endif

                <!-- Top No Existen -->
                <h5 style="font-weight: 700; font-size: 0.9rem; margin-bottom: 10px; margin-top: 20px; border-bottom: 1px solid var(--border-light); padding-bottom: 5px;">
                    🚫 Repuestos solicitados que no existen
                </h5>
                @if($topNoExisten->count() > 0)
                    <table class="table" style="font-size: 0.82rem; background-color: #fafafa;">
                        <tbody>
                            @foreach($topNoExisten as $item)
                                <tr>
                                    <td style="font-weight: 600;">{{ $item->nombre_repuesto }}</td>
                                    <td style="text-align: right;"><span class="badge badge-black">{{ $item->total }} veces</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="color: var(--color-text-light-muted); font-size: 0.82rem;">Sin datos de inexistencias.</p>
                @endif
            </div>
        </div>

    </div>
</div>
