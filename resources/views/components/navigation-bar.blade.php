@php
$styles = [
'sidebar' => 'w-72 shrink-0 h-[calc(100vh-2rem)] m-4 rounded-3xl bg-quaternary flex flex-col text-white shadow-2xl z-20 overflow-hidden',

'logo_container' => 'p-6 pb-2 border-b border-white/10 shrink-0 bg-quaternary z-10',
'logo_bg' => 'bg-white p-2 rounded-2xl shadow-lg flex items-center justify-center',
'logo_img' => 'h-14 w-auto object-contain scale-110',

'nav_container' => 'flex-1 py-6 px-4 flex flex-col gap-8 overflow-y-auto',

'group' => 'flex flex-col gap-2',
'group_title' => 'text-xs font-bold text-accent uppercase tracking-widest px-4 mb-2 opacity-80',

'link' => 'flex items-center gap-3 px-4 py-3 rounded-xl text-secondary hover:text-white hover:bg-accent/20 transition-all duration-300 relative group',
'link_active' => 'flex items-center gap-3 px-4 py-3 rounded-xl text-white bg-accent/40 font-semibold relative',

'active_indicator' => '',

'icon' => 'w-5 h-5 opacity-70 group-hover:opacity-100 transition-opacity',

'footer' => 'p-6 shrink-0 border-t border-white/10 bg-quaternary',
'logout_btn' => 'flex items-center gap-3 px-4 py-3 w-full rounded-xl text-red-300 hover:text-white hover:bg-red-500/20 transition-all duration-300',
];

// Helper to determine if a route is active.
// In a real scenario, you'd use request()->routeIs('route_name')
$currentRoute = request()->route()->getName() ?? '';

$linkClass = function ($route) use ($styles, $currentRoute) {
return $currentRoute === $route ? $styles['link_active'] : $styles['link'];
};
@endphp

<aside class="{{ $styles['sidebar'] }}">
    <!-- Logo -->
    <div class="{{ $styles['logo_container'] }}">
        <div class="{{ $styles['logo_bg'] }}">
            <img src="{{ asset('/logos/DFM_SECURE_LOGO.png') }}" alt="DFM SECURE" class="{{ $styles['logo_img'] }}">
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="{{ $styles['nav_container'] }}">

        <!-- Grupo: Generales -->
        <div class="{{ $styles['group'] }}">
            <h3 class="{{ $styles['group_title'] }}">Generales</h3>

            <a href="{{ route('dashboard') }}" class="{{ $linkClass('dashboard') }}">
                @if($currentRoute === 'dashboard')
                <div class="{{ $styles['active_indicator'] }}"></div>
                @endif
                <svg class="{{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('profile') }}" class="{{ $linkClass('profile') }}">
                @if($currentRoute === 'profile')
                <div class="{{ $styles['active_indicator'] }}"></div>
                @endif
                <svg class="{{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Perfil</span>
            </a>

            <a href="{{ route('consultation') }}" class="{{ $linkClass('consultation') }}">
                @if($currentRoute === 'consultation')
                <div class="{{ $styles['active_indicator'] }}"></div>
                @endif
                <svg class="{{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Consulta</span>
            </a>

        </div>

        <!-- Grupo: Asegurado -->
        @if (auth()->user()->isInsured())
        <div class="{{ $styles['group'] }}">
            <h3 class="{{ $styles['group_title'] }}">Asegurado</h3>

            <a href="{{ route('myVehicle') }}" class="{{ $linkClass('myVehicle') }}">
                @if($currentRoute === 'myVehicle')
                <div class="{{ $styles['active_indicator'] }}"></div>
                @endif
                <svg class="{{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                <span>Mis Vehículos</span>
            </a>

            <a href="{{ route('editVehicle') }}" class="{{ $linkClass('editVehicle') }}">
                @if($currentRoute === 'editVehicle')
                <div class="{{ $styles['active_indicator'] }}"></div>
                @endif
                <svg class="{{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
                <span>Editar Vehículo</span>
            </a>

            <a href="{{ route('myPolicies') }}" class="{{ $linkClass('myPolicies') }}">
                @if($currentRoute === 'myPolicies')
                <div class="{{ $styles['active_indicator'] }}"></div>
                @endif
                <svg class="{{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Mis Pólizas</span>
            </a>
        </div>
        @endif

        <!-- Grupo: Ajustador -->
        @if (auth()->user()->isAdjuster())
        <div class="{{ $styles['group'] }}">
            <h3 class="{{ $styles['group_title'] }}">Ajustador</h3>

            <a href="{{ route('sinisterRegister') }}" class="{{ $linkClass('sinisterRegister') }}">
                @if($currentRoute === 'sinisterRegister')
                <div class="{{ $styles['active_indicator'] }}"></div>
                @endif
                <svg class="{{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                <span>Registrar Siniestro</span>
            </a>

            <a href="{{ route('sinisterEdit') }}" class="{{ $linkClass('sinisterEdit') }}">
                @if($currentRoute === 'sinisterEdit')
                <div class="{{ $styles['active_indicator'] }}"></div>
                @endif
                <svg class="{{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
                <span>Editar Siniestro</span>
            </a>
        </div>
        @endif

        <!-- Grupo: Supervisor -->
        @if (auth()->user()->isSupervisor())
        <div class="{{ $styles['group'] }}">
            <h3 class="{{ $styles['group_title'] }}">Supervisor</h3>

            <a href="{{ route('search') }}" class="{{ $linkClass('search') }}">
                @if($currentRoute === 'search')
                <div class="{{ $styles['active_indicator'] }}"></div>
                @endif
                <svg class="{{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Buscar Expedientes</span>
            </a>

            <a href="{{ route('sinisterManage') }}" class="{{ $linkClass('sinisterManage') }}">
                @if($currentRoute === 'sinisterManage')
                <div class="{{ $styles['active_indicator'] }}"></div>
                @endif
                <svg class="{{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                    </path>
                </svg>
                <span>Dictaminar Siniestro</span>
            </a>
        </div>
        @endif

        <!-- Grupo: Administrador -->
        @if (auth()->user()->isAdmin())
        <div class="{{ $styles['group'] }}">
            <h3 class="{{ $styles['group_title'] }}">Administrador</h3>

            <a href="{{ route('manage') }}" class="{{ $linkClass('manage') }}">
                @if($currentRoute === 'manage')
                <div class="{{ $styles['active_indicator'] }}"></div>
                @endif
                <svg class="{{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                <span>Gestionar Usuarios</span>
            </a>
        </div>
        @endif

    </nav>

    <!-- Cerrar Sesión Footer -->
    <div class="{{ $styles['footer'] }}">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="{{ $styles['logout_btn'] }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                <span class="font-semibold">Cerrar Sesión</span>
            </button>
        </form>
    </div>
</aside>