@props(['image', 'alt' => 'Vehículo', 'folio', 'vehicle', 'status', 'url'])

@php
    // Determine the badge color based on status
    $badgeColor = match (strtolower($status)) {
        'aprobado', 'cerrado' => 'bg-green-100 text-green-700 border-green-200',
        'en revisión', 'activo', 'pendiente' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'rechazado' => 'bg-red-100 text-red-700 border-red-200',
        default => 'bg-gray-100 text-gray-700 border-gray-200',
    };

    $styles = [
        'card' => 'bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-extra/30 flex flex-col group',
        'image_container' => 'w-full h-48 relative overflow-hidden bg-secondary',
        'image' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105',
        'content_container' => 'p-5 flex flex-col flex-1',

        'header' => 'flex justify-between items-start mb-2 gap-2',
        'folio' => 'text-lg font-bold text-quaternary leading-tight',
        'badge' => "px-2.5 py-0.5 rounded-full text-xs font-semibold border $badgeColor",

        'vehicle' => 'text-sm text-tertiary mb-4 line-clamp-1',

        'spacer' => 'flex-1', // Pushes the button to the bottom if cards have different text heights

        'button' => 'w-full py-2.5 bg-secondary/30 hover:bg-accent text-quaternary hover:text-white text-sm font-semibold rounded-xl transition-colors text-center border border-extra/50 hover:border-accent',
    ];
@endphp

<article class="{{ $styles['card'] }}">
    <!-- Card Image -->
    <div class="{{ $styles['image_container'] }}">
        <img src="{{ $image }}" alt="{{ $alt }}" class="{{ $styles['image'] }}">
    </div>

    <!-- Card Content -->
    <div class="{{ $styles['content_container'] }}">

        <div class="{{ $styles['header'] }}">
            <h3 class="{{ $styles['folio'] }}">Folio: {{ $folio }}</h3>
            <span class="{{ $styles['badge'] }}">{{ $status }}</span>
        </div>

        <p class="{{ $styles['vehicle'] }}">Vehículo: {{ $vehicle }}</p>

        <div class="{{ $styles['spacer'] }}"></div>

        <!-- Action Button -->
        <a href="{{ $url }}" class="{{ $styles['button'] }}">
            Resumen del Siniestro
        </a>
    </div>
</article>