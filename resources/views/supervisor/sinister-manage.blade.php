@php
    $styles = [
        'page_container' => 'w-full max-w-5xl mx-auto space-y-8 pb-12',

        // Header
        'header_section' => 'flex flex-col md:flex-row justify-between items-start md:items-center py-4 gap-4',
        'page_title' => 'text-3xl font-extrabold text-quaternary flex items-center gap-3',
        'page_subtitle' => 'text-tertiary mt-1',

        // Sections
        'section_card' => 'bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-extra/30 relative overflow-hidden mb-6',
        'section_title' => 'text-xl font-bold text-quaternary mb-6 flex items-center gap-2 border-b border-extra/30 pb-4',

        // Status Large Dropdown (Supervisor Active)
        'status_container' => 'bg-quaternary rounded-2xl p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden shadow-lg',

        // Inputs
        'input_group' => 'flex flex-col gap-1.5',
        'label' => 'text-sm font-semibold text-tertiary',
        'input' => 'w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary',
        'input_readonly' => 'w-full px-4 py-3 bg-secondary/30 text-tertiary font-medium rounded-xl border border-transparent cursor-not-allowed',
        'textarea_readonly' => 'w-full px-4 py-3 bg-secondary/30 text-tertiary font-medium rounded-xl border border-transparent cursor-not-allowed resize-none min-h-[120px]',

        // Buttons
        'btn_primary' => 'bg-accent hover:bg-black text-white px-8 py-3 rounded-xl font-bold transition-colors inline-flex items-center gap-2 shadow-sm',
        'btn_secondary' => 'bg-white hover:bg-secondary/20 text-quaternary px-6 py-3 rounded-xl font-semibold transition-colors border border-extra/50',

        // Gallery 
        'gallery_grid' => 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4',
        'gallery_item' => 'aspect-square rounded-2xl bg-secondary/20 border border-extra/30 relative group overflow-hidden pointer-events-none', // Pointer events none so it matches edit view (no actions inside)
        'gallery_img' => 'w-full h-full object-cover',
    ];

    // Dummy data to manage
    $sinister = [
        'id' => 101,
        'folio' => 'SIN-2023-089A',
        'status' => 'En Revisión', // 'Pendiente', 'En Revisión', 'Aprobado', 'Rechazado', 'Cerrado'
        'occur_date' => '2023-11-05T14:30',
        'report_date' => '2023-11-05T15:00',
        'close_date' => null,
        'ublication' => 'Av. Universidad 1000, CDMX',
        'description' => 'Colisión trasera leve en semáforo. Daños en parachoques y cajuela. Ambas partes presentes y conscientes.',
        'policy' => 'POL-8273-ABCD-1029',
        'vehicle' => 'Ford Mustang GT (2024)',
        'insured' => 'Juan Pérez'
    ];

    $evidence = [
        'https://images.unsplash.com/photo-1543398933-2195f2fc4de7?w=400&q=80',
        'https://images.unsplash.com/photo-1520108398463-5490bc89a7da?w=400&q=80',
        'https://images.unsplash.com/photo-1600587713431-70fb905c088f?w=400&q=80',
    ];
@endphp

