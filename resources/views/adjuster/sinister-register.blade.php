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
    ];
@endphp

<x-app-layout>
    <x-slot name="content">
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

        <div class="{{ $styles['page_container'] }}">

            <!-- Encabezado -->
            <header class="{{ $styles['header_section'] }}">
                <div>
                    <h1 class="{{ $styles['page_title'] }}">
                        <div class="w-12 h-12 bg-accent/10 rounded-2xl flex items-center justify-center text-accent shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        Registro de Siniestro
                    </h1>
                    <p class="{{ $styles['page_subtitle'] }}">Completa el formulario inicial anexando la evidencia correspondientes.</p>
                </div>
            </header>

            @if($errors->any())
                <div class="mb-6 px-6 py-4 bg-red-50 border border-red-200 text-red-700 rounded-3xl text-sm font-semibold shadow-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 px-6 py-4 bg-green-50 border border-green-200 text-green-700 rounded-3xl text-sm font-semibold shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <form id="sinisterRegisterForm" action="{{ route('sinisterStore') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Sección 1: Enlace a Póliza / Vehículo -->
                <div class="{{ $styles['section_card'] }}">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-accent/5 rounded-bl-[100px] -z-10"></div>
                    <h2 class="{{ $styles['section_title'] }}">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        1. Identificación de Póliza
                    </h2>

                    <div class="grid grid-cols-1 gap-6">
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="policy_id">Póliza del Asegurado</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-tertiary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <select id="policy_id" name="policy_id" class="{{ $styles['input'] }} pl-11 appearance-none cursor-pointer" required>
                                    <option value="" disabled selected>Selecciona una póliza registrada activa...</option>
                                    @foreach($policies as $policy)
                                        <option value="{{ $policy->id }}" {{ old('policy_id') == $policy->id ? 'selected' : '' }}>
                                            {{ $policy->folio }} - {{ $policy->vehicle->vehicleModel->brand }} {{ $policy->vehicle->vehicleModel->sub_brand }} ({{ $policy->insured->name }} {{ $policy->insured->father_lastname }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-tertiary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección 2: Detalles del Incidente -->
                <div class="{{ $styles['section_card'] }}">
                    <h2 class="{{ $styles['section_title'] }}">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                        2. Detalles del Incidente
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="occur_date">Fecha y Hora de Ocurrencia (Aproximada)</label>
                            <input type="datetime-local" id="occur_date" name="occur_date" class="{{ $styles['input'] }}" value="{{ old('occur_date') }}" required>
                        </div>
                        
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="location">Ubicación del Siniestro</label>
                            <input type="text" id="location" name="location" placeholder="Escribe la dirección o referencias..." class="{{ $styles['input'] }}" value="{{ old('location') }}" required>
                        </div>

                        <div class="{{ $styles['input_group'] }} md:col-span-2 mt-2">
                            <label class="{{ $styles['label'] }}" for="description">Descripción / Relato de los Hechos</label>
                            <textarea id="description" name="description" placeholder="Redacta cómo ocurrieron los hechos, partes involucradas, clima, etc..." class="{{ $styles['textarea'] }}" required>{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Sección 3: Evidencia Multimedia -->
                <div class="{{ $styles['section_card'] }}">
                    <div class="flex justify-between items-center border-b border-extra/30 pb-4 mb-6">
                        <h2 class="text-xl font-bold text-quaternary flex items-center gap-2">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            3. Evidencia y Fotografía Inicial
                        </h2>
                        <span class="text-xs font-bold text-tertiary bg-secondary/30 px-3 py-1.5 rounded-full"><span id="filesCount">0</span> / 10 Archivos</span>
                    </div>

                    <!-- Drag and Drop Base Area -->
                    <div id="dropzoneContainer" class="flex flex-col">
                        <div id="dropzone" class="{{ $styles['upload_area'] }}">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-quaternary mb-1">Cargar Fotografías y Videos</h3>
                            <p class="text-sm text-tertiary">Agrega tus archivos haciendo clic en esta área.</p>
                            <p class="text-xs font-mono text-tertiary mt-4">Nota: Archivos BLOB limitados a 5MB.</p>
                        </div>
                        
                        <!-- Contenedor dinámico de archivos seleccionados -->
                        <div id="fileListContainer" class="flex flex-col gap-4"></div>
                    </div>

                </div>

                <!-- Bottom Actions -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t border-extra/30 pt-6">
                    <a href="{{ route('dashboard') }}" class="{{ $styles['btn_secondary'] }} text-center">
                        Descartar Registro
                    </a>
                    <button type="submit" id="submitBtn" class="{{ $styles['btn_primary'] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Generar Siniestro y Folio
                    </button>
                </div>

            </form>

        </div>

    </x-slot>
</x-app-layout>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Inicializar TomSelect en el selector de póliza
        new TomSelect("#policy_id", {
            create: false,
            dropdownParent: 'body',
            sortField: { field: "text", direction: "asc" }
        });

        const MAX_FILES = 10;
        const BLOB_MAX_SIZE_BYTES = 5 * 1024 * 1024; // 5 MB

        let totalFiles = 0;
        const dropzone = document.getElementById('dropzone');
        const dropzoneContainer = document.getElementById('dropzoneContainer');
        const fileListContainer = document.getElementById('fileListContainer');
        const filesCountLabel = document.getElementById('filesCount');
        const submitBtn = document.getElementById('submitBtn');

        function updateDropzoneUI() {
            if (totalFiles > 0) {
                dropzone.className = "border-2 border-dashed border-extra/50 rounded-xl p-4 flex flex-row items-center justify-center text-center hover:border-accent hover:bg-secondary/10 transition-colors cursor-pointer group gap-4 mt-4";
                dropzone.innerHTML = `
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold text-quaternary mb-0">Agregar más archivos</h3>
                `;
                // Mover dropzone abajo de la lista
                dropzoneContainer.appendChild(dropzone);
            } else {
                dropzone.className = "border-2 border-dashed border-extra/50 rounded-2xl p-8 flex flex-col items-center justify-center text-center hover:border-accent hover:bg-secondary/10 transition-colors cursor-pointer group mb-4";
                dropzone.innerHTML = `
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-quaternary mb-1">Cargar Fotografías y Videos</h3>
                    <p class="text-sm text-tertiary">Agrega tus archivos haciendo clic en esta área.</p>
                    <p class="text-xs font-mono text-tertiary mt-4">Nota: Archivos BLOB limitados a 5MB.</p>
                `;
                // Mover dropzone arriba de la lista
                dropzoneContainer.insertBefore(dropzone, fileListContainer);
            }

            if (totalFiles >= MAX_FILES) {
                dropzone.style.display = 'none';
            } else {
                dropzone.style.display = 'flex';
            }
        }

        dropzone.addEventListener('click', function() {
            if (totalFiles >= MAX_FILES) {
                alert(`Haz alcanzado el límite máximo de ${MAX_FILES} archivos.`);
                return;
            }
            createFileInput();
        });

        // Función de compresión con la API Canvas en Vanilla JS
        async function compressImageIfNeeded(file) {
            if (!file.type.startsWith('image/')) return file;
            
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => {
                    URL.revokeObjectURL(img.src);
                    
                    const MAX_SIZE = 1080;
                    let w = img.width;
                    let h = img.height;
                    
                    // Solo escalar si excede el tamaño máximo
                    if (w > MAX_SIZE || h > MAX_SIZE) {
                        if (w > h) {
                            h = Math.round(h * (MAX_SIZE / w));
                            w = MAX_SIZE;
                        } else {
                            w = Math.round(w * (MAX_SIZE / h));
                            h = MAX_SIZE;
                        }
                    }
                    
                    const canvas = document.createElement('canvas');
                    canvas.width = w;
                    canvas.height = h;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, w, h);
                    
                    // Convertir a formato moderno WebP con 80% de calidad web
                    canvas.toBlob(blob => {
                        if(!blob) return resolve(file); // Fallback
                        // Recrear un objeto File simulado con el binario nuevo
                        const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".webp", {
                            type: 'image/webp',
                            lastModified: Date.now()
                        });
                        resolve(compressedFile);
                    }, 'image/webp', 0.80);
                };
                img.onerror = () => resolve(file);
                img.src = URL.createObjectURL(file);
            });
        }

        function createFileInput() {
            const input = document.createElement('input');
            input.type = 'file';
            input.multiple = true;
            input.className = 'hidden';
            input.accept = 'image/*,video/mp4';
            
            input.addEventListener('change', async function(e) {
                const selectedFiles = Array.from(e.target.files);
                if (selectedFiles.length === 0) return;

                if (totalFiles + selectedFiles.length > MAX_FILES) {
                    alert(`Estás intentando añadir demasiados archivos. El límite restante es ${MAX_FILES - totalFiles}.`);
                    return;
                }

                // Deshabilitar momentaneamente UI si está procesando muchos archivos (simulando Loader ligero)
                const originalHTML = dropzone.innerHTML;
                dropzone.innerHTML = "<h3 class='text-sm font-bold text-quaternary'>Procesando archivos...</h3>";
                dropzone.style.pointerEvents = 'none';

                for (let file of selectedFiles) {
                    // Esperar a comprimir cada una
                    const processedFile = await compressImageIfNeeded(file);
                    attachSingleFileBlock(processedFile);
                }

                // Restaurar UI y actualizar apariencia
                dropzone.style.pointerEvents = 'auto';
                updateDropzoneUI();
            });

            input.click();
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = 2;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        function attachSingleFileBlock(file) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);

            // Se añade clase 'file-block-item' para seleccionarlo después en la subida iterativa
            const fileBlock = document.createElement('div');
            fileBlock.className = 'file-block-item flex flex-col md:flex-row md:items-center justify-between p-4 bg-white border border-extra/30 rounded-2xl shadow-sm gap-4 relative overflow-hidden group';

            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.name = 'files[]';
            fileInput.className = 'hidden';
            fileInput.files = dataTransfer.files; 
            
            const infoDiv = document.createElement('div');
            infoDiv.className = 'flex items-center gap-4 flex-1 overflow-hidden';
            
            let iconHtml = `<div class="w-10 h-10 bg-secondary/50 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>`;

            if (file.type.startsWith('image/')) {
                const objUrl = URL.createObjectURL(file);
                iconHtml = `<img src="${objUrl}" class="w-10 h-10 object-cover rounded-xl shrink-0 border border-extra/30" onload="window.URL.revokeObjectURL(this.src)"/>`;
            } else if (file.type.startsWith('video/')) {
                const objUrl = URL.createObjectURL(file);
                iconHtml = `<video src="${objUrl}" class="w-10 h-10 object-cover rounded-xl shrink-0 border border-extra/30 bg-black" muted preload="metadata" onloadeddata="window.URL.revokeObjectURL(this.src)"></video>`;
            }

            const isSizeExceeded = file.size > BLOB_MAX_SIZE_BYTES;

            infoDiv.innerHTML = `
                ${iconHtml}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-quaternary truncate">${file.name}</p>
                    <p class="text-xs text-tertiary font-bold ${file.type.includes('webp') ? 'text-green-600' : ''}">${formatBytes(file.size)} ${file.type.includes('webp') ? '(Comprimida)' : ''}</p>
                </div>
            `;

            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'flex items-center gap-4 w-full md:w-auto shrink-0 bg-secondary/10 p-2 rounded-xl';

            const selectContainer = document.createElement('div');
            selectContainer.className = 'flex flex-col flex-1 md:flex-initial';
            
            const typeLabel = document.createElement('label');
            typeLabel.className = 'text-[10px] font-bold text-tertiary uppercase tracking-wide mb-1 px-1';
            typeLabel.innerText = 'Tipo Guardado:';

            const select = document.createElement('select');
            select.name = 'storage_types[]';
            select.className = 'w-full md:w-40 px-3 py-2 bg-white rounded-lg border border-extra/30 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent text-sm font-semibold text-quaternary';
            select.innerHTML = `<option value="url">Vía URL (Físico)</option>`;

            const blobOption = document.createElement('option');
            blobOption.value = 'blob';
            blobOption.text = 'Base de Datos (BLOB)';
            
            if (isSizeExceeded) {
                blobOption.disabled = true;
                blobOption.text = 'BLOB (Muy pesado >5MB)';
            }
            
            select.appendChild(blobOption);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'w-10 h-10 flex items-center justify-center bg-white hover:bg-red-50 text-tertiary hover:text-red-500 rounded-lg border border-extra/30 transition-colors shrink-0';
            removeBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>`;
            
            removeBtn.addEventListener('click', function() {
                fileBlock.remove();
                totalFiles--;
                filesCountLabel.innerText = totalFiles;
                updateDropzoneUI();
            });

            selectContainer.appendChild(typeLabel);
            selectContainer.appendChild(select);
            actionsDiv.appendChild(selectContainer);
            actionsDiv.appendChild(removeBtn);

            fileBlock.appendChild(fileInput);
            fileBlock.appendChild(infoDiv);
            fileBlock.appendChild(actionsDiv);
            fileListContainer.appendChild(fileBlock);

            totalFiles++;
            filesCountLabel.innerText = totalFiles;
            // updateDropzoneUI() se llama después del bucle en el change event, pero 
            // si se añade un archivo singularmente, también deberíamos llamarlo aquí (opcional)
        }

        // --- SUBIDA ASÍNCRONA (AJAX CHUNKED) ---
        const form = document.getElementById('sinisterRegisterForm');
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Generando Base...
            `;
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                // Obtener datos iniciales sin los archivos
                const baseFormData = new FormData(form);
                baseFormData.delete('files[]');
                baseFormData.delete('storage_types[]');

                // PASO 1: Subir registro base
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: baseFormData
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Error del servidor al registrar el siniestro');
                }

                const sinisterId = data.sinister_id;
                const fileBlocks = document.querySelectorAll('.file-block-item');
                
                // PASO 2: Iterar para subir cada archivo individualmente (Cola de Trabajos)
                let count = 0;
                for (let block of fileBlocks) {
                    count++;
                    const innerFileInput = block.querySelector('input[type="file"]');
                    const innerSelect = block.querySelector('select[name="storage_types[]"]');

                    if(innerFileInput.files.length > 0) {
                        const fileParams = innerFileInput.files[0];
                        const CHUNK_SIZE = 1 * 1024 * 1024; // 1MB para máxima compatibilidad con servidores restringidos a 2MB
                        const totalChunks = Math.ceil(fileParams.size / CHUNK_SIZE);
                        const fileName = fileParams.name;
                        
                        // Si es archivo muy pesado cortarlo
                        for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
                            submitBtn.innerHTML = `
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Subiendo arc ${count} de ${fileBlocks.length}... (p. ${chunkIndex+1}/${totalChunks})
                            `;

                            const start = chunkIndex * CHUNK_SIZE;
                            const end = Math.min(start + CHUNK_SIZE, fileParams.size);
                            const chunk = fileParams.slice(start, end);

                            const chunkForm = new FormData();
                            chunkForm.append('_token', baseFormData.get('_token'));
                            chunkForm.append('sinister_id', sinisterId);
                            chunkForm.append('storage_type', innerSelect.value);
                            chunkForm.append('chunk', chunk);
                            chunkForm.append('chunk_index', chunkIndex);
                            chunkForm.append('total_chunks', totalChunks);
                            chunkForm.append('file_name', fileName);

                            const chunkRes = await fetch('{{ route("sinister.uploadChunk") }}', {
                                method: 'POST',
                                headers: { 'Accept': 'application/json' },
                                body: chunkForm
                            });

                            const resData = await chunkRes.json().catch(() => null);

                            if(!chunkRes.ok) {
                                const errMessage = resData?.message || `Error del servidor (HTTP ${chunkRes.status})`;
                                console.error(`Falló chunk ${chunkIndex} de ${fileName}:`, errMessage);
                                alert(`Atención: El archivo "${fileName}" se interrumpió en la subida. Razón: ${errMessage}`);
                                throw new Error(`Interrupción en archivo ${fileName}.`);
                            }
                        }
                    }
                }

                submitBtn.innerHTML = '¡Proceso Terminado!';
                alert('Siniestro y Evidencias cargados de forma óptima.');
                window.location.reload(); // Por defecto recargamos o podríamos hacer href= ruta del dashboard

            } catch (err) {
                alert(`Error general: ${err.message}`);
                submitBtn.innerHTML = 'Reintentar Envío';
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });

    });
</script>