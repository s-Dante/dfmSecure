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

    // Dummy Data for Statistics
    $statistics = [
        [
            'label' => 'Total Siniestros',
            'value' => '142',
            'iconBg' => 'bg-quaternary/10',
            'iconColor' => 'text-quaternary',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'
        ],
        [
            'label' => 'En Revisión',
            'value' => '38',
            'iconBg' => 'bg-yellow-500/10',
            'iconColor' => 'text-yellow-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        ],
        [
            'label' => 'Aprobados',
            'value' => '85',
            'iconBg' => 'bg-green-500/10',
            'iconColor' => 'text-green-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        ],
        [
            'label' => 'Rechazados',
            'value' => '19',
            'iconBg' => 'bg-red-500/10',
            'iconColor' => 'text-red-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        ],
    ];

    // Dummy Data for Sinister Cards
    $sinisterCards = [
        [
            'image' => 'https://www.toyota.mx/adobe/dynamicmedia/deliver/dm-aid--10dfa575-b7a6-4016-8c25-3ad4ceaf49ca/corolla-xle-cvt.png?preferwebp=true&quality=85',
            'folio' => 'SIN-2023-001',
            'vehicle' => 'Toyota Corolla 2022',
            'status' => 'Aprobado',
            'url' => route('sinisterDetail')
        ],
        [
            'image' => 'https://www.forddinastia.mx/Assets/ModelosNuevos/Img/Modelos/MUSTANG/25/coloresred/GRIS-CARBONO.png',
            'folio' => 'SIN-2023-002',
            'vehicle' => 'Ford Mustang 2024',
            'status' => 'En Revisión',
            'url' => route('sinisterDetail')
        ],
        [
            'image' => 'https://jeblamotors.com/aveo/img/azul-persuacion.webp',
            'folio' => 'SIN-2023-003',
            'vehicle' => 'Chevrolet Aveo 2023',
            'status' => 'Rechazado',
            'url' => route('sinisterDetail')
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
                    @foreach ($sinisterCards as $card)
                        <x-sinister-card image="{{ $card['image'] }}" alt="Vehículo siniestrado"
                            folio="{{ $card['folio'] }}" vehicle="{{ $card['vehicle'] }}" status="{{ $card['status'] }}"
                            url="{{ $card['url'] }}" />
                    @endforeach
                </div>
            </section>

        </div>
    </x-slot>
</x-app-layout>