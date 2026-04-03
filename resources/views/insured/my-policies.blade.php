@php
    $styles = [
        'page_container' => 'w-full max-w-7xl mx-auto space-y-10 pb-12',

        // Header
        'header_section' => 'flex flex-col md:flex-row justify-between items-start md:items-center py-4 gap-4',
        'page_title' => 'text-3xl font-extrabold text-quaternary',
        'page_subtitle' => 'text-tertiary mt-1',

        // Buttons
        'btn_primary' => 'bg-accent hover:bg-black text-white px-6 py-3 rounded-xl font-bold transition-colors inline-flex items-center justify-center gap-2 shadow-sm',
        'btn_secondary' => 'bg-secondary/30 hover:bg-accent text-quaternary hover:text-white px-4 py-2 rounded-xl font-semibold transition-colors border border-extra/50 hover:border-accent inline-flex items-center gap-2 text-sm',

        // Sections
        'section_title' => 'text-2xl font-bold text-quaternary mb-6 flex items-center gap-2',

        // Policy Cards
        'grid_container' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6',
        'card' => 'bg-white rounded-3xl p-6 shadow-sm border border-extra/30 hover:shadow-lg transition-all relative overflow-hidden flex flex-col',
        'card_header' => 'flex justify-between items-start mb-4 border-b border-extra/30 pb-4',

        // Dynamic Status Badge
        'status_active' => 'bg-green-100 text-green-700 border-green-200',
        'status_pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'status_expired' => 'bg-red-100 text-red-700 border-red-200',

        // Card Content
        'plan_title' => 'text-xl font-extrabold text-quaternary mb-1',
        'policy_folio' => 'text-xs font-mono text-tertiary tracking-wider',
        'info_grid' => 'grid grid-cols-1 gap-y-3 mt-4 flex-1',
        'info_row' => 'flex justify-between items-center py-2 border-b border-extra/20 last:border-0',
        'info_label' => 'text-sm font-semibold text-tertiary flex items-center gap-2',
        'info_value' => 'text-sm font-bold text-quaternary text-right',

        // Call to Action Banner
        'cta_banner' => 'bg-quaternary text-white rounded-3xl p-8 md:p-10 relative overflow-hidden shadow-xl flex flex-col md:flex-row items-center justify-between gap-8',
        'cta_pattern' => 'absolute inset-0 opacity-10',
    ];

@endphp

