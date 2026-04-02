@php
    $styles = [
        'page_container' => 'w-full max-w-7xl mx-auto space-y-8 pb-10',
        'header_section' => 'flex flex-col md:flex-row justify-between items-start md:items-center py-4',
        'page_title'     => 'text-3xl font-extrabold text-quaternary',
        'page_subtitle'  => 'text-tertiary mt-1',

        'filter_card'  => 'bg-white p-6 rounded-3xl shadow-sm border border-extra/30 w-full',
        'filter_title' => 'text-lg font-bold text-quaternary mb-4 flex items-center gap-2',
        'filter_grid'  => 'grid grid-cols-1 md:grid-cols-12 gap-4 items-end',
        'input_group'  => 'flex flex-col md:col-span-3',
        'label'        => 'text-sm font-semibold text-quaternary mb-1.5',
        'input'        => 'w-full px-4 py-2.5 rounded-xl border border-extra/50 bg-secondary/10 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary placeholder-tertiary/70',
        'select'       => 'w-full px-4 py-2.5 rounded-xl border border-extra/50 bg-secondary/10 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary',

        'btn_group'     => 'flex gap-3 md:col-span-12 md:justify-end',
        'btn_primary'   => 'px-6 py-2.5 bg-accent text-white font-semibold rounded-xl hover:bg-black transition-colors',
        'btn_secondary' => 'px-6 py-2.5 bg-secondary text-quaternary font-semibold rounded-xl hover:bg-extra/50 transition-colors',

        'results_container' => 'mt-2',
        'results_header'    => 'flex justify-between items-end mb-6 border-b border-extra/50 pb-2',
        'results_title'     => 'text-xl font-bold text-quaternary',
        'results_count'     => 'text-sm font-semibold text-tertiary',
        'cards_grid'        => 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6',
    ];

    $statusOptions = [
        ''                             => 'Todos los estados',
        'reported'                     => 'Reportado',
        'in_review'                    => 'En Revisión',
        'approved'                     => 'Aprobado',
        'approved_with_deductible'     => 'Aprobado c/Deducible',
        'approved_without_deductible'  => 'Aprobado s/Deducible',
        'applies_payment_for_repairs'  => 'Pago por Reparación',
        'rejected'                     => 'Rechazado',
        'total_loss'                   => 'Pérdida Total',
        'closed'                       => 'Cerrado',
    ];
@endphp