<x-app-layout>
    <x-slot name="content">
        <!-- x-data controlling status to show/hide close_date dynamically -->
        <div class="{{ $styles['page_container'] }}" x-data="{ status: '{{ $sinister['status'] }}' }">

            <header class="{{ $styles['header_section'] }}">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ url()->previous() }}"
                            class="w-10 h-10 rounded-xl bg-white border border-extra/50 flex items-center justify-center text-tertiary hover:text-accent hover:border-accent transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <h1 class="{{ $styles['page_title'] }}">Dictaminar Siniestro</h1>
                    </div>
                    <p class="{{ $styles['page_subtitle'] }}">Revisando folio <strong
                            class="font-mono text-quaternary">{{ $sinister['folio'] }}</strong> • Dictaminador Oficial
                    </p>
                </div>
            </header>

            <form action="#" method="POST">
                @csrf
                @method('PUT')

                <!-- THE ONE DIFFERENCE: Supervisor CAN edit status, so we put the big Status Dropdown here from the original layout -->
                <div class="{{ $styles['status_container'] }} mb-8">
                    <!-- Decoración -->
                    <svg class="absolute inset-0 w-full h-full opacity-5 pointer-events-none text-white"
                        fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <pattern id="lines" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M0 10L10 0" stroke="currentColor" stroke-width="1" fill="none" />
                        </pattern>
                        <rect width="100%" height="100%" fill="url(#lines)" />
                    </svg>

                    <div class="relative z-10 space-y-1">
                        <h2 class="text-white text-2xl font-extrabold flex items-center gap-2">Cambiar Estatus del
                            Siniestro</h2>
                        <p class="text-white/70 text-sm max-w-sm">Determine el estatus operativo para este incidente.
                        </p>
                    </div>

                    <div class="relative z-10 w-full md:w-auto">
                        <div class="relative">
                            <select id="status" name="status" x-model="status"
                                class="appearance-none bg-white text-quaternary text-lg font-bold px-6 py-4 rounded-xl shadow-[0_0_20px_rgba(255,255,255,0.1)] w-full md:w-72 cursor-pointer outline-none focus:ring-4 focus:ring-accent transition-all border-none">
                                <option value="Pendiente" {{ $sinister['status'] == 'Pendiente' ? 'selected' : '' }}>
                                    Pendiente</option>
                                <option value="En Revisión" {{ $sinister['status'] == 'En Revisión' ? 'selected' : '' }}>
                                    En Revisión</option>
                                <option value="Aprobado" {{ $sinister['status'] == 'Aprobado' ? 'selected' : '' }}>Aprobar
                                    (Procedente)</option>
                                <option value="Rechazado" {{ $sinister['status'] == 'Rechazado' ? 'selected' : '' }}>
                                    Rechazar (Improcedente)</option>
                                <option value="Cerrado" {{ $sinister['status'] == 'Cerrado' ? 'selected' : '' }}>Cerrar
                                    Expediente</option>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-quaternary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Vinculada (Solo Lectura) -->
                <div class="{{ $styles['section_card'] }}">
                    <div class="flex justify-between items-start mb-6 border-b border-extra/30 pb-4">
                        <h2 class="text-xl font-bold text-quaternary">Contexto de la Póliza</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}">Póliza Involucrada</label>
                            <input type="text" value="{{ $sinister['policy'] }}" class="{{ $styles['input_readonly'] }}"
                                readonly disabled>
                        </div>
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}">Vehículo</label>
                            <input type="text" value="{{ $sinister['vehicle'] }}"
                                class="{{ $styles['input_readonly'] }}" readonly disabled>
                        </div>
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}">Asegurado</label>
                            <input type="text" value="{{ $sinister['insured'] }}"
                                class="{{ $styles['input_readonly'] }}" readonly disabled>
                        </div>
                    </div>
                </div>

                <!-- Detalles y Fechas (TODAS LECTURA PARA SUPERVISOR EXCEPTO CIERRE DINÁMICO) -->
                <div class="{{ $styles['section_card'] }}">
                    <h2 class="{{ $styles['section_title'] }}">Detalles Técnicos y Relato</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="occur_date">Fecha de Ocurrencia</label>
                            <input type="datetime-local" id="occur_date" name="occur_date"
                                class="{{ $styles['input_readonly'] }}" value="{{ $sinister['occur_date'] }}" readonly
                                disabled>
                        </div>

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="report_date">Fecha de Reporte</label>
                            <input type="datetime-local" id="report_date" name="report_date"
                                class="{{ $styles['input_readonly'] }}" value="{{ $sinister['report_date'] }}" readonly
                                disabled>
                        </div>

                        <!-- Fecha de Cierre (Dinámica/Writeable only when closed) -->
                        <div class="{{ $styles['input_group'] }}" x-show="status === 'Cerrado'" x-cloak>
                            <label class="{{ $styles['label'] }} text-tertiary flex items-center gap-1"
                                for="close_date">
                                <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                                Fecha de Cierre Oficial (Asignar)
                            </label>
                            <input type="datetime-local" id="close_date" class="{{ $styles['input'] }}"
                                value="{{ $sinister['close_date'] ?? date('Y-m-d\TH:i') }}"
                                x-bind:disabled="status !== 'Cerrado'">
                        </div>
                        <div class="lg:col-span-1" x-show="status !== 'Cerrado'"></div> <!-- Spacer -->

                        <div class="{{ $styles['input_group'] }} md:col-span-2 lg:col-span-3">
                            <label class="{{ $styles['label'] }}" for="ublication">Ubicación del Siniestro</label>
                            <input type="text" id="ublication" name="ublication" value="{{ $sinister['ublication'] }}"
                                class="{{ $styles['input_readonly'] }}" readonly disabled>
                        </div>

                        <div class="{{ $styles['input_group'] }} md:col-span-2 lg:col-span-3 mt-2">
                            <label class="{{ $styles['label'] }}" for="description">Relato / Dictamen del
                                Ajustador</label>
                            <textarea id="description" name="description"
                                class="{{ $styles['textarea_readonly'] }} text-sm" readonly
                                disabled>{{ $sinister['description'] }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Evidencia Visual (Visualización sin botones edición) -->
                <div class="{{ $styles['section_card'] }}">
                    <div
                        class="flex flex-col sm:flex-row justify-between sm:items-end mb-4 border-b border-extra/30 pb-4">
                        <h2 class="text-xl font-bold text-quaternary flex items-center gap-2">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Evidencia Almacenada
                        </h2>
                    </div>

                    <div class="{{ $styles['gallery_grid'] }}">
                        <!-- Iterate stored evidence -->
                        @foreach($evidence as $index => $img_url)
                            <div class="{{ $styles['gallery_item'] }}">
                                <img src="{{ $img_url }}" class="{{ $styles['gallery_img'] }}" alt="Evidencia">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Botones Finales -->
                <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-extra/30">
                    <button type="submit" class="{{ $styles['btn_primary'] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Guardar Resolución
                    </button>
                </div>
            </form>

        </div>
    </x-slot>
</x-app-layout>