<div>
    @section('title', 'Panel de Control - Importadora Martinez')

    <!-- Welcome Card -->
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
            <div class="stat-card" style="{{ $stats['mis_activas'] >= 4 ? 'border-left: 4px solid var(--color-red);' : '' }}">
                <div>
                    <span class="stat-label">Mis Órdenes Activas</span>
                    <h3 class="stat-value">{{ $stats['mis_activas'] }} / 4</h3>
                    @if($stats['mis_activas'] >= 4)
                        <span style="color: var(--color-red); font-size: 0.75rem; font-weight: 600;">⚠️ Capacidad máxima alcanzada</span>
                    @endif
                </div>
                <div class="stat-icon">⚙️</div>
            </div>
            <div class="stat-card">
                <div>
                    <span class="stat-label">Mis Reparados</span>
                    <h3 class="stat-value">{{ $stats['mis_reparadas'] }}</h3>
                </div>
                <div class="stat-icon">✅</div>
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
    </div>

    <!-- Status Overview Card -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Resumen de Estado de Órdenes</h4>
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
</div>
