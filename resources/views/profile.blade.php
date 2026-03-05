@php
    $styles = [
        'page_container' => 'w-full max-w-7xl mx-auto space-y-8 pb-10',

        // Header
        'header_section' => 'flex items-center gap-6 bg-white p-6 rounded-3xl shadow-sm border border-extra/30 relative overflow-hidden',
        'header_bg_deco' => 'absolute -right-20 -top-20 w-64 h-64 bg-accent/10 rounded-full blur-3xl pointer-events-none',
        'avatar_wrapper' => 'w-24 h-24 rounded-full bg-secondary flex items-center justify-center border-4 border-white shadow-md shrink-0 overflow-hidden',
        'avatar_img' => 'w-full h-full object-cover',
        'welcome_text' => 'text-tertiary font-medium',
        'user_name' => 'text-3xl font-extrabold text-quaternary leading-tight',

        // Sections Base
        'section_title' => 'text-xl font-bold text-quaternary mb-4 flex items-center gap-2',
        'card' => 'bg-white p-6 lg:p-8 rounded-3xl shadow-sm border border-extra/30',

        // General Info Grid
        'info_grid' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-6 gap-x-8',
        'info_item' => 'flex flex-col',
        'info_label' => 'text-sm text-tertiary mb-1 font-medium',
        'info_value' => 'text-lg font-semibold text-quaternary',

        // Buttons
        'btn_outline' => 'px-6 py-2.5 rounded-xl border-2 border-quaternary text-quaternary font-semibold hover:bg-quaternary hover:text-white transition-colors self-start mt-6 md:mt-0',
        'btn_primary' => 'px-4 py-2 bg-accent text-white font-semibold rounded-lg hover:bg-black transition-colors text-sm',

        // Tables
        'table_wrapper' => 'overflow-x-auto bg-white rounded-2xl border border-extra/30',
        'table' => 'w-full text-left border-collapse',
        'th' => 'px-6 py-4 border-b border-extra/30 bg-secondary/30 text-sm font-semibold text-tertiary',
        'td' => 'px-6 py-4 border-b border-extra/10 text-quaternary font-medium',
        'td_alt' => 'px-6 py-4 border-b border-extra/10 text-tertiary',

        // Role Specific
        'grid_3' => 'grid grid-cols-1 md:grid-cols-3 gap-6',
        'stat_box' => 'bg-secondary/20 p-6 rounded-2xl flex flex-col items-center justify-center text-center',
        'stat_val' => 'text-4xl font-extrabold text-accent mb-2',
        'stat_desc' => 'text-sm font-medium text-tertiary',

        // Progress Bar
        'progress_bar_bg' => 'w-full h-4 bg-secondary rounded-full overflow-hidden flex',
        'progress_green' => 'h-full bg-green-500',
        'progress_yellow' => 'h-full bg-yellow-500',
        'progress_red' => 'h-full bg-red-500',
        'legend_dot' => 'w-3 h-3 rounded-full',
    ];
@endphp

