@php
$styles = [
'page_container' => 'w-full max-w-7xl mx-auto space-y-8 pb-12',

// Header
'header_section' => 'flex flex-col md:flex-row justify-between items-start md:items-center py-4 gap-4',
'page_title' => 'text-3xl font-extrabold text-quaternary flex items-center gap-3',
'page_subtitle' => 'text-tertiary mt-1',

// Form & Section Cards
'card' => 'bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-extra/30 relative overflow-hidden mb-6',
'section_title' => 'text-xl font-bold text-quaternary mb-6 flex items-center gap-2 border-b border-extra/30 pb-4',

// Inputs
'input_group' => 'flex flex-col gap-1.5',
'label' => 'text-sm font-semibold text-tertiary',
'input' => 'w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary',

// Buttons
'btn_primary' => 'bg-accent hover:bg-black text-white px-8 py-3 rounded-xl font-bold transition-colors inline-flex items-center justify-center gap-2 shadow-sm whitespace-nowrap',
'btn_danger' => 'bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-xl font-semibold transition-colors justify-center flex items-center gap-2 text-sm',

// Table/List
'list_container' => 'bg-white rounded-3xl shadow-sm border border-extra/30 overflow-hidden',
'table' => 'w-full text-left border-collapse',
'th' => 'px-6 py-4 bg-secondary/5 text-quaternary font-bold text-sm tracking-wider border-b border-extra/30',
'td' => 'px-6 py-4 border-b border-extra/10 text-quaternary text-sm align-middle',
'td_primary' => 'px-6 py-4 border-b border-extra/10 text-quaternary font-bold align-middle',
];
@endphp

