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

        // Inputs
        'input_group' => 'flex flex-col gap-1.5',
        'label' => 'text-sm font-semibold text-tertiary',
        'input' => 'w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary',
        'textarea' => 'w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary resize-y min-h-[120px]',

        // Buttons
        'btn_primary' => 'bg-accent hover:bg-black text-white px-8 py-3 rounded-xl font-bold transition-colors inline-flex items-center gap-2 shadow-sm',
        'btn_secondary' => 'bg-white hover:bg-secondary/20 text-quaternary px-6 py-3 rounded-xl font-semibold transition-colors border border-extra/50',

        // Drag & Drop Area
        'upload_area' => 'border-2 border-dashed border-extra/50 rounded-2xl p-8 flex flex-col items-center justify-center text-center hover:border-accent hover:bg-secondary/10 transition-colors cursor-pointer group',

        // Toggle/Tabs
        'toggle_container' => 'flex bg-secondary/20 p-1 rounded-xl w-full max-w-sm mb-4',
        'toggle_btn_active' => 'flex-1 py-2 px-4 rounded-lg bg-white text-quaternary font-bold shadow-sm text-sm text-center transition-all',
        'toggle_btn_inactive' => 'flex-1 py-2 px-4 rounded-lg text-tertiary font-medium hover:text-quaternary text-sm text-center transition-all cursor-pointer',
    ];

    // Dummy options for Policy Search (Simulation)
    $policies = [
        ['id' => 1, 'folio' => 'POL-8273-ABCD-1029', 'vehicle' => 'Ford Mustang GT (2024)', 'insured' => 'Juan Pérez'],
        ['id' => 2, 'folio' => 'POL-9182-WXYZ-4058', 'vehicle' => 'Toyota Yaris Sedan (2018)', 'insured' => 'María García'],
        ['id' => 3, 'folio' => 'POL-1928-QWER-7654', 'vehicle' => 'Chevrolet Aveo (2023)', 'insured' => 'Carlos López'],
    ];
@endphp