<x-app-layout>
    <x-slot name="content">
        <div class="{{ $styles['page_container'] }}">

            {{-- Encabezado --}}
            <header class="{{ $styles['header_section'] }}">
                <div>
                    <h1 class="{{ $styles['page_title'] }}">Consulta de Siniestros</h1>
                    <p class="{{ $styles['page_subtitle'] }}">Filtre y busque información sobre las reclamaciones registradas.</p>
                </div>
            </header>

            {{-- Filtros --}}
            <section class="{{ $styles['filter_card'] }}">
                <h2 class="{{ $styles['filter_title'] }}">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtros de Búsqueda
                </h2>
                <form action="{{ route('consultation') }}" method="GET" class="{{ $styles['filter_grid'] }}">
                    <div class="{{ $styles['input_group'] }}">
                        <label for="folio" class="{{ $styles['label'] }}">Folio / Siniestro</label>
                        <input type="text" id="folio" name="folio" value="{{ request('folio') }}"
                            placeholder="Ej. SIN-00001" class="{{ $styles['input'] }}">
                    </div>
                    <div class="{{ $styles['input_group'] }}">
                        <label for="status" class="{{ $styles['label'] }}">Estado</label>
                        <select id="status" name="status" class="{{ $styles['select'] }}">
                            @foreach($statusOptions as $val => $label)
                                <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="{{ $styles['input_group'] }}">
                        <label for="fecha_inicio" class="{{ $styles['label'] }}">Fecha de Inicio</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="{{ $styles['input'] }}">
                    </div>
                    <div class="{{ $styles['input_group'] }}">
                        <label for="fecha_fin" class="{{ $styles['label'] }}">Fecha de Fin</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" value="{{ request('fecha_fin') }}" class="{{ $styles['input'] }}">
                    </div>
                    <div class="{{ $styles['btn_group'] }}">
                        <button type="submit" class="{{ $styles['btn_primary'] }}">Buscar</button>
                        <a href="{{ route('consultation') }}" class="{{ $styles['btn_secondary'] }}">Limpiar</a>
                    </div>
                </form>
            </section>

            {{-- Resultados --}}
            <section class="{{ $styles['results_container'] }}">
                <div class="{{ $styles['results_header'] }}">
                    <h2 class="{{ $styles['results_title'] }}">Resultados Obtenidos</h2>
                    <span class="{{ $styles['results_count'] }}">Mostrando {{ $sinisters->count() }} de {{ $sinisters->total() }} siniestros</span>
                </div>

                @if($sinisters->count() > 0)
                    <div class="{{ $styles['cards_grid'] }}">
                        @foreach($sinisters as $sinister)
                            @php
                                $image = null;
                                if ($sinister->multimedia && $sinister->multimedia->count() > 0) {
                                    $media = $sinister->multimedia->first();
                                    if (!empty($media->blob_file)) {
                                        $image = route('media.sinister', $media->id);
                                    } elseif (!empty($media->path_file)) {
                                        $image = str_starts_with($media->path_file, 'http') ? $media->path_file : asset($media->path_file);
                                    }
                                }
                                $v   = $sinister->policy?->vehicle;
                                $vm  = $v?->vehicleModel;
                                $vehicleName = $vm ? trim(($vm->brand ?? '') . ' ' . ($vm->sub_brand ?? '') . ' ' . ($vm->year ?? '')) : 'Vehículo N/A';
                                $valStatusRaw  = $sinister->status instanceof \BackedEnum ? $sinister->status->value : (string)$sinister->status;
                                $statusDisplay = match($valStatusRaw) {
                                    'in_review' => 'En Revisión', 'reported' => 'Reportado',
                                    'approved'  => 'Aprobado',
                                    'approved_with_deductible'    => 'Aprobado c/Deducible',
                                    'approved_without_deductible' => 'Aprobado s/Deducible',
                                    'applies_payment_for_repairs' => 'Pago por Reparación',
                                    'rejected'   => 'Rechazado',
                                    'total_loss' => 'Pérdida Total',
                                    'closed'     => 'Cerrado',
                                    default      => ucfirst($valStatusRaw),
                                };
                                $folio     = $sinister->sinister_number ?? ('SIN-' . str_pad($sinister->id, 5, '0', STR_PAD_LEFT));
                                $detailUrl = route('sinisterDetail', $sinister->id);
                            @endphp

                            @if($image)
                                <x-sinister-card :image="$image" alt="Vehículo siniestrado"
                                    :folio="$folio" :vehicle="$vehicleName" :status="$statusDisplay"
                                    :url="$detailUrl" />
                            @else
                                <article class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-extra/30 flex flex-col group">
                                    <div class="w-full h-48 bg-secondary/20 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-extra/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="p-5 flex flex-col flex-1">
                                        <div class="flex justify-between items-start mb-2 gap-2">
                                            <h3 class="text-lg font-bold text-quaternary">{{ $folio }}</h3>
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-gray-100 text-gray-700">{{ $statusDisplay }}</span>
                                        </div>
                                        <p class="text-sm text-tertiary mb-4">Vehículo: {{ $vehicleName }}</p>
                                        <div class="flex-1"></div>
                                        <a href="{{ $detailUrl }}"
                                            class="w-full py-2.5 bg-secondary/30 hover:bg-accent text-quaternary hover:text-white text-sm font-semibold rounded-xl transition-colors text-center border border-extra/50 hover:border-accent">
                                            Resumen del Siniestro
                                        </a>
                                    </div>
                                </article>
                            @endif
                        @endforeach
                    </div>

                    {{-- Paginación --}}
                    @if($sinisters->hasPages())
                        <div class="mt-8">
                            {{ $sinisters->links() }}
                        </div>
                    @endif

                @else
                    <div class="bg-white p-12 rounded-3xl border border-extra/30 flex flex-col items-center justify-center text-center">
                        <svg class="w-16 h-16 text-extra/50 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-quaternary mb-2">No se encontraron resultados</h3>
                        <p class="text-tertiary">Intenta ajustar los filtros para encontrar siniestros.</p>
                    </div>
                @endif
            </section>

        </div>
    </x-slot>
</x-app-layout>