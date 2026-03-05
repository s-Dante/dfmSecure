@php
    $styles = [
        'page_container' => 'w-full max-w-7xl mx-auto space-y-8 pb-10',

        // Header
        'header_section' => 'flex flex-col md:flex-row justify-between items-start md:items-center py-4',
        'page_title' => 'text-3xl font-extrabold text-quaternary',
        'page_subtitle' => 'text-tertiary mt-1',

        // Filter Card
        'filter_card' => 'bg-white p-6 rounded-3xl shadow-sm border border-extra/30 w-full',
        'filter_title' => 'text-lg font-bold text-quaternary mb-4 flex items-center gap-2',
        'filter_grid' => 'grid grid-cols-1 md:grid-cols-12 gap-6 items-end',
        'input_group' => 'flex flex-col md:col-span-3',
        'input_group_large' => 'flex flex-col md:col-span-4',
        'label' => 'text-sm font-semibold text-quaternary mb-1.5',
        'input' => 'w-full px-4 py-2.5 rounded-xl border border-extra/50 bg-secondary/10 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary placeholder-tertiary/70',

        // Buttons
        'btn_group' => 'flex gap-3 md:col-span-12 justify-end',
        'btn_primary' => 'px-6 py-2.5 bg-accent text-white font-semibold rounded-xl hover:bg-black transition-colors',
        'btn_secondary' => 'px-6 py-2.5 bg-secondary text-quaternary font-semibold rounded-xl hover:bg-extra/50 transition-colors',

        // Results Area
        'results_container' => 'mt-10',
        'results_header' => 'flex justify-between items-end mb-6 border-b border-extra/50 pb-2',
        'results_title' => 'text-xl font-bold text-quaternary',
        'results_count' => 'text-sm font-semibold text-tertiary',

        // Cards Grid (Reutilizado del Dashboard)
        'cards_grid' => 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6',
    ];

    // Dummy Data for Preview
    $siniestros = [
        [
            'image' => 'https://www.forddinastia.mx/Assets/ModelosNuevos/Img/Modelos/MUSTANG/25/coloresred/GRIS-CARBONO.png',
            'folio' => 'SIN-2023-002',
            'vehicle' => 'Ford Mustang 2024',
            'status' => 'En Revisión',
            'url' => route('sinisterManage')
        ],
        [
            'image' => 'https://jeblamotors.com/aveo/img/azul-persuacion.webp',
            'folio' => 'SIN-2023-003',
            'vehicle' => 'Chevrolet Aveo 2023',
            'status' => 'Rechazado',
            'url' => route('sinisterManage')
        ],
        [
            'image' => 'https://www.toyota.mx/adobe/dynamicmedia/deliver/dm-aid--10dfa575-b7a6-4016-8c25-3ad4ceaf49ca/corolla-xle-cvt.png?preferwebp=true&quality=85',
            'folio' => 'SIN-2023-001',
            'vehicle' => 'Toyota Corolla 2022',
            'status' => 'Aprobado',
            'url' => route('sinisterManage')
        ],
        [
            'image' => 'https://www.toyota.mx/adobe/dynamicmedia/deliver/dm-aid--10dfa575-b7a6-4016-8c25-3ad4ceaf49ca/corolla-xle-cvt.png?preferwebp=true&quality=85',
            'folio' => 'SIN-2023-104',
            'vehicle' => 'Nissan Sentra 2023',
            'status' => 'Aprobado',
            'url' => route('sinisterManage')
        ],
        [
            'image' => 'https://jeblamotors.com/aveo/img/azul-persuacion.webp',
            'folio' => 'SIN-2023-105',
            'vehicle' => 'Chevrolet Tahoe 2021',
            'status' => 'Pendiente',
            'url' => route('sinisterManage')
        ]
    ];
@endphp

<x-app-layout>
    <x-slot name="content">
        <div class="{{ $styles['page_container'] }}">

            <!-- Encabezado -->
            <header class="{{ $styles['header_section'] }}">
                <div>
                    <h1 class="{{ $styles['page_title'] }}">Búsqueda de Siniestros</h1>
                    <p class="{{ $styles['page_subtitle'] }}">Busque expedientes por diferentes criterios para proceder
                        a su dictamen.</p>
                </div>
            </header>

            <!-- Sección 1: Filtros (Modificados para Supervisor) -->
            <section class="{{ $styles['filter_card'] }}">
                <h2 class="{{ $styles['filter_title'] }}">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                    Filtros de Búsqueda
                </h2>

                <form action="#" method="GET" class="{{ $styles['filter_grid'] }}">

                    <!-- Palabra Clave / General -->
                    <div class="{{ $styles['input_group_large'] }}">
                        <label for="keyword" class="{{ $styles['label'] }}">Nombre, Vehículo o Póliza</label>
                        <input type="text" id="keyword" name="keyword" class="{{ $styles['input'] }}"
                            placeholder="Término de búsqueda...">
                    </div>

                    <!-- Fecha Inicio -->
                    <div class="{{ $styles['input_group'] }}">
                        <label for="fecha_inicio" class="{{ $styles['label'] }}">Desde (Fecha Re.)</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="{{ $styles['input'] }}">
                    </div>

                    <!-- Fecha Fin -->
                    <div class="{{ $styles['input_group'] }}">
                        <label for="fecha_fin" class="{{ $styles['label'] }}">Hasta (Fecha Re.)</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="{{ $styles['input'] }}">
                    </div>

                    <!-- Estatus opcional extra filter for supervisor if needed, keeping it minimal for now as requested or adding it as part of the grid -->
                    <div class="{{ $styles['input_group'] }} md:col-span-2">
                        <label for="status" class="{{ $styles['label'] }}">Estatus</label>
                        <select id="status" name="status" class="{{ $styles['input'] }} appearance-none">
                            <option value="">Todos</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="En Revisión">En Revisión</option>
                        </select>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="{{ $styles['btn_group'] }}">
                        <button type="submit" class="{{ $styles['btn_primary'] }}">Buscar</button>
                        <button type="reset" class="{{ $styles['btn_secondary'] }}">Limpiar</button>
                    </div>

                </form>
            </section>

            <!-- Sección 2: Resultados Reutilizando Sinister Card -->
            <section class="{{ $styles['results_container'] }}">
                <div class="{{ $styles['results_header'] }}">
                    <h2 class="{{ $styles['results_title'] }}">Resultados Obtenidos</h2>
                    <span class="{{ $styles['results_count'] }}">Mostrando {{ count($siniestros) }} siniestros</span>
                </div>

                @if(count($siniestros) > 0)
                    <div class="{{ $styles['cards_grid'] }}">
                        @foreach ($siniestros as $siniestro)
                            <x-sinister-card image="{{ $siniestro['image'] }}"
                                alt="Vehículo siniestrado {{ $siniestro['folio'] }}" folio="{{ $siniestro['folio'] }}"
                                vehicle="{{ $siniestro['vehicle'] }}" status="{{ $siniestro['status'] }}"
                                url="{{ $siniestro['url'] }}" />
                        @endforeach
                    </div>
                @else
                    <div
                        class="bg-white p-12 rounded-3xl border border-extra/30 flex flex-col items-center justify-center text-center">
                        <svg class="w-16 h-16 text-extra/50 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <h3 class="text-xl font-bold text-quaternary mb-2">No se encontraron resultados</h3>
                        <p class="text-tertiary">Intenta ajustar los criterios para buscar siniestros registrados.</p>
                    </div>
                @endif
            </section>

        </div>
    </x-slot>
</x-app-layout>