<x-app-layout>
    <x-slot name="content">
        <!-- x-data for managing UI state (tabs, dynamic fields) -->
        <div class="{{ $styles['page_container'] }}" x-data="{ uploadType: 'path' }">

            <!-- Encabezado -->
            <header class="{{ $styles['header_section'] }}">
                <div>
                    <h1 class="{{ $styles['page_title'] }}">
                        <div
                            class="w-12 h-12 bg-accent/10 rounded-2xl flex items-center justify-center text-accent shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        Apertura de Siniestro
                    </h1>
                    <p class="{{ $styles['page_subtitle'] }}">Completa el formulario inicial para registrar un nuevo
                        incidente.</p>
                </div>
            </header>

            <form action="#" method="POST">
                @csrf

                <!-- Sección 1: Enlace a Póliza / Vehículo -->
                <div class="{{ $styles['section_card'] }}">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-accent/5 rounded-bl-[100px] -z-10"></div>
                    <h2 class="{{ $styles['section_title'] }}">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        1. Identificación de Póliza
                    </h2>

                    <div class="grid grid-cols-1 gap-6">
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="policy_id">Buscar Póliza, VIN o Nombre del
                                Asegurado</label>

                            <!-- Simulación de un Autocomplete select -->
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-tertiary" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <select id="policy_id" name="policy_id"
                                    class="{{ $styles['input'] }} pl-11 appearance-none cursor-pointer" required>
                                    <option value="" disabled selected>Selecciona una póliza registrada...</option>
                                    @foreach($policies as $policy)
                                        <option value="{{ $policy['id'] }}">
                                            {{ $policy['folio'] }} - {{ $policy['vehicle'] }} ({{ $policy['insured'] }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-tertiary" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-tertiary mt-1">El sistema vinculará automáticamente el vehículo y al
                                asegurado a este reporte.</p>
                        </div>
                    </div>
                </div>

                <!-- Sección 2: Detalles del Incidente -->
                <div class="{{ $styles['section_card'] }}">
                    <h2 class="{{ $styles['section_title'] }}">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                            </path>
                        </svg>
                        2. Detalles del Incidente
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Fechas -->
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="occur_date">Fecha y Hora de Ocurrencia
                                (Aproximada)</label>
                            <input type="datetime-local" id="occur_date" name="occur_date"
                                class="{{ $styles['input'] }}" required>
                        </div>

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="report_date">Fecha y Hora de Reporte</label>
                            <!-- Idealmente prellenado con la hora actual -->
                            <input type="datetime-local" id="report_date" name="report_date"
                                class="{{ $styles['input'] }}" value="{{ date('Y-m-d\TH:i') }}" required>
                        </div>

                        <!-- Ubicación -->
                        <div class="{{ $styles['input_group'] }} md:col-span-2">
                            <label class="{{ $styles['label'] }}" for="ublication">Ubicación del Siniestro</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-tertiary" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="ublication" name="ublication"
                                    placeholder="Escribe la dirección, cruce de calles, carretera o referencias..."
                                    class="{{ $styles['input'] }} pl-11" required>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="{{ $styles['input_group'] }} md:col-span-2 mt-2">
                            <label class="{{ $styles['label'] }}" for="description">Descripción / Relato de los
                                Hechos</label>
                            <textarea id="description" name="description"
                                placeholder="Redacta cómo ocurrieron los hechos, partes involucradas, clima, etc..."
                                class="{{ $styles['textarea'] }}" required></textarea>
                        </div>

                    </div>
                </div>

                <!-- Sección 3: Evidencia Multimedia -->
                <div class="{{ $styles['section_card'] }}">
                    <h2 class="{{ $styles['section_title'] }}">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        3. Evidencia y Fotografía Inicial
                    </h2>

                    <!-- Toggle Storage Type -->
                    <div class="mb-6">
                        <label class="{{ $styles['label'] }} mb-2 block">Método de Almacenamiento de Archivos</label>
                        <div class="{{ $styles['toggle_container'] }}">
                            <div @click="uploadType = 'path'"
                                :class="uploadType === 'path' ? '{{ $styles['toggle_btn_active'] }}' : '{{ $styles['toggle_btn_inactive'] }}'">
                                URL / Archivo (Recomendado)
                            </div>
                            <div @click="uploadType = 'blob'"
                                :class="uploadType === 'blob' ? '{{ $styles['toggle_btn_active'] }}' : '{{ $styles['toggle_btn_inactive'] }}'">
                                Base de Datos (BLOB)
                            </div>
                        </div>
                        <input type="hidden" name="storage_preference" x-model="uploadType">

                        <!-- Contextual Help -->
                        <p x-show="uploadType === 'path'" class="text-xs text-tertiary">Los archivos se subirán al
                            servidor (Storage) y solo se guardará la ruta en la base de datos.</p>
                        <p x-show="uploadType === 'blob'" class="text-xs text-yellow-600 font-medium">Atención: Cargar
                            múltiples imágenes como BLOB puede afectar el rendimiento de la base de datos.</p>
                    </div>

                    <!-- Drag and Drop Area -->
                    <div class="{{ $styles['upload_area'] }}" onclick="document.getElementById('file_upload').click()">
                        <div
                            class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-quaternary mb-1">Cargar Fotografías y Videos</h3>
                        <p class="text-sm text-tertiary">Arrastra y suelta tus archivos aquí, o haz clic para explorar.
                        </p>
                        <p class="text-xs font-mono text-tertiary mt-4">Soporta: JPG, PNG, MP4 (Máx 20MB p/archivo)</p>
                        <input type="file" id="file_upload" name="files[]" multiple class="hidden"
                            accept="image/*,video/mp4">
                    </div>

                </div>

                <!-- Bottom Actions -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t border-extra/30 pt-6">
                    <a href="{{ route('dashboard') }}" class="{{ $styles['btn_secondary'] }} text-center">
                        Descartar Registro
                    </a>
                    <button type="submit" class="{{ $styles['btn_primary'] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Generar Siniestro y Folio
                    </button>
                </div>

            </form>

        </div>
    </x-slot>
</x-app-layout>