<div>
    @section('title', 'Reportes de Rendimiento')

    <!-- Header with filters -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
        <div>
            <h3 style="font-weight: 700; color: var(--color-text-dark); margin: 0;">📊 Rendimiento del Taller</h3>
            <span style="font-size: 0.85rem; color: var(--color-text-light-muted);">Comparativa por sucursal y técnico</span>
        </div>
        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <span style="font-size: 0.82rem; color: var(--color-text-light-muted); white-space: nowrap;">Período:</span>
            @foreach([7 => '7 días', 15 => '15 días', 30 => '30 días', 90 => '90 días', 365 => '1 año'] as $val => $label)
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

    <!-- ===== GRAFICO 1: Sucursales comparativa ===== -->
    <div class="card" style="margin-bottom: 25px;">
        <div class="card-header"><h4 class="card-title">🏢 Órdenes por Sucursal: Entregadas vs En Progreso</h4></div>
        <div class="card-body" style="max-height: 300px;"><canvas id="chartSucursales" height="60"></canvas></div>
    </div>

    <!-- ===== TABLA 1: Ranking Sucursales ===== -->
    <div class="card" style="margin-bottom: 25px;">
        <div class="card-header"><h4 class="card-title">🏆 Ranking de Sucursales</h4></div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th>Sucursal</th>
                            <th style="text-align: center;">Técnicos</th>
                            <th style="text-align: center;">Total Órdenes</th>
                            <th style="text-align: center;">En Progreso</th>
                            <th style="text-align: center;">Entregadas</th>
                            <th style="text-align: center;">Tasa Éxito</th>
                            <th style="text-align: right;">Presupuestado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rendimientoSucursales as $i => $suc)
                            <tr style="{{ $i < 3 ? 'background-color: rgba(229,9,20,0.03);' : '' }}">
                                <td style="font-weight: 800; color: {{ $i == 0 ? '#ff9800' : ($i == 1 ? '#adb5bd' : ($i == 2 ? '#cd7f32' : '#6c757d')) }}; font-size: 1.1rem;">
                                    {{ $i + 1 }}
                                </td>
                                <td>
                                    <strong style="display: block;">{{ $suc['nombre'] }}</strong>
                                    <span style="font-size: 0.75rem; color: var(--color-text-light-muted);">{{ $suc['direccion'] }}</span>
                                </td>
                                <td style="text-align: center;">{{ $suc['tecnicos'] }}</td>
                                <td style="text-align: center; font-weight: 600;">{{ $suc['total'] }}</td>
                                <td style="text-align: center;">
                                    <span class="badge badge-yellow">{{ $suc['en_progreso'] }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge badge-green">{{ $suc['entregadas'] }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-weight: 700; color: {{ $suc['tasa_exito'] >= 70 ? '#28a745' : ($suc['tasa_exito'] >= 40 ? '#ff9800' : '#dc3545') }};">
                                        {{ $suc['tasa_exito'] }}%
                                    </span>
                                    <div style="height: 4px; background-color: #e9ecef; border-radius: 2px; margin-top: 3px; width: 60px; margin-left: auto; margin-right: auto;">
                                        <div style="height: 100%; width: {{ $suc['tasa_exito'] }}%; background-color: {{ $suc['tasa_exito'] >= 70 ? '#28a745' : ($suc['tasa_exito'] >= 40 ? '#ff9800' : '#dc3545') }}; border-radius: 2px;"></div>
                                    </div>
                                </td>
                                <td style="text-align: right; font-weight: 600; color: var(--color-red);">Bs. {{ number_format($suc['presupuestado'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== GRAFICO 2: Tasa de éxito por sucursal ===== -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; margin-bottom: 25px;">
        <div class="card">
            <div class="card-header"><h4 class="card-title">📈 Tasa de Éxito por Sucursal (%)</h4></div>
            <div class="card-body" style="max-height: 350px;"><canvas id="chartTasaExito" height="60"></canvas></div>
        </div>
        <div class="card">
            <div class="card-header"><h4 class="card-title">🔧 Órdenes por Técnico (Top 15)</h4></div>
            <div class="card-body" style="max-height: 350px;"><canvas id="chartTecnicos" height="60"></canvas></div>
        </div>
    </div>

    <!-- ===== TABLA 2: Ranking Técnicos ===== -->
    <div class="card">
        <div class="card-header"><h4 class="card-title">🏆 Ranking de Técnicos</h4></div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th>Técnico</th>
                            <th>Sucursal</th>
                            <th style="text-align: center;">Asignadas</th>
                            <th style="text-align: center;">Completadas</th>
                            <th style="text-align: center;">En Progreso</th>
                            <th style="text-align: center;">Tasa Éxito</th>
                            <th style="text-align: center;">Promedio Días</th>
                            <th style="text-align: right;">Presupuestado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rendimientoTecnicos as $i => $tec)
                            <tr style="{{ $i < 3 ? 'background-color: rgba(229,9,20,0.03);' : '' }}">
                                <td style="font-weight: 800; color: {{ $i == 0 ? '#ff9800' : ($i == 1 ? '#adb5bd' : ($i == 2 ? '#cd7f32' : '#6c757d')) }}; font-size: 1.1rem;">
                                    {{ $i + 1 }}
                                </td>
                                <td>
                                    <strong style="display: block;">{{ $tec['nombre'] }}</strong>
                                </td>
                                <td><span style="font-size: 0.8rem; color: var(--color-text-light-muted);">{{ $tec['sucursal'] }}</span></td>
                                <td style="text-align: center; font-weight: 600;">{{ $tec['asignadas'] }}</td>
                                <td style="text-align: center;">
                                    <span class="badge badge-green">{{ $tec['completadas'] }}</span>
                                </td>
                                <td style="text-align: center;">
                                    @if($tec['en_progreso'] >= 4)
                                        <span class="badge badge-red" title="Capacidad máxima">{{ $tec['en_progreso'] }} ⚠️</span>
                                    @else
                                        <span class="badge badge-yellow">{{ $tec['en_progreso'] }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-weight: 700; color: {{ $tec['tasa_exito'] >= 70 ? '#28a745' : ($tec['tasa_exito'] >= 40 ? '#ff9800' : '#dc3545') }};">
                                        {{ $tec['tasa_exito'] }}%
                                    </span>
                                </td>
                                <td style="text-align: center; font-size: 0.85rem;">
                                    {{ $tec['promedio_dias'] !== null ? $tec['promedio_dias'] . ' días' : 'N/A' }}
                                </td>
                                <td style="text-align: right; font-weight: 600; color: var(--color-red);">Bs. {{ number_format($tec['presupuestado'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 30px; color: var(--color-text-light-muted);">No hay datos de técnicos disponibles.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () { initReportes(); });
    document.addEventListener('livewire:navigated', function () { initReportes(); });

    function initReportes() {
        // Chart 1: Sucursales comparativa (bar)
        (function(){
            const ctx = document.getElementById('chartSucursales');
            if(!ctx) return;
            if(ctx._chart) ctx._chart.destroy();
            ctx._chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartSucursalesLabels),
                    datasets: [
                        { label: 'Total', data: @json($chartSucursalesTotal), backgroundColor: '#6c757d', borderRadius: 4 },
                        { label: 'En Progreso', data: @json($chartSucursalesEnProgreso), backgroundColor: '#ff9800', borderRadius: 4 },
                        { label: 'Entregadas', data: @json($chartSucursalesEntregadas), backgroundColor: '#28a745', borderRadius: 4 },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' }, title: { display: true, text: 'Órdenes por Sucursal' } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        })();

        // Chart 2: Tasa éxito por sucursal (bar horizontal)
        (function(){
            const ctx = document.getElementById('chartTasaExito');
            if(!ctx) return;
            if(ctx._chart) ctx._chart.destroy();
            ctx._chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartSucursalTasaLabels),
                    datasets: [{
                        label: '% Éxito',
                        data: @json($chartSucursalTasaValores),
                        backgroundColor: @json($chartSucursalTasaValores).map(v => v >= 70 ? '#28a745' : v >= 40 ? '#ff9800' : '#dc3545'),
                        borderRadius: 6,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8,
                    plugins: { legend: { display: false }, title: { display: true, text: 'Tasa de Éxito por Sucursal' } },
                    scales: { x: { max: 100, beginAtZero: true, ticks: { callback: v => v + '%' } } }
                }
            });
        })();

        // Chart 3: Técnicos (horizontal stacked bar)
        (function(){
            const ctx = document.getElementById('chartTecnicos');
            if(!ctx) return;
            if(ctx._chart) ctx._chart.destroy();
            ctx._chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartTecnicosLabels),
                    datasets: [
                        { label: 'Completadas', data: @json($chartTecnicosCompletadas), backgroundColor: '#28a745', borderRadius: 4 },
                        { label: 'En Progreso', data: @json($chartTecnicosEnProgreso), backgroundColor: '#ff9800', borderRadius: 4 },
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8,
                    plugins: { legend: { position: 'bottom' }, title: { display: true, text: 'Órdenes por Técnico' } },
                    scales: { x: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }, y: { stacked: true } }
                }
            });
        })();
    }
    </script>
    @endpush
</div>
