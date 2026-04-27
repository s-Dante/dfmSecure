@php
    $styles = [
        'page_container' => 'w-full max-w-7xl mx-auto space-y-8 pb-10',

        // Header
        'header_section' => 'flex items-center gap-6 bg-white p-6 rounded-3xl shadow-sm border border-extra/30 relative overflow-hidden',
        'header_bg_deco' => 'absolute -right-20 -top-20 w-64 h-64 bg-accent/10 rounded-full blur-3xl pointer-events-none',
        'avatar_wrapper' => 'w-24 h-24 rounded-full bg-secondary flex items-center justify-center border-4 border-white shadow-md shrink-0 overflow-hidden',
        'avatar_img'     => 'w-full h-full object-cover',
        'welcome_text'   => 'text-tertiary font-medium',
        'user_name'      => 'text-3xl font-extrabold text-quaternary leading-tight',

        // Sections
        'section_title'  => 'text-xl font-bold text-quaternary mb-4 flex items-center gap-2',
        'card'           => 'bg-white p-6 lg:p-8 rounded-3xl shadow-sm border border-extra/30',

        // Info Grid
        'info_grid'  => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-6 gap-x-8',
        'info_item'  => 'flex flex-col',
        'info_label' => 'text-sm text-tertiary mb-1 font-medium',
        'info_value' => 'text-lg font-semibold text-quaternary',

        // Buttons
        'btn_outline'  => 'px-6 py-2.5 rounded-xl border-2 border-quaternary text-quaternary font-semibold hover:bg-quaternary hover:text-white transition-colors self-start mt-6 md:mt-0',
        'btn_primary'  => 'px-4 py-2 bg-accent text-white font-semibold rounded-lg hover:bg-black transition-colors text-sm',

        // Tables
        'table_wrapper' => 'overflow-x-auto bg-white rounded-2xl border border-extra/30',
        'table' => 'w-full text-left border-collapse',
        'th'    => 'px-6 py-4 border-b border-extra/30 bg-secondary/30 text-sm font-semibold text-tertiary',
        'td'    => 'px-6 py-4 border-b border-extra/10 text-quaternary font-medium',
        'td_alt'=> 'px-6 py-4 border-b border-extra/10 text-tertiary',

        // Stats
        'grid_3'   => 'grid grid-cols-1 md:grid-cols-3 gap-6',
        'stat_box' => 'bg-secondary/20 p-6 rounded-2xl flex flex-col items-center justify-center text-center',
        'stat_val' => 'text-4xl font-extrabold text-accent mb-2',
        'stat_desc'=> 'text-sm font-medium text-tertiary',

        // Progress Bar
        'progress_bar_bg' => 'w-full h-4 bg-secondary rounded-full overflow-hidden flex',
        'progress_green'  => 'h-full bg-green-500',
        'progress_yellow' => 'h-full bg-yellow-500',
        'progress_red'    => 'h-full bg-red-500',
        'legend_dot'      => 'w-3 h-3 rounded-full',
    ];

    // Avatar URL
    $hasBlob   = !empty($user->profile_picture_blob);
    $avatarUrl = $hasBlob
        ? route('media.profile', $user->id) . '?v=' . time()
        : (!empty($user->profile_picture_url)
            ? asset($user->profile_picture_url)
            : 'https://ui-avatars.com/api/?name=' . urlencode($user->name . '+' . $user->father_lastname) . '&background=92AA74&color=fff&size=256');

    // Género legible
    $genderLabel = $user->gender instanceof \BackedEnum
        ? (method_exists($user->gender, 'label') ? $user->gender->label() : ucfirst($user->gender->value))
        : ucfirst((string)($user->gender ?? ''));

    // Rol legible
    $roleLabel = match($user->role?->name) {
        'insured'    => 'Asegurado',
        'adjuster'   => 'Ajustador',
        'supervisor' => 'Supervisor',
        'admin'      => 'Administrador',
        default      => ucfirst($user->role?->name ?? 'N/A'),
    };

    $stats = (object) $stats;
@endphp

