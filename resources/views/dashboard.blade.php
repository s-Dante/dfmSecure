@php
$styles = [
'page_container' => 'w-full max-w-7xl mx-auto space-y-8',
'header_section' => 'flex flex-col md:flex-row justify-between items-start md:items-center py-4',
'page_title' => 'text-3xl font-extrabold text-quaternary',
'page_subtitle' => 'text-tertiary mt-1',

// Stats Grid
'stats_grid' => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6',
'stat_card' => 'bg-white p-6 rounded-2xl shadow-lg border border-transparent hover:border-extra transition-all flex items-center gap-4',
'stat_icon_wrapper' => 'w-14 h-14 rounded-full flex items-center justify-center shrink-0',
'stat_info' => 'flex flex-col',
'stat_value' => 'text-3xl font-extrabold text-quaternary leading-tight',
'stat_label' => 'text-sm font-medium text-tertiary uppercase tracking-wider',

// Cards Grid
'section_title' => 'text-xl font-bold text-quaternary mb-6 border-b border-extra/50 pb-2',
'cards_grid' => 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6',
];

// Dynamic Statistics Mapping
$statsObj = (object) $stats;

$statistics = [
    [
        'label' => 'Total Siniestros',
        'value' => $statsObj->total_sinisters ?? 0,
        'iconBg' => 'bg-quaternary/10',
        'iconColor' => 'text-quaternary',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'
    ],
    [
        'label' => 'En Revisión',
        'value' => $statsObj->in_review_sinisters ?? $statsObj->in_review_sinister ?? 0,
        'iconBg' => 'bg-yellow-500/10',
        'iconColor' => 'text-yellow-600',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
    ],
    [
        'label' => 'Aprobados',
        'value' => $statsObj->approved_sinisters ?? $statsObj->aproved_sinisters ?? 0,
        'iconBg' => 'bg-green-500/10',
        'iconColor' => 'text-green-600',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
    ],
    [
        'label' => 'Rechazados',
        'value' => $statsObj->rejected_sinisters ?? 0,
        'iconBg' => 'bg-red-500/10',
        'iconColor' => 'text-red-600',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
    ],
];
@endphp

