<div>
    @section('title', 'Seguimiento de Trabajos')

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
        <div>
            <h3 style="font-weight: 700; color: var(--color-text-dark); margin: 0;">🔍 Seguimiento por Técnico</h3>
            <span style="font-size: 0.85rem; color: var(--color-text-light-muted);">Órdenes activas, retrasadas y entregadas de cada trabajador</span>
        </div>
        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <span style="font-size: 0.82rem; color: var(--color-text-light-muted); white-space: nowrap;">Entregadas en:</span>
            @foreach([7 => '7 días', 15 => '15 días', 30 => '30 días', 90 => '90 días'] as $val => $label)
                <button wire:click="$set('filtroDias', {{ $val }})"
                    style="padding: 6px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; cursor: pointer;
                    border: 1px solid {{ $filtroDias == $val ? 'var(--color-red)' : '#dee2e6' }};
                    background-color: {{ $filtroDias == $val ? 'var(--color-red)' : '#fff' }};
                    color: {{ $filtroDias == $val ? '#fff' : '#495057' }};">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="stats-grid" style="margin-bottom: 25px;">
        <div class="stat-card" style="border-left: 4px solid #ff9800;">
            <div>
                <span class="stat-label">Órdenes Activas</span>
                <h3 class="stat-value">{{ $totalActivas }}</h3>
                <span style="font-size: 0.72rem; color: var(--color-text-light-muted);">En cualquier etapa del proceso</span>
            </div>
            <div class="stat-icon">⚙️</div>
        </div>
        <div class="stat-card" style="border-left: 4px solid var(--color-red); {{ $totalRetrasadas > 0 ? 'background-color: rgba(229,9,20,0.04);' : '' }}">
            <div>
                <span class="stat-label">Retrasadas (+{{ $umbralRetraso }} días)</span>
                <h3 class="stat-value" style="color: {{ $totalRetrasadas > 0 ? 'var(--color-red)' : '#28a745' }};">{{ $totalRetrasadas }}</h3>
                <span style="font-size: 0.72rem; color: var(--color-text-light-muted);">Sin entregar después de {{ $umbralRetraso }} días</span>
            </div>
            <div class="stat-icon">{{ $totalRetrasadas > 0 ? '⚠️' : '✅' }}</div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #28a745;">
            <div>
                <span class="stat-label">Entregadas (últimos {{ $filtroDias }}d)</span>
                <h3 class="stat-value">{{ $totalEntregadas }}</h3>
                <span style="font-size: 0.72rem; color: var(--color-text-light-muted);">Completadas en el período</span>
            </div>
            <div class="stat-icon">📦</div>
        </div>
    </div>

    <!-- ===== SECCIÓN POR TÉCNICO ===== -->
    <div class="card" style="margin-bottom: 25px;">
        <div class="card-header"><h4 class="card-title">👨‍🔧 Seguimiento por Técnico</h4></div>
        <div class="card-body" style="padding: 0;">
            @forelse($seguimientoTecnicos as $item)
                @php
                    $t = $item['tecnico'];
                    $expandido = $tecnicoExpandido == $t->id;
                @endphp
                <div style="border-bottom: 1px solid #f0f0f0;">
                    <!-- Technician header row (clickable) -->
                    <div wire:click="toggleTecnico({{ $t->id }})"
                         style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; cursor: pointer; transition: background-color 0.15s;"
                         onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor=''">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span style="font-size: 1.2rem; transition: transform 0.2s; {{ $expandido ? 'transform: rotate(90deg);' : '' }}">▶</span>
                            <div>
                                <strong style="display: block; font-size: 0.95rem;">{{ $t->nombre_completo }}</strong>
                                <span style="font-size: 0.75rem; color: var(--color-text-light-muted);">{{ $t->sucursal->nombre ?? 'Sin sucursal' }}</span>
                            </div>
                        </div>
                        <div style="display: flex; gap: 15px; align-items: center;">
                            <span style="font-size: 0.8rem; color: var(--color-text-light-muted);">
                                Activas: <strong style="color: #ff9800;">{{ $item['activas_count'] }}</strong>
                            </span>
                            @if($item['retrasadas_count'] > 0)
                                <span class="badge badge-red" style="font-size: 0.75rem;">⚠️ {{ $item['retrasadas_count'] }} retrasada(s)</span>
                            @endif
                            <span style="font-size: 0.8rem; color: var(--color-text-light-muted);">
                                Entregadas: <strong style="color: #28a745;">{{ $item['entregadas_count'] }}</strong>
                            </span>
                        </div>
                    </div>

                    <!-- Expanded detail -->
                    @if($expandido)
                        <div style="padding: 0 20px 20px 20px; background-color: #fafafa;">
                            <!-- Active orders -->
                            <div style="margin-bottom: 15px;">
                                <h5 style="font-weight: 600; font-size: 0.9rem; margin: 12px 0 10px 0; color: #ff9800;">
                                    ⚙️ Órdenes Activas ({{ $item['activas_count'] }})
                                </h5>
                                @if($item['activas']->count() > 0)
                                    <div class="table-responsive" style="margin-top: 0;">
                                        <table class="table" style="font-size: 0.82rem;">
                                            <thead>
                                                <tr>
                                                    <th>Ticket</th>
                                                    <th>Cliente</th>
                                                    <th>Equipo</th>
                                                    <th style="text-align: center;">Estado</th>
                                                    <th style="text-align: center;">Días</th>
                                                    <th style="text-align: right;">Costo Est.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($item['activas'] as $orden)
                                                    <tr style="{{ $orden->es_retrasado ? 'background-color: rgba(229,9,20,0.06);' : '' }}">
                                                        <td>
                                                            <strong style="color: var(--color-text-dark);">{{ $orden->numero_ticket }}</strong>
                                                            @if($orden->es_retrasado)
                                                                <span class="badge badge-red" style="font-size: 0.65rem; padding: 2px 6px; margin-left: 4px;">RETRASADO</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $orden->cliente->nombre ?? 'N/A' }}</td>
                                                        <td>
                                                            <span style="font-size: 0.75rem;">{{ $orden->marca }} {{ $orden->modelo }}</span>
                                                            <span style="display: block; font-size: 0.7rem; color: var(--color-text-light-muted);">{{ ucfirst($orden->categoria) }}</span>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            @php
                                                                $estadoClase = match($orden->estado) {
                                                                    'por_diagnosticar' => 'badge-black',
                                                                    'diagnosticado', 'esperando_aprobacion' => 'badge-yellow',
                                                                    'en_reparacion' => 'badge-red',
                                                                    'reparado' => 'badge-green',
                                                                    default => 'badge-black'
                                                                };
                                                                $estadoTexto = match($orden->estado) {
                                                                    'por_diagnosticar' => 'Por diagnosticar',
                                                                    'diagnosticado' => 'Diagnosticado',
                                                                    'esperando_aprobacion' => 'Esperando aprob.',
                                                                    'en_reparacion' => 'En reparación',
                                                                    'reparado' => 'Reparado',
                                                                    default => $orden->estado
                                                                };
                                                            @endphp
                                                            <span class="badge {{ $estadoClase }}" style="font-size: 0.7rem;">{{ $estadoTexto }}</span>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <strong style="color: {{ $orden->dias_transcurridos > $umbralRetraso ? 'var(--color-red)' : ($orden->dias_transcurridos > 3 ? '#ff9800' : '#28a745') }};">
                                                                {{ $orden->dias_transcurridos }}
                                                            </strong>
                                                            <span style="font-size: 0.7rem; color: var(--color-text-light-muted);">días</span>
                                                        </td>
                                                        <td style="text-align: right; font-weight: 600;">Bs. {{ number_format($orden->costo_estimado ?? 0, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p style="color: var(--color-text-light-muted); font-size: 0.82rem; padding: 8px 0;">Sin órdenes activas.</p>
                                @endif
                            </div>

                            <!-- Delivered orders -->
                            <div>
                                <h5 style="font-weight: 600; font-size: 0.9rem; margin: 12px 0 10px 0; color: #28a745;">
                                    📦 Entregadas (últimos {{ $filtroDias }}d) — {{ $item['entregadas_count'] }}
                                </h5>
                                @if($item['entregadas']->count() > 0)
                                    <div class="table-responsive" style="margin-top: 0;">
                                        <table class="table" style="font-size: 0.82rem;">
                                            <thead>
                                                <tr>
                                                    <th>Ticket</th>
                                                    <th>Cliente</th>
                                                    <th>Equipo</th>
                                                    <th style="text-align: center;">Días totales</th>
                                                    <th style="text-align: center;">Entregado el</th>
                                                    <th style="text-align: right;">Costo Est.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($item['entregadas'] as $orden)
                                                    <tr>
                                                        <td><strong style="color: var(--color-text-dark);">{{ $orden->numero_ticket }}</strong></td>
                                                        <td>{{ $orden->cliente->nombre ?? 'N/A' }}</td>
                                                        <td>
                                                            <span style="font-size: 0.75rem;">{{ $orden->marca }} {{ $orden->modelo }}</span>
                                                            <span style="display: block; font-size: 0.7rem; color: var(--color-text-light-muted);">{{ ucfirst($orden->categoria) }}</span>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <strong style="color: {{ $orden->dias_hasta_entrega > $umbralRetraso ? 'var(--color-red)' : '#28a745' }};">
                                                                {{ $orden->dias_hasta_entrega }} días
                                                            </strong>
                                                        </td>
                                                        <td style="text-align: center; font-size: 0.75rem; color: var(--color-text-light-muted);">
                                                            {{ $orden->updated_at->format('d/m/Y') }}
                                                        </td>
                                                        <td style="text-align: right; font-weight: 600;">Bs. {{ number_format($orden->costo_estimado ?? 0, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p style="color: var(--color-text-light-muted); font-size: 0.82rem; padding: 8px 0;">Sin entregas en este período.</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div style="padding: 30px; text-align: center; color: var(--color-text-light-muted);">
                    No hay técnicos registrados en el sistema.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ===== RETRASADAS GLOBAL ===== -->
    <div class="card" style="margin-bottom: 25px;">
        <div class="card-header" style="background-color: {{ $totalRetrasadas > 0 ? 'rgba(229,9,20,0.06)' : '' }};">
            <h4 class="card-title" style="color: {{ $totalRetrasadas > 0 ? 'var(--color-red)' : '' }};">
                ⚠️ Todas las Órdenes Retrasadas (+{{ $umbralRetraso }} días sin entregar)
            </h4>
        </div>
        <div class="card-body" style="padding: 0;">
            @if($retrasadasGlobal->count() > 0)
                <div class="table-responsive">
                    <table class="table" style="font-size: 0.82rem;">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Cliente</th>
                                <th>Equipo</th>
                                <th>Técnico</th>
                                <th>Sucursal</th>
                                <th style="text-align: center;">Estado</th>
                                <th style="text-align: center;">Días</th>
                                <th style="text-align: center;">Ingresó</th>
                                <th style="text-align: right;">Costo Est.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($retrasadasGlobal as $orden)
                                <tr>
                                    <td><strong style="color: var(--color-red);">{{ $orden->numero_ticket }}</strong></td>
                                    <td>{{ $orden->cliente->nombre ?? 'N/A' }}</td>
                                    <td>
                                        <span style="font-size: 0.75rem;">{{ $orden->marca }} {{ $orden->modelo }}</span>
                                        <span style="display: block; font-size: 0.7rem; color: var(--color-text-light-muted);">{{ ucfirst($orden->categoria) }}</span>
                                    </td>
                                    <td>{{ $orden->tecnico->nombre_completo ?? 'Sin asignar' }}</td>
                                    <td>{{ $orden->sucursal->nombre ?? 'N/A' }}</td>
                                    <td style="text-align: center;">
                                        @php
                                            $estClase = match($orden->estado) {
                                                'por_diagnosticar' => 'badge-black',
                                                'diagnosticado', 'esperando_aprobacion' => 'badge-yellow',
                                                'en_reparacion' => 'badge-red',
                                                'reparado' => 'badge-green',
                                                default => 'badge-black'
                                            };
                                            $estTexto = match($orden->estado) {
                                                'por_diagnosticar' => 'Por diagnosticar',
                                                'diagnosticado' => 'Diagnosticado',
                                                'esperando_aprobacion' => 'Esperando aprob.',
                                                'en_reparacion' => 'En reparación',
                                                'reparado' => 'Reparado',
                                                default => $orden->estado
                                            };
                                        @endphp
                                        <span class="badge {{ $estClase }}" style="font-size: 0.7rem;">{{ $estTexto }}</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <strong style="color: var(--color-red); font-size: 1rem;">{{ $orden->dias_transcurridos }}</strong>
                                        <span style="font-size: 0.7rem; color: var(--color-red);">días</span>
                                    </td>
                                    <td style="text-align: center; font-size: 0.75rem; color: var(--color-text-light-muted);">
                                        {{ $orden->created_at->format('d/m/Y') }}
                                    </td>
                                    <td style="text-align: right; font-weight: 600;">Bs. {{ number_format($orden->costo_estimado ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding: 30px; text-align: center; color: #28a745;">
                    <span style="font-size: 1.5rem;">✅</span>
                    <p style="margin-top: 8px; font-weight: 600;">No hay órdenes retrasadas. ¡Todo al día!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ===== ENTREGADAS RECIENTES ===== -->
    <div class="card">
        <div class="card-header"><h4 class="card-title">📦 Órdenes Entregadas (últimos {{ $filtroDias }} días)</h4></div>
        <div class="card-body" style="padding: 0;">
            @if($entregadasRecientes->count() > 0)
                <div class="table-responsive">
                    <table class="table" style="font-size: 0.82rem;">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Cliente</th>
                                <th>Equipo</th>
                                <th>Técnico</th>
                                <th>Sucursal</th>
                                <th style="text-align: center;">Días totales</th>
                                <th style="text-align: center;">Entregado el</th>
                                <th style="text-align: right;">Costo Est.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entregadasRecientes as $orden)
                                <tr>
                                    <td><strong style="color: var(--color-text-dark);">{{ $orden->numero_ticket }}</strong></td>
                                    <td>{{ $orden->cliente->nombre ?? 'N/A' }}</td>
                                    <td>
                                        <span style="font-size: 0.75rem;">{{ $orden->marca }} {{ $orden->modelo }}</span>
                                        <span style="display: block; font-size: 0.7rem; color: var(--color-text-light-muted);">{{ ucfirst($orden->categoria) }}</span>
                                    </td>
                                    <td>{{ $orden->tecnico->nombre_completo ?? 'N/A' }}</td>
                                    <td>{{ $orden->sucursal->nombre ?? 'N/A' }}</td>
                                    <td style="text-align: center;">
                                        <strong style="color: {{ $orden->dias_hasta_entrega > $umbralRetraso ? 'var(--color-red)' : '#28a745' }};">
                                            {{ $orden->dias_hasta_entrega }} días
                                        </strong>
                                    </td>
                                    <td style="text-align: center; font-size: 0.75rem; color: var(--color-text-light-muted);">
                                        {{ $orden->updated_at->format('d/m/Y') }}
                                    </td>
                                    <td style="text-align: right; font-weight: 600;">Bs. {{ number_format($orden->costo_estimado ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding: 30px; text-align: center; color: var(--color-text-light-muted);">
                    Sin entregas en este período.
                </div>
            @endif
        </div>
    </div>
</div>