<x-app-layout>
    <x-slot name="content">
        <div class="{{ $styles['page_container'] }}">

            <!-- Encabezado -->
            <header class="{{ $styles['header_section'] }}">
                <div>
                    <h1 class="{{ $styles['page_title'] }}">Mis Pólizas</h1>
                    <p class="{{ $styles['page_subtitle'] }}">Consulta y gestiona las coberturas de tus vehículos.</p>
                </div>
            </header>

            <!-- CTA Banner "Adquirir Póliza" para vehículos sin seguro -->
            <section class="{{ $styles['cta_banner'] }}">
                <svg class="{{ $styles['cta_pattern'] }}" fill="currentColor" viewBox="0 0 100 100"
                    preserveAspectRatio="none">
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5" />
                    </pattern>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>

                <div class="relative z-10 max-w-2xl">
                    <span
                        class="inline-flex items-center gap-1 bg-accent bg-opacity-30 text-accent-light px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 border border-accent">
                        Nuevas Opciones
                    </span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4 leading-tight">¿Tienes un vehículo
                        sin cobertura?</h2>
                    <p class="text-secondary opacity-90 text-lg">Adquiere o actualiza una póliza en minutos. Protege tu
                        patrimonio con nuestros planes adaptables a tus necesidades y presupuesto.</p>
                </div>

                <div class="relative z-10 shrink-0 w-full md:w-auto">
                    <a href="{{ route('myPolicies.create') }}" class="{{ $styles['btn_primary'] }} !px-8 !py-4 text-lg w-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        Adquirir Seguro
                    </a>
                </div>
            </section>

            <!-- Listado de Pólizas Actuales -->
            <section>
                <h2 class="{{ $styles['section_title'] }}">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    Pólizas Contratadas
                </h2>

                <div class="{{ $styles['grid_container'] }}">
                    @foreach($policies as $policy)
                        @php
                            $label = method_exists($policy->status, 'label') ? $policy->status->label() : $policy->status->value;
                            $badgeStyle = match ($label) {
                                'Activa', 'Activo' => $styles['status_active'],
                                'Por Vencer', 'Pendiente' => $styles['status_pending'],
                                'Vencida', 'Inactiva' => $styles['status_expired'],
                                default => 'bg-gray-100 text-gray-700 border-gray-200'
                            };
                            $info = $policy->plan->info ?? [];
                            $danosMat = $info['deducible_danos'] ?? 'N/A';
                            // If robo total exists in cobertura_vehiculo, show it
                            $roboT = 'N/D';
                            if (isset($info['cobertura_vehiculo']['robo_total'])) {
                                $roboT = $info['cobertura_vehiculo']['robo_total'] ? ($info['deducible_robo'] ?? '10%') : 'No Amparado';
                            }
                        @endphp

                        <article class="{{ $styles['card'] }}">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-accent/5 rounded-bl-full -z-10"></div>

                            <div class="{{ $styles['card_header'] }}">
                                <div>
                                    <h3 class="{{ $styles['plan_title'] }}">{{ $policy->plan->name }}</h3>
                                    <p class="{{ $styles['policy_folio'] }}">Folio: {{ $policy->folio }}</p>
                                </div>
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-bold border {{ $badgeStyle }} whitespace-nowrap">
                                    {{ $label }}
                                </span>
                            </div>

                            <div class="{{ $styles['info_grid'] }}">
                                <!-- Vehículo -->
                                <div class="{{ $styles['info_row'] }} border-none !pb-1">
                                    <span class="{{ $styles['info_label'] }}">
                                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                        Vehículo
                                    </span>
                                </div>
                                <div class="text-base font-extrabold text-quaternary mb-2 mt-0">
                                    {{ $policy->vehicle->vehicleModel->brand }} {{ $policy->vehicle->vehicleModel->sub_brand }} ({{ $policy->vehicle->vehicleModel->year }})
                                </div>

                                <!-- Vigencia -->
                                <div class="{{ $styles['info_row'] }}">
                                    <span class="{{ $styles['info_label'] }}">Vigencia</span>
                                    <span class="{{ $styles['info_value'] }}">{{ $policy->begin_validity->format('d/M/Y') }} a
                                        {{ $policy->end_validity->format('d/M/Y') }}</span>
                                </div>

                                <!-- Deducibles -->
                                <div class="{{ $styles['info_row'] }}">
                                    <span class="{{ $styles['info_label'] }}">Daños Materiales</span>
                                    <span class="{{ $styles['info_value'] }}">{{ $danosMat }}</span>
                                </div>
                                <div class="{{ $styles['info_row'] }}">
                                    <span class="{{ $styles['info_label'] }}">Robo Total</span>
                                    <span class="{{ $styles['info_value'] }}">{{ $roboT }}</span>
                                </div>
                            </div>

                            <!-- Botones Acciones -->
                            <div class="mt-6 flex gap-3 pt-4 border-t border-extra/30">
                                <a href="#" class="{{ $styles['btn_secondary'] }} flex-1 justify-center">
                                    Detalles
                                </a>
                                @if($label === 'Por Vencer' || $label === 'Vencida')
                                    <a href="#" class="{{ $styles['btn_primary'] }} flex-1 justify-center !py-2 !px-4 text-sm">
                                        Renovar
                                    </a>
                                @endif
                                <a href="#"
                                    class="p-2 bg-secondary/20 hover:bg-black text-quaternary hover:text-white rounded-xl transition-colors border border-extra/50"
                                    title="Descargar Póliza (PDF)">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

        </div>
    </x-slot>
</x-app-layout>