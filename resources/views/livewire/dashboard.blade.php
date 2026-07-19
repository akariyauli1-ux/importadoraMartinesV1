<div>
    @section('title', 'Panel de Control - Importadora Martinez')

    <div class="card" style="border-left: 5px solid var(--color-red); background: linear-gradient(90deg, #ffffff 0%, #fafafa 100%);">
        <div class="card-body">
            <h3 style="font-weight: 700; font-size: 1.5rem; color: var(--color-text-dark);">
                ¡Bienvenido de nuevo, {{ auth()->user()->name }}!
            </h3>
            <p style="color: var(--color-text-light-muted); margin-top: 5px;">
                Hoy es {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}. Estás operando en el sistema de gestión del taller de electrónica.
            </p>
        </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="stats-grid">
        @if(auth()->user()->hasRole('Gerente'))
            <div class="stat-card">
                <div>
                    <span class="stat-label">Sucursales</span>
                    <h3 class="stat-value">{{ $stats['sucursales_count'] }}</h3>
                </div>
                <div class="stat-icon">🏢</div>
            </div>
            <div class="stat-card">
                <div>
                    <span class="stat-label">Personal RRHH</span>
                    <h3 class="stat-value">{{ $stats['empleados_count'] }}</h3>
                </div>
                <div class="stat-icon">👥</div>
            </div>
            <div class="stat-card">
                <div>
                    <span class="stat-label">Clientes</span>
                    <h3 class="stat-value">{{ $stats['clientes_count'] }}</h3>
                </div>
                <div class="stat-icon">🤝</div>
            </div>
            <div class="stat-card">
                <div>
                    <span class="stat-label">Presupuestado (Total)</span>
                    <h3 class="stat-value">Bs. {{ number_format($stats['presupuestos_estimados'], 2) }}</h3>
                </div>
                <div class="stat-icon">💰</div>
            </div>
        @endif

        @if(auth()->user()->hasRole('Administrador'))
            <div class="stat-card">
                <div>
                    <span class="stat-label">Órdenes Sede</span>
                    <h3 class="stat-value">{{ $stats['ordenes_totales'] }}</h3>
                </div>
                <div class="stat-icon">📁</div>
            </div>
            <div class="stat-card">
                <div>
                    <span class="stat-label">Por Diagnosticar</span>
                    <h3 class="stat-value">{{ $stats['ordenes_por_estado']['por_diagnosticar'] }}</h3>
                </div>
                <div class="stat-icon">🔍</div>
            </div>
            <div class="stat-card">
                <div>
                    <span class="stat-label">En Reparación</span>
                    <h3 class="stat-value">{{ $stats['ordenes_por_estado']['en_reparacion'] }}</h3>
                </div>
                <div class="stat-icon">🔧</div>
            </div>
        @endif

        @if(auth()->user()->hasRole('Técnico'))
            <div class="stat-card" style="{{ ($stats['mis_activas'] ?? 0) >= 4 ? 'border-left: 4px solid var(--color-red);' : '' }}">
                <div>
                    <span class="stat-label">Órdenes Activas</span>
                    <h3 class="stat-value">{{ $stats['mis_activas'] ?? 0 }} / 4</h3>
                    @if(($stats['mis_activas'] ?? 0) >= 4)
                        <span style="color: var(--color-red); font-size: 0.75rem; font-weight: 600;">⚠️ Capacidad máxima</span>
                    @endif
                </div>
                <div class="stat-icon">⚙️</div>
            </div>
            <div class="stat-card">
                <div>
                    <span class="stat-label">Reparados</span>
                    <h3 class="stat-value">{{ $stats['mis_reparadas'] ?? 0 }}</h3>
                </div>
                <div class="stat-icon">✅</div>
            </div>
            <div class="stat-card">
                <div>
                    <span class="stat-label">Entregados</span>
                    <h3 class="stat-value">{{ $stats['mis_entregadas'] ?? 0 }}</h3>
                </div>
                <div class="stat-icon">📦</div>
            </div>
        @endif

        @if(auth()->user()->hasRole('Recepcionista'))
            <div class="stat-card">
                <div>
                    <span class="stat-label">Ingresados hoy</span>
                    <h3 class="stat-value">{{ $stats['mis_ingresos_hoy'] }}</h3>
                </div>
                <div class="stat-icon">📥</div>
            </div>
            <div class="stat-card">
                <div>
                    <span class="stat-label">Pendientes Entrega</span>
                    <h3 class="stat-value">{{ $stats['ordenes_por_estado']['reparado'] }}</h3>
                </div>
                <div class="stat-icon">📦</div>
            </div>
        @endif

        @if(auth()->user()->hasRole('Almacenista'))
            <div class="stat-card">
                <div>
                    <span class="stat-label">Repuestos Enviados</span>
                    <h3 class="stat-value">{{ $stats['solicitudes_enviadas'] ?? 0 }}</h3>
                </div>
                <div class="stat-icon">📦</div>
            </div>
            <div class="stat-card">
                <div>
                    <span class="stat-label">Pendientes</span>
                    <h3 class="stat-value">{{ $stats['solicitudes_pendientes'] ?? 0 }}</h3>
                </div>
                <div class="stat-icon">⏳</div>
            </div>
            <div class="stat-card">
                <div>
                    <span class="stat-label">Agotados</span>
                    <h3 class="stat-value">{{ $stats['solicitudes_agotadas'] ?? 0 }}</h3>
                </div>
                <div class="stat-icon">📭</div>
            </div>
            <div class="stat-card">
                <div>
                    <span class="stat-label">No Existen</span>
                    <h3 class="stat-value">{{ $stats['solicitudes_no_existen'] ?? 0 }}</h3>
                </div>
                <div class="stat-icon">🚫</div>
            </div>
        @endif
    </div>

    <!-- ==================== CHARTS SECTION ==================== -->

    <!-- Bar Chart: Órdenes por Estado (all roles) -->
    <div class="card" style="margin-top: 25px;">
        <div class="card-header">
            <h4 class="card-title">📊 Órdenes por Estado</h4>
        </div>
        <div class="card-body" style="max-height: 280px;">
            <canvas id="chartEstados" height="60"></canvas>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-top: 25px;">
        
        @if(auth()->user()->hasRole('Gerente'))
            <!-- Bar: Órdenes por Sucursal -->
            <div class="card">
                <div class="card-header"><h4 class="card-title">🏢 Órdenes por Sucursal</h4></div>
                <div class="card-body" style="max-height: 280px;"><canvas id="chartSucursales" height="60"></canvas></div>
            </div>
            <!-- Doughnut: Órdenes por Categoría -->
            <div class="card">
                <div class="card-header"><h4 class="card-title">📱 Órdenes por Categoría</h4></div>
                <div class="card-body" style="max-height: 280px;"><canvas id="chartCategorias" height="60"></canvas></div>
            </div>
        @endif

        @if(auth()->user()->hasRole('Administrador'))
            <!-- Bar: Carga por Técnico -->
            <div class="card">
                <div class="card-header"><h4 class="card-title">🔧 Carga de Trabajo por Técnico</h4></div>
                <div class="card-body" style="max-height: 280px;"><canvas id="chartTecnicosCarga" height="60"></canvas></div>
            </div>
            <!-- Bar: Órdenes por Categoría en Sede -->
            <div class="card">
                <div class="card-header"><h4 class="card-title">📱 Órdenes por Categoría en Sede</h4></div>
                <div class="card-body" style="max-height: 280px;"><canvas id="chartCatSede" height="60"></canvas></div>
            </div>
        @endif

        @if(auth()->user()->hasRole('Técnico'))
            <!-- Doughnut: Mis tareas -->
            <div class="card">
                <div class="card-header"><h4 class="card-title">⚙️ Distribución de Mis Tareas</h4></div>
                <div class="card-body" style="max-height: 280px;"><canvas id="chartMisTareas" height="60"></canvas></div>
            </div>
            <!-- Bar: Tareas por tipo -->
            <div class="card">
                <div class="card-header"><h4 class="card-title">📋 Tareas por Tipo</h4></div>
                <div class="card-body" style="max-height: 280px;"><canvas id="chartCatSede" height="60"></canvas></div>
            </div>
        @endif

        @if(auth()->user()->hasRole('Recepcionista'))
            <!-- Bar: Ingresos de la semana -->
            <div class="card">
                <div class="card-header"><h4 class="card-title">📥 Ingresos de Esta Semana</h4></div>
                <div class="card-body" style="max-height: 280px;"><canvas id="chartRecepcionSemanal" height="60"></canvas></div>
            </div>
            <!-- Doughnut: Categorías -->
            <div class="card">
                <div class="card-header"><h4 class="card-title">📱 Categorías en Sede</h4></div>
                <div class="card-body" style="max-height: 280px;"><canvas id="chartCatSede" height="60"></canvas></div>
            </div>
        @endif

        @if(auth()->user()->hasRole('Almacenista'))
            <!-- Doughnut: Solicitudes -->
            <div class="card">
                <div class="card-header"><h4 class="card-title">📦 Solicitudes de Repuestos</h4></div>
                <div class="card-body" style="max-height: 280px;"><canvas id="chartSolicitudes" height="60"></canvas></div>
            </div>
            <!-- Bar: Top repuestos -->
            <div class="card">
                <div class="card-header"><h4 class="card-title">🔝 Repuestos Más Solicitados</h4></div>
                <div class="card-body" style="max-height: 280px;"><canvas id="chartTopRepuestos" height="60"></canvas></div>
            </div>
        @endif
    </div>

    @if(auth()->user()->hasRole('Gerente'))
    <!-- Charts Row 2 - Gerente only -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 25px; margin-top: 25px;">
        <div class="card">
            <div class="card-header"><h4 class="card-title">📈 Ingresos vs Entregados (Últimos 6 meses)</h4></div>
            <div class="card-body" style="max-height: 250px;"><canvas id="chartMensual" height="60"></canvas></div>
        </div>
    </div>
    @endif

    @if(auth()->user()->hasRole('Técnico'))
    <!-- Técnico: Monthly trend -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 25px; margin-top: 25px;">
        <div class="card">
            <div class="card-header"><h4 class="card-title">📈 Tareas Asignadas vs Completadas (Últimos 6 meses)</h4></div>
            <div class="card-body" style="max-height: 250px;"><canvas id="chartTecnicoMensual" height="60"></canvas></div>
        </div>
    </div>
    @endif

    <!-- Status Overview Card -->
    <div class="card" style="margin-top: 25px;">
        <div class="card-header">
            <h4 class="card-title">📋 Resumen Detallado de Estados</h4>
        </div>
        <div class="card-body">
            <div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); margin-bottom: 0;">
                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; text-align: center;">
                    <span class="badge badge-black">Por Diagnosticar</span>
                    <h2 style="margin-top: 10px; font-weight: 700;">{{ $stats['ordenes_por_estado']['por_diagnosticar'] }}</h2>
                </div>
                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; text-align: center;">
                    <span class="badge badge-yellow">Diagnosticado</span>
                    <h2 style="margin-top: 10px; font-weight: 700;">{{ $stats['ordenes_por_estado']['diagnosticado'] }}</h2>
                </div>
                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; text-align: center;">
                    <span class="badge badge-yellow">Esperando Aprobación</span>
                    <h2 style="margin-top: 10px; font-weight: 700;">{{ $stats['ordenes_por_estado']['esperando_aprobacion'] }}</h2>
                </div>
                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; text-align: center;">
                    <span class="badge badge-red">En Reparación</span>
                    <h2 style="margin-top: 10px; font-weight: 700;">{{ $stats['ordenes_por_estado']['en_reparacion'] }}</h2>
                </div>
                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; text-align: center;">
                    <span class="badge badge-green">Reparado</span>
                    <h2 style="margin-top: 10px; font-weight: 700;">{{ $stats['ordenes_por_estado']['reparado'] }}</h2>
                </div>
                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; text-align: center;">
                    <span class="badge badge-green" style="background-color: #e2f0d9; color: #385723;">Entregado</span>
                    <h2 style="margin-top: 10px; font-weight: 700;">{{ $stats['ordenes_por_estado']['entregado'] }}</h2>
                </div>
                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; text-align: center;">
                    <span class="badge badge-black" style="background-color: #f2f2f2; color: #7f7f7f;">Rechazado</span>
                    <h2 style="margin-top: 10px; font-weight: 700;">{{ $stats['ordenes_por_estado']['rechazado'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('livewire:navigated', function () {
        initCharts();
    });
    document.addEventListener('DOMContentLoaded', function () {
        initCharts();
    });

    function initCharts() {
        const primary = '#e50914';
        const colors = ['#6c757d','#ffc107','#ff9800','#e50914','#28a745','#17a2b8','#adb5bd'];
        const labels = @json($statesLabels);
        const values = @json($statesValues);

        // ===== Chart 1: Órdenes por Estado (all) =====
        const ctx1 = document.getElementById('chartEstados');
        if (ctx1) {
            if (ctx1._chart) ctx1._chart.destroy();
            ctx1._chart = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Órdenes',
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 0,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, title: { display: true, text: 'Órdenes por Estado' } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });
        }

        @if(auth()->user()->hasRole('Gerente'))
        // ===== Chart: Órdenes por Sucursal =====
        (function(){
            const ctx = document.getElementById('chartSucursales');
            if(!ctx) return;
            const data = @json($charts['ordenes_por_sucursal'] ?? []);
            if(ctx._chart) ctx._chart.destroy();
            ctx._chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.label),
                    datasets: [{
                        label: 'Órdenes',
                        data: data.map(d => d.value),
                        backgroundColor: '#e50914',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, title: { display: true, text: 'Órdenes por Sucursal' } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        })();

        // ===== Chart: Órdenes por Categoría (Gerente Doughnut) =====
        (function(){
            const ctx = document.getElementById('chartCategorias');
            if(!ctx) return;
            const data = @json($charts['ordenes_por_categoria'] ?? []);
            const colCat = ['#28a745','#17a2b8','#ff9800','#e50914'];
            if(ctx._chart) ctx._chart.destroy();
            ctx._chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.map(d => d.label),
                    datasets: [{
                        data: data.map(d => d.value),
                        backgroundColor: colCat.slice(0, data.length),
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true } },
                        title: { display: true, text: 'Órdenes por Categoría' }
                    }
                }
            });
        })();

        // ===== Chart: Ingresos mensuales =====
        (function(){
            const ctx = document.getElementById('chartMensual');
            if(!ctx) return;
            const data = @json($charts['ingresos_por_mes'] ?? []);
            if(ctx._chart) ctx._chart.destroy();
            ctx._chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels || [],
                    datasets: [
                        {
                            label: 'Ingresos',
                            data: data.total || [],
                            borderColor: '#e50914',
                            backgroundColor: 'rgba(229,9,20,0.1)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 5,
                        },
                        {
                            label: 'Entregados',
                            data: data.entregados || [],
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40,167,69,0.1)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 5,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' }, title: { display: true, text: 'Ingresos vs Entregados' } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        })();
        @endif

        @if(auth()->user()->hasRole('Administrador'))
        // ===== Chart: Carga por Técnico =====
        (function(){
            const ctx = document.getElementById('chartTecnicosCarga');
            if(!ctx) return;
            const data = @json($charts['tecnicos_carga'] ?? []);
            if(ctx._chart) ctx._chart.destroy();
            if(data.labels && data.labels.length > 0) {
                ctx._chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            { label: 'Activas', data: data.activas, backgroundColor: '#e50914', borderRadius: 4 },
                            { label: 'Completadas', data: data.completadas, backgroundColor: '#28a745', borderRadius: 4 },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' }, title: { display: true, text: 'Carga de Trabajo por Técnico' } },
                        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } } }
                    }
                });
            }
        })();
        @endif

        @if(auth()->user()->hasRole('Administrador') || auth()->user()->hasRole('Recepcionista') || auth()->user()->hasRole('Técnico'))
        // ===== Chart: Categorías en sede (bar) =====
        (function(){
            const ctx = document.getElementById('chartCatSede');
            if(!ctx) return;
            const labels = @json($charts['categorias_labels'] ?? []);
            const values = @json($charts['categorias_total'] ?? []);
            const colCat = ['#28a745','#17a2b8','#ff9800','#e50914'];
            if(ctx._chart) ctx._chart.destroy();
            ctx._chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Órdenes',
                        data: values,
                        backgroundColor: colCat,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, title: { display: true, text: 'Órdenes por Categoría' } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        })();
        @endif

        @if(auth()->user()->hasRole('Técnico'))
        // ===== Chart: Mis Tareas (doughnut) =====
        (function(){
            const ctx = document.getElementById('chartMisTareas');
            if(!ctx) return;
            const labels = @json($charts['tecnico_tareas_labels'] ?? []);
            const values = @json($charts['tecnico_tareas_valores'] ?? []);
            const col = @json($charts['tecnico_tareas_colores'] ?? []);
            if(ctx._chart) ctx._chart.destroy();
            ctx._chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: col,
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } }, title: { display: true, text: 'Distribución de Mis Tareas' } }
                }
            });
        })();

        // ===== Chart: Técnico mensual =====
        (function(){
            const ctx = document.getElementById('chartTecnicoMensual');
            if(!ctx) return;
            const data = @json($charts['tecnico_mensual'] ?? []);
            if(ctx._chart) ctx._chart.destroy();
            if(data.labels && data.labels.length > 0) {
                ctx._chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Asignadas',
                                data: data.total,
                                borderColor: '#e50914',
                                backgroundColor: 'rgba(229,9,20,0.1)',
                                fill: true,
                                tension: 0.3,
                                pointRadius: 5,
                            },
                            {
                                label: 'Completadas',
                                data: data.completadas,
                                borderColor: '#28a745',
                                backgroundColor: 'rgba(40,167,69,0.1)',
                                fill: true,
                                tension: 0.3,
                                pointRadius: 5,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' }, title: { display: true, text: 'Tareas Asignadas vs Completadas' } },
                        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                    }
                });
            }
        })();
        @endif

        @if(auth()->user()->hasRole('Recepcionista'))
        // ===== Chart: Ingresos semanales =====
        (function(){
            const ctx = document.getElementById('chartRecepcionSemanal');
            if(!ctx) return;
            const data = @json($charts['recepcion_semanal'] ?? []);
            if(ctx._chart) ctx._chart.destroy();
            ctx._chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        label: 'Ingresos',
                        data: data.valores || [],
                        backgroundColor: '#e50914',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, title: { display: true, text: 'Ingresos de Esta Semana' } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        })();
        @endif

        @if(auth()->user()->hasRole('Almacenista'))
        // ===== Chart: Solicitudes (doughnut) =====
        (function(){
            const ctx = document.getElementById('chartSolicitudes');
            if(!ctx) return;
            const labels = @json($charts['solicitudes_labels'] ?? []);
            const values = @json($charts['solicitudes_valores'] ?? []);
            const col = @json($charts['solicitudes_colores'] ?? []);
            if(ctx._chart) ctx._chart.destroy();
            ctx._chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: col,
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } }, title: { display: true, text: 'Solicitudes de Repuestos' } }
                }
            });
        })();

        // ===== Chart: Top repuestos =====
        (function(){
            const ctx = document.getElementById('chartTopRepuestos');
            if(!ctx) return;
            const labels = @json($charts['top_repuestos_labels'] ?? []);
            const values = @json($charts['top_repuestos_valores'] ?? []);
            if(ctx._chart) ctx._chart.destroy();
            ctx._chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Solicitudes',
                        data: values,
                        backgroundColor: '#e50914',
                        borderRadius: 6,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, title: { display: true, text: 'Repuestos Más Solicitados' } },
                    scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        })();
        @endif
    }
    </script>
    @endpush
</div>