<x-app-layout>
    <x-slot name="content">
        <div class="{{ $styles['page_container'] }}">

            <header class="{{ $styles['header_section'] }}">
                <div>
                    <h1 class="{{ $styles['page_title'] }}">
                        <div
                            class="w-12 h-12 bg-accent/10 rounded-2xl flex items-center justify-center text-accent shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        Gestión de Personal
                    </h1>
                    <p class="{{ $styles['page_subtitle'] }}">Panel de Administrador • Gestiona accesos, roles y datos
                        de empleados.</p>
                </div>
            </header>

            {{-- Mensajes Flash --}}
            @if(session('success'))
            <div class="px-5 py-3 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-semibold">
                ✓ {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="px-5 py-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-semibold">
                Se encontraron errores al verificar el formulario. Por favor, revisa los datos ingresados.
            </div>
            @endif

            <!-- Filtros y Búsqueda -->
            <div class="{{ $styles['card'] }} !pb-6 !pt-6 mb-6">
                <form action="{{ route('manage') }}" method="GET" class="flex flex-col md:flex-row gap-4 justify-between items-center">
                    <div class="flex gap-2 bg-secondary/10 p-1.5 rounded-xl border border-extra/30 overflow-x-auto w-full md:w-auto">
                        <button type="submit" name="role" value="all" class="px-5 py-2 rounded-lg text-sm font-bold transition-all whitespace-nowrap {{ $roleFilter === 'all' ? 'bg-white shadow-sm text-accent' : 'text-tertiary hover:bg-secondary/20' }}">
                            Todos ({{ $counts['all'] }})
                        </button>
                        <button type="submit" name="role" value="adjuster" class="px-5 py-2 rounded-lg text-sm font-bold transition-all whitespace-nowrap {{ $roleFilter === 'adjuster' ? 'bg-white shadow-sm text-accent' : 'text-tertiary hover:bg-secondary/20' }}">
                            Ajustadores ({{ $counts['adjuster'] }})
                        </button>
                        <button type="submit" name="role" value="supervisor" class="px-5 py-2 rounded-lg text-sm font-bold transition-all whitespace-nowrap {{ $roleFilter === 'supervisor' ? 'bg-white shadow-sm text-accent' : 'text-tertiary hover:bg-secondary/20' }}">
                            Supervisores ({{ $counts['supervisor'] }})
                        </button>
                        <button type="submit" name="role" value="admin" class="px-5 py-2 rounded-lg text-sm font-bold transition-all whitespace-nowrap {{ $roleFilter === 'admin' ? 'bg-white shadow-sm text-accent' : 'text-tertiary hover:bg-secondary/20' }}">
                            Administradores ({{ $counts['admin'] }})
                        </button>
                    </div>

                    <div class="flex gap-2 w-full md:w-80">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar nombre o correo..." class="{{ $styles['input'] }} !py-2.5">
                        <button type="submit" class="bg-secondary text-quaternary px-4 py-2.5 rounded-xl font-bold hover:bg-extra/50 transition-colors">Buscar</button>
                    </div>
                </form>
            </div>

            <!-- Bloque Fijo: Registrar Nuevo Empleado -->
            <div class="{{ $styles['card'] }}">
                <h2 class="{{ $styles['section_title'] }}">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                        </path>
                    </svg>
                    Registrar Nuevo Empleado
                </h2>

                <form action="{{ route('manage.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                        <div class="{{ $styles['input_group'] }} lg:col-span-2">
                            <label class="{{ $styles['label'] }}" for="name">Nombre(s) <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" class="{{ $styles['input'] }}" value="{{ old('name') }}" required>
                            @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="father_lastname">Apellido Paterno <span class="text-red-500">*</span></label>
                            <input type="text" id="father_lastname" name="father_lastname" class="{{ $styles['input'] }}" value="{{ old('father_lastname') }}" required>
                            @error('father_lastname') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="mother_lastname">Apellido Materno</label>
                            <input type="text" id="mother_lastname" name="mother_lastname" class="{{ $styles['input'] }}" value="{{ old('mother_lastname') }}">
                            @error('mother_lastname') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="{{ $styles['input_group'] }} lg:col-span-1">
                            <label class="{{ $styles['label'] }}" for="email">Correo Electrónico Laboral <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" class="{{ $styles['input'] }}" value="{{ old('email') }}" required>
                            @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="{{ $styles['input_group'] }} lg:col-span-1">
                            <label class="{{ $styles['label'] }}" for="phone">Teléfono</label>
                            <input type="text" id="phone" name="phone" class="{{ $styles['input'] }}" value="{{ old('phone') }}" placeholder="+52 55 1234 5678">
                            @error('phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="birth_date">Fecha de Nacimiento <span class="text-red-500">*</span></label>
                            <input type="date" id="birth_date" name="birth_date" class="{{ $styles['input'] }}" value="{{ old('birth_date') }}" required>
                            @error('birth_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="role">Rol en el Sistema <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select id="role" name="role" class="{{ $styles['input'] }} appearance-none" required>
                                    <option value="" disabled selected>Seleccione un rol...</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                        {{ \App\Enums\RoleEnum::tryFrom($role->name)?->label() ?? ucfirst($role->name) }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-tertiary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            @error('role') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="{{ $styles['input_group'] }} lg:col-span-2">
                            <label class="{{ $styles['label'] }}" for="password">Contraseña Inicial <span class="text-red-500">*</span></label>
                            <input type="password" id="password" name="password" class="{{ $styles['input'] }}"
                                placeholder="Mínimo 8 caracteres" required>
                            @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-end lg:col-span-2 lg:justify-end">
                            <button type="submit" class="{{ $styles['btn_primary'] }} w-full md:w-auto">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Crear Empleado
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabla de Empleados -->
            <div class="{{ $styles['list_container'] }}">
                <div class="px-6 py-4 flex justify-between items-center border-b border-extra/30 bg-white">
                    <h2 class="text-lg font-bold text-quaternary">Directorio de Empleados Activos</h2>
                    <span class="text-xs font-semibold text-tertiary">Mostrando {{ $employees->count() }} de {{ $employees->total() }} registros</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="{{ $styles['table'] }}">
                        <thead>
                            <tr>
                                <th class="{{ $styles['th'] }}">Nombre Completo</th>
                                <th class="{{ $styles['th'] }}">Rol / Cargo</th>
                                <th class="{{ $styles['th'] }}">Correo Electrónico</th>
                                <th class="{{ $styles['th'] }}">Fecha de Nac.</th>
                                <th class="{{ $styles['th'] }} text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $emp)
                            <tr class="hover:bg-secondary/5 transition-colors group {{ $emp->trashed() ? 'opacity-60 bg-extra/10' : '' }}">
                                <td class="{{ $styles['td_primary'] }}">
                                    {{ $emp->name }} {{ $emp->father_lastname }}
                                </td>
                                <td class="{{ $styles['td'] }}">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-secondary/10 text-quaternary border border-extra/30 rounded-lg text-xs font-bold w-fit">
                                        @if($emp->role?->name === 'supervisor')
                                        <svg class="w-3.5 h-3.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                        @elseif($emp->role?->name === 'admin')
                                        <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        @else
                                        <svg class="w-3.5 h-3.5 text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        @endif
                                        {{ \App\Enums\RoleEnum::tryFrom($emp->role?->name)?->label() ?? ucfirst($emp->role?->name) }}
                                    </span>
                                    @if($emp->trashed())
                                    <span class="inline-flex items-center ml-2 px-2 py-0.5 bg-red-100 text-red-700 border border-red-200 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                        Inactivo
                                    </span>
                                    @endif
                                </td>
                                <td class="{{ $styles['td'] }} text-tertiary">
                                    {{ $emp->email }}
                                </td>
                                <td class="{{ $styles['td'] }} font-mono text-xs">
                                    {{ $emp->birth_date?->format('Y-m-d') ?? 'N/A' }}
                                </td>
                                <td class="{{ $styles['td'] }} text-right align-middle">
                                    <!-- Acciones -->
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @if($emp->id !== auth()->user()->id)
                                        @if($emp->trashed())
                                        <form action="{{ route('manage.restore', $emp->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-green-50 hover:bg-green-100 text-green-600 px-4 py-2 rounded-xl font-semibold transition-colors justify-center flex items-center gap-2 text-sm" title="Reestablecer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                Reestablecer
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('manage.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas dar de baja a este empleado?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="{{ $styles['btn_danger'] }}" title="Dar de Baja">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                Baja
                                            </button>
                                        </form>
                                        @endif
                                        @else
                                        <span class="text-xs font-semibold text-tertiary italic px-3 py-2 bg-secondary/10 rounded-xl">Tú</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-tertiary">
                                    <svg class="w-12 h-12 text-extra/40 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    No se encontraron empleados con los filtros actuales.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if($employees->hasPages())
                <div class="px-6 py-4 border-t border-extra/30 bg-white">
                    {{ $employees->links() }}
                </div>
                @endif
            </div>

        </div>
    </x-slot>
</x-app-layout>