<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importadora Martinez | Taller</title>
    <!-- Custom Premium CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <!-- Alpine JS is loaded by Livewire, so we can write basic Alpine logic if needed -->
    @livewireStyles
</head>
<body class="bg-light">

    <div class="app-container" x-data="{ mobileSidebarOpen: false }">
        
        <!-- Sidebar overlay -->
        <div class="sidebar-overlay" x-show="mobileSidebarOpen" @click="mobileSidebarOpen = false" x-transition.opacity></div>

        <!-- Sidebar -->
        <aside class="sidebar" :class="{ 'open': mobileSidebarOpen }">
            <div class="sidebar-header">
                @php
                    $empConfig = \App\Models\ConfiguracionEmpresa::first();
                @endphp
                @if($empConfig && $empConfig->logo_path)
                    <img src="{{ asset('storage/' . $empConfig->logo_path) }}" class="sidebar-logo" alt="Logo">
                @else
                    <div style="font-weight: 800; font-size: 1.2rem; color: #fff; border-left: 4px solid var(--color-red); padding-left: 8px;">
                        IMPORTADORA
                        <span style="color: var(--color-red);">MARTINEZ</span>
                    </div>
                @endif
            </div>

            <!-- Profile Info -->
            @auth
            <div class="sidebar-profile">
                @if(auth()->user()->foto_path)
                    <img src="{{ asset('storage/' . auth()->user()->foto_path) }}" class="profile-avatar" alt="Foto">
                @else
                    <!-- Default avatar based on red theme -->
                    <div style="width: 48px; height: 48px; border-radius: 50%; background-color: var(--color-red); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; border: 2px solid #fff;">
                        {{ substr(auth()->user()->name, 0, 1) }}{{ substr(auth()->user()->apellido_paterno, 0, 1) }}
                    </div>
                @endif
                <div class="profile-info">
                    <div class="profile-name" title="{{ auth()->user()->nombre_completo }}">
                        {{ auth()->user()->name }} {{ auth()->user()->apellido_paterno }}
                    </div>
                    <div class="profile-role">
                        {{ auth()->user()->getRoleNames()->first() ?? 'Sin Rol' }}
                    </div>
                </div>
            </div>
            @endauth

            <!-- Navigation -->
            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" @click="mobileSidebarOpen = false">
                            📊 Dashboard
                        </a>
                    </li>

                    @if(auth()->user()->hasRole('Gerente'))
                        <li class="nav-item">
                            <a href="{{ route('gerente.sucursales') }}" class="nav-link {{ request()->routeIs('gerente.sucursales') ? 'active' : '' }}" @click="mobileSidebarOpen = false">
                                🏢 Sucursales
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('gerente.rrhh') }}" class="nav-link {{ request()->routeIs('gerente.rrhh') ? 'active' : '' }}" @click="mobileSidebarOpen = false">
                                👥 Personal (RRHH)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('gerente.reportes') }}" class="nav-link {{ request()->routeIs('gerente.reportes') ? 'active' : '' }}" @click="mobileSidebarOpen = false">
                                📊 Reportes y Rendimiento
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('gerente.seguimiento') }}" class="nav-link {{ request()->routeIs('gerente.seguimiento') ? 'active' : '' }}" @click="mobileSidebarOpen = false">
                                🔍 Seguimiento Técnicos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('gerente.branding') }}" class="nav-link {{ request()->routeIs('gerente.branding') ? 'active' : '' }}" @click="mobileSidebarOpen = false">
                                🎨 Identidad / Branding
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->hasRole('Administrador'))
                        <li class="nav-item">
                            <a href="{{ route('admin.checklist') }}" class="nav-link {{ request()->routeIs('admin.checklist') ? 'active' : '' }}" @click="mobileSidebarOpen = false">
                                📋 Checklist Diario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.cola') }}" class="nav-link {{ request()->routeIs('admin.cola') ? 'active' : '' }}" @click="mobileSidebarOpen = false">
                                ⏳ Cola de Trabajo
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->hasRole('Recepcionista'))
                        <li class="nav-item">
                            <a href="{{ route('recepcion.checkin') }}" class="nav-link {{ request()->routeIs('recepcion.checkin') ? 'active' : '' }}" @click="mobileSidebarOpen = false">
                                📥 Nueva Orden (Check-in)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('recepcion.ordenes') }}" class="nav-link {{ request()->routeIs('recepcion.ordenes') ? 'active' : '' }}" @click="mobileSidebarOpen = false">
                                📁 Órdenes del Taller
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->hasRole('Técnico'))
                        <li class="nav-item">
                            <a href="{{ route('tecnico.tareas') }}" class="nav-link {{ request()->routeIs('tecnico.tareas') ? 'active' : '' }}" @click="mobileSidebarOpen = false">
                                🔧 Mis Tareas Asignadas
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->hasRole('Almacenista'))
                        <li class="nav-item">
                            <a href="{{ route('almacen.solicitudes') }}" class="nav-link {{ request()->routeIs('almacen.solicitudes') ? 'active' : '' }}" @click="mobileSidebarOpen = false">
                                📦 Gestión de Repuestos
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">
                        🚪 Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            
            <!-- Topbar -->
            <header class="topbar">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <!-- Menu button for mobile -->
                    <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="hamburger-btn" aria-label="Menú">
                        <span x-text="mobileSidebarOpen ? '✕' : '☰'"></span>
                    </button>
                    <h2 class="topbar-title">
                        @yield('title', 'Taller de Electrónica')
                    </h2>
                </div>

                <div class="topbar-actions">
                    @auth
                        @if(auth()->user()->hasAnyRole(['Administrador', 'Técnico', 'Recepcionista']))
                            @livewire('notificaciones-repuesto')
                        @endif
                        @if(auth()->user()->sucursal)
                            <span class="branch-badge">
                                📍 {{ auth()->user()->sucursal->nombre }}
                            </span>
                        @else
                            <span class="branch-badge" style="background-color: var(--color-red);">
                                📍 Sede Central (Corporativo)
                            </span>
                        @endif
                    @endauth
                </div>
            </header>

            <!-- Body Content -->
            <main class="content-body">
                @if (session()->has('success'))
                    <div class="alert alert-success">
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if (session()->has('warning'))
                    <div class="alert alert-warning">
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

    </div>

    <!-- FontAwesome or other icons if needed can be loaded, but simple emojis look great and load instantly. -->
    @if(auth()->user()->hasAnyRole(['Administrador', 'Técnico', 'Recepcionista']))
        @livewire('solicitar-repuesto')
    @endif
    @livewireScripts
    @stack('scripts')
</body>
</html>