<x-app-layout>
    <x-slot name="content">
        <div class="{{ $styles['page_container'] }}">

            {{-- ── HEADER ── --}}
            <section class="{{ $styles['header_section'] }}">
                <div class="{{ $styles['header_bg_deco'] }}"></div>
                <div class="{{ $styles['avatar_wrapper'] }}">
                    <img src="{{ $avatarUrl }}" alt="Avatar" class="{{ $styles['avatar_img'] }}">
                </div>
                <div class="flex-1 flex flex-col md:flex-row md:items-center justify-between z-10">
                    <div>
                        <p class="{{ $styles['welcome_text'] }}">Bienvenido de vuelta,</p>
                        <h1 class="{{ $styles['user_name'] }}">{{ $user->full_name }}</h1>
                        <span class="mt-1 inline-block px-3 py-1 bg-accent/10 text-accent text-sm font-bold rounded-full">
                            {{ $roleLabel }}
                        </span>
                    </div>
                </div>
            </section>

            {{-- ── DATOS GENERALES (todos los roles) ── --}}
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
                            <span class="{{ $styles['info_value'] }}">{{ $user->full_name }}</span>
                        </div>
                        @if($user->birth_date)
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Fecha de Nacimiento</span>
                            <span class="{{ $styles['info_value'] }}">{{ $user->birth_date->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</span>
                        </div>
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Edad</span>
                            <span class="{{ $styles['info_value'] }}">{{ $user->birth_date->age }} Años</span>
                        </div>
                        @endif
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Correo Electrónico</span>
                            <span class="{{ $styles['info_value'] }}">{{ $user->email }}</span>
                        </div>
                        @if($user->phone)
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Teléfono Móvil</span>
                            <span class="{{ $styles['info_value'] }}">{{ $user->phone }}</span>
                        </div>
                        @endif
                        @if($genderLabel)
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Género</span>
                            <span class="{{ $styles['info_value'] }}">{{ $genderLabel }}</span>
                        </div>
                        @endif
                    </div>
                    <a href="{{ route('profile.edit') }}" class="{{ $styles['btn_outline'] }}">Editar Perfil</a>
                </div>
            </section>

            {{-- ── ASEGURADO ── --}}
            @if($user->isInsured())
            <section>
                <h2 class="{{ $styles['section_title'] }}">
                    <span class="bg-blue-100 text-blue-700 px-2 rounded text-sm">Asegurado</span>
                    Mis Pólizas
                </h2>
                <div class="{{ $styles['table_wrapper'] }}">
                    <table class="{{ $styles['table'] }}">
                        <thead>
                            <tr>
                                <th class="{{ $styles['th'] }}">Folio Póliza</th>
                                <th class="{{ $styles['th'] }}">Vehículo Asegurado</th>
                                <th class="{{ $styles['th'] }}">Plan</th>
                                <th class="{{ $styles['th'] }}">Vencimiento</th>
                                <th class="{{ $styles['th'] }} text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->policies as $policy)
                            @php
                                $vm = $policy->vehicle?->vehicleModel;
                                $vName = $vm ? trim(($vm->brand ?? '') . ' ' . ($vm->sub_brand ?? '') . ' ' . ($vm->year ?? '')) : 'N/A';
                                $polStatus = $policy->status instanceof \BackedEnum ? $policy->status->value : (string)$policy->status;
                                $polBadge = match($polStatus) {
                                    'active' => 'bg-green-100 text-green-700',
                                    'expired'=> 'bg-red-100 text-red-700',
                                    'cancelled'=>'bg-gray-100 text-gray-600',
                                    default  => 'bg-yellow-100 text-yellow-700',
                                };
                            @endphp
                            <tr class="hover:bg-secondary/10 transition-colors">
                                <td class="{{ $styles['td'] }}">{{ $policy->folio ?? $policy->policy_number ?? 'N/A' }}</td>
                                <td class="{{ $styles['td_alt'] }}">{{ $vName }}</td>
                                <td class="{{ $styles['td'] }}">{{ $policy->plan?->name ?? 'N/A' }}</td>
                                <td class="{{ $styles['td_alt'] }}">{{ $policy->end_validity?->format('d / M / Y') ?? 'N/A' }}</td>
                                <td class="px-6 py-4 border-b border-extra/10 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $polBadge }}">
                                        {{ ucfirst($polStatus) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-tertiary">No tienes pólizas registradas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Resumen stats asegurado --}}
            <section>
                <h2 class="{{ $styles['section_title'] }}">
                    <span class="bg-blue-100 text-blue-700 px-2 rounded text-sm">Asegurado</span>
                    Resumen de Siniestros
                </h2>
                <div class="{{ $styles['grid_3'] }}">
                    <div class="{{ $styles['stat_box'] }}">
                        <span class="{{ $styles['stat_val'] }}">{{ $stats->total_policies ?? 0 }}</span>
                        <span class="{{ $styles['stat_desc'] }}">Pólizas Totales</span>
                    </div>
                    <div class="{{ $styles['stat_box'] }}">
                        <span class="{{ $styles['stat_val'] }}">{{ $stats->total_sinisters ?? 0 }}</span>
                        <span class="{{ $styles['stat_desc'] }}">Siniestros Totales</span>
                    </div>
                    <div class="{{ $styles['stat_box'] }} bg-yellow-50">
                        <span class="{{ $styles['stat_val'] }} !text-yellow-500">{{ $stats->in_review ?? 0 }}</span>
                        <span class="{{ $styles['stat_desc'] }}">En Revisión</span>
                    </div>
                </div>
            </section>

            {{-- Dirección --}}
            @if($user->address)
            <section>
                <h2 class="{{ $styles['section_title'] }}">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Dirección Registrada
                </h2>
                <div class="{{ $styles['card'] }}">
                    <div class="{{ $styles['info_grid'] }}">
                        @foreach([
                            'Calle'        => $user->address->street,
                            'Núm. Ext.'    => $user->address->external_number,
                            'Núm. Int.'    => $user->address->internal_number,
                            'Colonia'      => $user->address->neighborhood,
                            'Ciudad'       => $user->address->city,
                            'Estado'       => $user->address->state,
                            'País'         => $user->address->country,
                            'C.P.'         => $user->address->zip_code,
                        ] as $label => $value)
                        @if($value)
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">{{ $label }}</span>
                            <span class="{{ $styles['info_value'] }}">{{ $value }}</span>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </section>
            @endif

            {{-- Datos Fiscales --}}
            @if($user->fiscalData)
            <section>
                <h2 class="{{ $styles['section_title'] }}">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Datos Fiscales
                </h2>
                <div class="{{ $styles['card'] }}">
                    <div class="{{ $styles['info_grid'] }}">
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">RFC</span>
                            <span class="{{ $styles['info_value'] }} tracking-widest">{{ $user->fiscalData->rfc }}</span>
                        </div>
                        @if($user->fiscalData->company_name)
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Razón Social</span>
                            <span class="{{ $styles['info_value'] }}">{{ $user->fiscalData->company_name }}</span>
                        </div>
                        @endif
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Régimen Fiscal</span>
                            <span class="{{ $styles['info_value'] }}">
                                {{ $user->fiscalData->tax_regime instanceof \BackedEnum && method_exists($user->fiscalData->tax_regime, 'label')
                                    ? $user->fiscalData->tax_regime->label()
                                    : ucfirst((string)$user->fiscalData->tax_regime) }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>
            @endif
            @endif
            {{-- ── FIN ASEGURADO ── --}}

            {{-- ── AJUSTADOR ── --}}
            @if($user->isAdjuster())
            <section>
                <h2 class="{{ $styles['section_title'] }}">
                    <span class="bg-purple-100 text-purple-700 px-2 rounded text-sm">Ajustador</span>
                    Panel de Ajustador
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-5 {{ $styles['card'] }} flex flex-col justify-center">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-secondary/30 p-4 rounded-xl">
                                <span class="block text-3xl font-extrabold text-quaternary">{{ $stats->active_sinisters ?? 0 }}</span>
                                <span class="text-xs font-semibold text-tertiary">Siniestros Activos</span>
                            </div>
                            <div class="bg-secondary/30 p-4 rounded-xl">
                                <span class="block text-3xl font-extrabold text-quaternary">{{ $stats->total_sinisters ?? 0 }}</span>
                                <span class="text-xs font-semibold text-tertiary">Histórico Total</span>
                            </div>
                        </div>
                    </div>

                    @if($user->fiscalData)
                    <div class="lg:col-span-7 {{ $styles['card'] }} !p-0 overflow-hidden flex flex-col">
                        <div class="p-4 bg-secondary/30 border-b border-extra/30">
                            <h3 class="font-bold text-quaternary">Datos Fiscales</h3>
                        </div>
                        <div class="p-6 grid grid-cols-2 gap-6">
                            <div>
                                <span class="block text-xs text-tertiary mb-1">RFC</span>
                                <span class="font-bold text-quaternary tracking-wide">{{ $user->fiscalData->rfc }}</span>
                            </div>
                            @if($user->fiscalData->company_name)
                            <div>
                                <span class="block text-xs text-tertiary mb-1">Razón Social</span>
                                <span class="font-bold text-quaternary">{{ $user->fiscalData->company_name }}</span>
                            </div>
                            @endif
                            <div>
                                <span class="block text-xs text-tertiary mb-1">Régimen</span>
                                <span class="font-bold text-quaternary">
                                    {{ $user->fiscalData->tax_regime instanceof \BackedEnum && method_exists($user->fiscalData->tax_regime, 'label')
                                        ? $user->fiscalData->tax_regime->label()
                                        : ucfirst((string)$user->fiscalData->tax_regime) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </section>
            @endif
            {{-- ── FIN AJUSTADOR ── --}}

            {{-- ── SUPERVISOR ── --}}
            @if($user->isSupervisor())
            <section>
                <h2 class="{{ $styles['section_title'] }}">
                    <span class="bg-orange-100 text-orange-700 px-2 rounded text-sm">Supervisor</span>
                    Métricas de Supervisión
                </h2>
                <div class="{{ $styles['card'] }}">
                    <div class="{{ $styles['grid_3'] }}">
                        <div class="{{ $styles['stat_box'] }}">
                            <span class="{{ $styles['stat_val'] }}">{{ $stats->supervised ?? 0 }}</span>
                            <span class="{{ $styles['stat_desc'] }}">Casos Supervisados (Total)</span>
                        </div>
                        <div class="{{ $styles['stat_box'] }} bg-blue-50/50">
                            <span class="{{ $styles['stat_val'] }} !text-blue-600">{{ $stats->in_review ?? 0 }}</span>
                            <span class="{{ $styles['stat_desc'] }}">En Revisión</span>
                        </div>
                        <div class="{{ $styles['stat_box'] }} bg-green-50/50">
                            <span class="{{ $styles['stat_val'] }} !text-green-600">{{ $stats->closed ?? 0 }}</span>
                            <span class="{{ $styles['stat_desc'] }}">Cerrados / Resueltos</span>
                        </div>
                    </div>
                </div>
            </section>
            @endif
            {{-- ── FIN SUPERVISOR ── --}}

            {{-- ── ADMINISTRADOR ── --}}
            @if($user->isAdmin())
            <section>
                <h2 class="{{ $styles['section_title'] }}">
                    <span class="bg-red-100 text-red-700 px-2 rounded text-sm">Admin</span>
                    Dashboard Administrativo (KPIs)
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Métricas Base --}}
                    <div class="{{ $styles['card'] }}">
                        <h3 class="font-bold text-quaternary mb-4 border-b border-extra/30 pb-2">Sistema y Usuarios</h3>
                        <div class="grid grid-cols-2 gap-y-6">
                            <div>
                                <span class="text-sm text-tertiary block">Total Asegurados</span>
                                <span class="text-2xl font-extrabold text-quaternary">{{ $stats->total_insured ?? 0 }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-tertiary block">Ajustadores</span>
                                <span class="text-2xl font-extrabold text-quaternary">{{ $stats->total_adjuster ?? 0 }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-tertiary block">Supervisores</span>
                                <span class="text-2xl font-extrabold text-quaternary">{{ $stats->total_supervisor ?? 0 }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-tertiary block">Pólizas Activas</span>
                                <span class="text-2xl font-extrabold text-quaternary">{{ $stats->active_policies ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Gráfico Siniestros --}}
                    <div class="{{ $styles['card'] }} flex flex-col justify-center">
                        <h3 class="font-bold text-quaternary mb-4 text-center">
                            Siniestros (Total vs. Este Mes)
                        </h3>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="bg-secondary/20 rounded-2xl p-5">
                                <span class="block text-3xl font-extrabold text-accent">{{ $stats->total_sinisters ?? 0 }}</span>
                                <span class="text-xs font-semibold text-tertiary mt-1 block">Total Histórico</span>
                            </div>
                            <div class="bg-blue-50 rounded-2xl p-5">
                                <span class="block text-3xl font-extrabold text-blue-600">{{ $stats->sinisters_month ?? 0 }}</span>
                                <span class="text-xs font-semibold text-tertiary mt-1 block">Este Mes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @endif
            {{-- ── FIN ADMINISTRADOR ── --}}

        </div>
    </x-slot>
</x-app-layout>