<x-app-layout>
    <x-slot name="content">
        <div class="{{ $styles['page_container'] }}">

            <!-- SECCIÓN 1: HEADER & BIENVENIDA -->
            <section class="{{ $styles['header_section'] }}">
                <div class="{{ $styles['header_bg_deco'] }}"></div>
                <div class="{{ $styles['avatar_wrapper'] }}">
                    <img src="https://ui-avatars.com/api/?name=Omar+Fernandez&background=92AA74&color=fff&size=256"
                        alt="Avatar" class="{{ $styles['avatar_img'] }}">
                </div>
                <div class="flex-1 flex flex-col md:flex-row md:items-center justify-between z-10">
                    <div>
                        <p class="{{ $styles['welcome_text'] }}">Bienvenido de vuelta,</p>
                        <h1 class="{{ $styles['user_name'] }}">Omar Fernandez</h1>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN 2: DATOS GENERALES (Todos los usuarios) -->
            <section>
                <h2 class="{{ $styles['section_title'] }}">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Información Personal
                </h2>
                <div class="{{ $styles['card'] }} flex flex-col md:flex-row gap-8 justify-between">
                    <div class="{{ $styles['info_grid'] }} flex-1">
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Nombre Completo</span>
                            <span class="{{ $styles['info_value'] }}">Omar Fernandez Rivera</span>
                        </div>
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Fecha de Nacimiento</span>
                            <span class="{{ $styles['info_value'] }}">15 / Oct / 1990</span>
                        </div>
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Edad</span>
                            <span class="{{ $styles['info_value'] }}">34 Años</span>
                        </div>
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Correo Electrónico</span>
                            <span class="{{ $styles['info_value'] }}">omar.fernandez@ejemplo.com</span>
                        </div>
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Teléfono Móvil</span>
                            <span class="{{ $styles['info_value'] }}">+52 55 1234 5678</span>
                        </div>
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Género</span>
                            <span class="{{ $styles['info_value'] }}">Masculino</span>
                        </div>
                    </div>
                    <button class="{{ $styles['btn_outline'] }}">
                        Editar Perfil
                    </button>
                </div>
            </section>

            <!-- SEPARADOR VISUAL TEMPORAL PARA DEMOSTRACIÓN -->
            <div class="py-4 flex items-center text-extra opacity-50">
                <hr class="flex-1 border-extra">
                <hr class="flex-1 border-extra">
            </div>

            <!-- SECCIÓN 3: VISTA ASEGURADO -->
            <section>
                <h2 class="{{ $styles['section_title'] }}">
                    <span class="bg-blue-100 text-blue-700 px-2 rounded text-sm mr-2">Rol</span>
                    Mis Pólizas (Asegurado)
                </h2>
                <div class="{{ $styles['table_wrapper'] }}">
                    <table class="{{ $styles['table'] }}">
                        <thead>
                            <tr>
                                <th class="{{ $styles['th'] }}">Folio Póliza</th>
                                <th class="{{ $styles['th'] }}">Vehículo Asegurado</th>
                                <th class="{{ $styles['th'] }}">Tipo de Cobertura</th>
                                <th class="{{ $styles['th'] }}">Vencimiento</th>
                                <th class="{{ $styles['th'] }} text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-secondary/10 transition-colors">
                                <td class="{{ $styles['td'] }}">POL-2023-A892</td>
                                <td class="{{ $styles['td_alt'] }}">Ford Mustang 2024</td>
                                <td class="{{ $styles['td'] }}"><span
                                        class="px-2 py-1 bg-quaternary/10 text-quaternary rounded-md text-xs font-bold">Cobertura
                                        Amplia</span></td>
                                <td class="{{ $styles['td_alt'] }}">12 / Dic / 2024</td>
                                <td class="px-6 py-4 border-b border-extra/10 text-center">
                                    <button class="{{ $styles['btn_primary'] }}">Ver Póliza</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-secondary/10 transition-colors">
                                <td class="{{ $styles['td'] }}">POL-2022-B415</td>
                                <td class="{{ $styles['td_alt'] }}">Chevrolet Aveo 2023</td>
                                <td class="{{ $styles['td'] }}"><span
                                        class="px-2 py-1 bg-secondary text-tertiary rounded-md text-xs font-bold">Daños
                                        a Terceros</span></td>
                                <td class="{{ $styles['td_alt'] }}">05 / Feb / 2025</td>
                                <td class="px-6 py-4 border-b border-extra/10 text-center">
                                    <button class="{{ $styles['btn_primary'] }}">Ver Póliza</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- SECCIÓN 4: VISTA AJUSTADOR -->
            <section>
                <h2 class="{{ $styles['section_title'] }}">
                    <span class="bg-purple-100 text-purple-700 px-2 rounded text-sm mr-2">Rol</span>
                    Panel de Ajustador
                </h2>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
                    <!-- Stats / Info Básica -->
                    <div class="lg:col-span-5 {{ $styles['card'] }} flex flex-col justify-center">
                        <div class="mb-6">
                            <span class="text-sm font-bold text-tertiary uppercase tracking-wider block mb-1">Zona
                                Asignada</span>
                            <span class="text-2xl font-bold text-quaternary">Norte - Área Metropolitana</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-secondary/30 p-4 rounded-xl">
                                <span class="block text-3xl font-extrabold text-quaternary">14</span>
                                <span class="text-xs font-semibold text-tertiary">Siniestros Activos</span>
                            </div>
                            <div class="bg-secondary/30 p-4 rounded-xl">
                                <span class="block text-3xl font-extrabold text-quaternary">128</span>
                                <span class="text-xs font-semibold text-tertiary">Histórico Registrado</span>
                            </div>
                        </div>
                    </div>

                    <!-- Datos Fiscales -->
                    <div class="lg:col-span-7 {{ $styles['card'] }} !p-0 overflow-hidden flex flex-col">
                        <div class="p-4 bg-secondary/30 border-b border-extra/30">
                            <h3 class="font-bold text-quaternary">Datos Fiscales Relevantes</h3>
                        </div>
                        <div class="p-6 grid grid-cols-2 gap-6">
                            <div>
                                <span class="block text-xs text-tertiary mb-1">RFC</span>
                                <span class="font-bold text-quaternary tracking-wide">FERO901015HX8</span>
                            </div>
                            <div>
                                <span class="block text-xs text-tertiary mb-1">Cédula Profesional</span>
                                <span class="font-bold text-quaternary">12345678</span>
                            </div>
                            <div>
                                <span class="block text-xs text-tertiary mb-1">Entidad Federativa</span>
                                <span class="font-bold text-quaternary">CDMX</span>
                            </div>
                            <div>
                                <span class="block text-xs text-tertiary mb-1">Régimen</span>
                                <span class="font-bold text-quaternary">Personas Físicas con Actividades
                                    Empresariales</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN 5: VISTA SUPERVISOR -->
            <section>
                <h2 class="{{ $styles['section_title'] }}">
                    <span class="bg-orange-100 text-orange-700 px-2 rounded text-sm mr-2">Rol</span>
                    Métricas de Supervisión
                </h2>
                <div class="{{ $styles['card'] }}">
                    <div class="{{ $styles['grid_3'] }}">
                        <div class="{{ $styles['stat_box'] }}">
                            <span class="{{ $styles['stat_val'] }}">452</span>
                            <span class="{{ $styles['stat_desc'] }}">Casos Revisados (Histórico)</span>
                        </div>
                        <div class="{{ $styles['stat_box'] }} bg-blue-50/50">
                            <span class="{{ $styles['stat_val'] }} !text-blue-600">1,204</span>
                            <span class="{{ $styles['stat_desc'] }}">Siniestros Registrados Globales</span>
                        </div>
                        <div class="{{ $styles['stat_box'] }} bg-red-50/50">
                            <span
                                class="{{ $styles['stat_val'] }} !text-red-500 hover:scale-110 transition-transform cursor-pointer">89</span>
                            <span class="{{ $styles['stat_desc'] }}">Siniestros Activos Actualmente</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN 6: VISTA ADMINISTRADOR -->
            <section>
                <h2 class="{{ $styles['section_title'] }}">
                    <span class="bg-red-100 text-red-700 px-2 rounded text-sm mr-2">Rol</span>
                    Dashboard Administrativo (KPIs)
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Metricas Base -->
                    <div class="{{ $styles['card'] }}">
                        <h3 class="font-bold text-quaternary mb-4 border-b border-extra/30 pb-2">Sistema y Usuarios</h3>
                        <div class="grid grid-cols-2 gap-y-6">
                            <div>
                                <span class="text-sm text-tertiary block">Total Asegurados</span>
                                <span class="text-2xl font-extrabold text-quaternary">3,450</span>
                            </div>
                            <div>
                                <span class="text-sm text-tertiary block">Total Empleados</span>
                                <span class="text-2xl font-extrabold text-quaternary">45</span>
                            </div>
                            <div>
                                <span class="text-sm text-tertiary block">Pólizas Activas</span>
                                <span class="text-2xl font-extrabold text-quaternary">2,890</span>
                            </div>
                            <div>
                                <span class="text-sm text-tertiary block text-accent font-bold">Sin. Registrados
                                    (Mes)</span>
                                <span class="text-2xl font-extrabold text-accent">128</span>
                            </div>
                        </div>
                    </div>

                    <!-- Grafico Siniestros -->
                    <div class="{{ $styles['card'] }} flex flex-col justify-center">
                        <h3 class="font-bold text-quaternary mb-6 text-center">Distribución de Siniestros (Mes Actual)
                        </h3>

                        <!-- Barra de progreso multicolor -->
                        <div class="{{ $styles['progress_bar_bg'] }} mb-4">
                            <div class="{{ $styles['progress_green'] }}" style="width: 60%;"
                                title="Cerrados/Pagados (60%)"></div>
                            <div class="{{ $styles['progress_yellow'] }}" style="width: 25%;" title="En Revisión (25%)">
                            </div>
                            <div class="{{ $styles['progress_red'] }}" style="width: 15%;" title="Rechazados (15%)">
                            </div>
                        </div>

                        <!-- Leyenda -->
                        <div class="flex justify-between px-4">
                            <div class="flex items-center gap-2">
                                <div class="{{ $styles['legend_dot'] }} bg-green-500"></div>
                                <span class="text-xs text-tertiary font-bold">Cerrados (60%)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="{{ $styles['legend_dot'] }} bg-yellow-500"></div>
                                <span class="text-xs text-tertiary font-bold">En Revisión (25%)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="{{ $styles['legend_dot'] }} bg-red-500"></div>
                                <span class="text-xs text-tertiary font-bold">Rechazados (15%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </x-slot>
</x-app-layout>