<x-app-layout>
    <x-slot name="content">
        <div class="{{ $styles['page_container'] }}">

            <!-- Encabezado de la página -->
            <header class="{{ $styles['header_section'] }}">
                <div>
                    <h1 class="{{ $styles['page_title'] }}">Resumen de Siniestros</h1>
                    <p class="{{ $styles['page_subtitle'] }}">Monitorea el estado y avance de las reclamaciones.</p>
                    @if(auth()->user()->isInsured())
                    <span>Mis siniestros</span>
                    @elseif(auth()->user()->isAdjuster())
                    <span>Siniestros asignados</span>
                    @elseif(auth()->user()->isSupervisor())
                    <span>Siniestros por supervisor</span>
                    @elseif(auth()->user()->isAdmin())
                    <span>Todos los siniestros</span>
                    @endif
                </div>
                <!-- Aquí podría ir un selector de fechas o botón de acción rápido en el futuro -->
            </header>

            <!-- Sección 1: Estadísticas Numéricas -->
            <section class="{{ $styles['stats_grid'] }}">
                @foreach ($statistics as $stat)
                <article class="{{ $styles['stat_card'] }}">
                    <div class="{{ $styles['stat_icon_wrapper'] }} {{ $stat['iconBg'] }}">
                        <svg class="w-7 h-7 {{ $stat['iconColor'] }}" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            {!! $stat['icon'] !!}
                        </svg>
                    </div>
                    <div class="{{ $styles['stat_info'] }}">
                        <span class="{{ $styles['stat_value'] }}">{{ $stat['value'] }}</span>
                        <span class="{{ $styles['stat_label'] }}">{{ $stat['label'] }}</span>
                    </div>
                </article>
                @endforeach
            </section>

            <!-- Sección 2: Siniestros Recientes -->
            <section>
                <h2 class="{{ $styles['section_title'] }}">Siniestros Recientes</h2>
                <div class="{{ $styles['cards_grid'] }}">
                    @forelse ($sinisters as $sinister)
                        @php
                            $isModel = $sinister instanceof \Illuminate\Database\Eloquent\Model;

                            // --- Imagen ---
                            $image = null;
                            if ($isModel && $sinister->multimedia && $sinister->multimedia->count() > 0) {
                                $media = $sinister->multimedia->first();
                                if (!empty($media->blob_file)) {
                                    // LONGBLOB: servir via ruta de streaming
                                    $image = route('media.sinister', $media->id);
                                } elseif (!empty($media->path_file)) {
                                    $image = str_starts_with($media->path_file, 'http')
                                        ? $media->path_file
                                        : asset($media->path_file);
                                }
                            }

                            // --- Nombre del vehículo ---
                            $vehicleName = 'Vehículo N/A';
                            if ($isModel && !empty($sinister->policy) && !empty($sinister->policy->vehicle)) {
                                $v = $sinister->policy->vehicle;
                                $vm = $v->vehicleModel;
                                $vehicleName = trim(($vm->brand ?? '') . ' ' . ($vm->sub_brand ?? '') . ' ' . ($vm->year ?? ''));
                                if ($vehicleName === '') $vehicleName = 'Vehículo N/A';
                            }

                            // --- Status ---
                            $valStatusRaw = $isModel ? $sinister->status : ($sinister->status ?? 'in_review');
                            $valStatus = $valStatusRaw instanceof \BackedEnum ? $valStatusRaw->value : (string) $valStatusRaw;
                            $statusDisplay = match($valStatus) {
                                'in_review'                    => 'En Revisión',
                                'reported'                     => 'Reportado',
                                'approved'                     => 'Aprobado',
                                'approved_with_deductible'     => 'Aprobado c/Deducible',
                                'approved_without_deductible'  => 'Aprobado s/Deducible',
                                'applies_payment_for_repairs'  => 'Pago por Reparación',
                                'rejected'                     => 'Rechazado',
                                'total_loss'                   => 'Pérdida Total',
                                'closed'                       => 'Cerrado',
                                default                        => ucfirst($valStatus)
                            };

                            // --- Folio (sinister_number de la BD) ---
                            $folio = $isModel
                                ? ($sinister->sinister_number ?? ('SIN-' . str_pad($sinister->id, 5, '0', STR_PAD_LEFT)))
                                : ($sinister->sinister_number ?? 'SIN-?????');

                            // --- URL detalle ---
                            $detailUrl = route('sinisterDetail', $sinister->id);
                        @endphp

                        @if($image)
                            <x-sinister-card image="{{ $image }}" alt="Vehículo siniestrado"
                                folio="{{ $folio }}" vehicle="{{ $vehicleName }}" status="{{ $statusDisplay }}"
                                url="{{ $detailUrl }}" />
                        @else
                            {{-- Placeholder cuando no hay imagen --}}
                            <article class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-extra/30 flex flex-col group">
                                <div class="w-full h-48 bg-secondary/20 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-extra/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="p-5 flex flex-col flex-1">
                                    <div class="flex justify-between items-start mb-2 gap-2">
                                        <h3 class="text-lg font-bold text-quaternary">{{ $folio }}</h3>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-gray-100 text-gray-700 border-gray-200">{{ $statusDisplay }}</span>
                                    </div>
                                    <p class="text-sm text-tertiary mb-4">Vehículo: {{ $vehicleName }}</p>
                                    <div class="flex-1"></div>
                                    <a href="{{ $detailUrl }}" class="w-full py-2.5 bg-secondary/30 hover:bg-accent text-quaternary hover:text-white text-sm font-semibold rounded-xl transition-colors text-center border border-extra/50 hover:border-accent">
                                        Resumen del Siniestro
                                    </a>
                                </div>
                            </article>
                        @endif
                    @empty
                        <div class="col-span-full py-10 text-center text-tertiary bg-white border border-extra border-dashed rounded-2xl">
                            No se encontraron siniestros recientes.
                        </div>
                    @endforelse
                </div>
                
                @if($sinisters instanceof \Illuminate\Pagination\LengthAwarePaginator && $sinisters->hasPages())
                    <div class="mt-6">
                        {{ $sinisters->links() }}
                    </div>
                @endif
            </section>

        </div>
    </x-slot>
</x-app-layout>