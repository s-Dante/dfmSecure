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

        // Status Large Dropdown
        'status_container' => 'bg-quaternary rounded-2xl p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden shadow-lg',

        // Inputs
        'input_group' => 'flex flex-col gap-1.5',
        'label' => 'text-sm font-semibold text-tertiary',
        'input' => 'w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary',
        'input_readonly' => 'w-full px-4 py-3 bg-secondary/30 text-tertiary font-medium rounded-xl border border-transparent cursor-not-allowed',
        'textarea' => 'w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary resize-y min-h-[120px]',

        // Buttons
        'btn_primary' => 'bg-accent hover:bg-black text-white px-8 py-3 rounded-xl font-bold transition-colors inline-flex items-center gap-2 shadow-sm',
        'btn_secondary' => 'bg-white hover:bg-secondary/20 text-quaternary px-6 py-3 rounded-xl font-semibold transition-colors border border-extra/50',

        // Gallery 
        'gallery_grid' => 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4',
        'gallery_item' => 'aspect-square rounded-2xl bg-secondary/20 border border-extra/30 relative group overflow-hidden',
        'gallery_img' => 'w-full h-full object-cover',
        'gallery_delete' => 'absolute top-2 right-2 p-1.5 bg-red-600/90 text-white rounded-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700 shadow-md',
    ];

    // Variables derivadas
    $statusLabel = $sinister->status instanceof \App\Enums\SinisterStatusEnum 
        ? $sinister->status->label() 
        : ucfirst(str_replace('_', ' ', $sinister->status->value ?? $sinister->status));

    $vm = $sinister->policy->vehicle->vehicleModel;
    $vehicleName = trim(($vm->brand ?? '') . ' ' . ($vm->sub_brand ?? '') . ' (' . ($vm->year ?? '') . ')');
    $insuredName = trim(($sinister->policy->insured->name ?? '') . ' ' . ($sinister->policy->insured->father_lastname ?? ''));

    $evidence = $sinister->multimedia;
    $mediaSrc = function(\App\Models\SinisterMultimedia $media): ?string {
        if (!empty($media->blob_file)) {
            return route('media.sinister', $media->id);
        }
        if (!empty($media->path_file)) {
            return str_starts_with($media->path_file, 'http')
                ? $media->path_file
                : asset('storage/' . $media->path_file);
        }
        return null;
    };
@endphp

<x-app-layout>
    <x-slot name="content">
        <div class="{{ $styles['page_container'] }}" x-data="{ status: '{{ $statusLabel }}' }">

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
                        <h1 class="{{ $styles['page_title'] }}">Gestión de Siniestro</h1>
                    </div>
                    <p class="{{ $styles['page_subtitle'] }}">Editando folio <strong
                            class="font-mono text-quaternary">{{ $sinister->folio }}</strong> • Ajustador Responsable
                    </p>
                </div>
            </header>

            <form id="sinisterEditForm" action="{{ route('sinisterUpdate', $sinister->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Info Vinculada (Solo Lectura) -->
                <div class="{{ $styles['section_card'] }}">
                    <div class="flex justify-between items-start mb-6 border-b border-extra/30 pb-4">
                        <h2 class="text-xl font-bold text-quaternary">Contexto de la Póliza</h2>

                        <!-- Status Readonly Badge -->
                        <div
                            class="bg-secondary/10 px-4 py-2 rounded-xl flex items-center gap-2 border border-extra/50 shadow-sm">
                            <span class="text-xs font-bold text-tertiary uppercase tracking-wider">Estatus
                                Actual:</span>
                            <span class="text-sm font-extrabold text-accent">{{ $statusLabel }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}">Póliza Involucrada</label>
                            <input type="text" value="{{ $sinister->policy->folio }}" class="{{ $styles['input_readonly'] }}"
                                readonly disabled>
                        </div>
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}">Vehículo</label>
                            <input type="text" value="{{ $vehicleName }}"
                                class="{{ $styles['input_readonly'] }}" readonly disabled>
                        </div>
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}">Asegurado</label>
                            <input type="text" value="{{ $insuredName }}"
                                class="{{ $styles['input_readonly'] }}" readonly disabled>
                        </div>
                    </div>
                </div>

                <!-- Detalles y Fechas -->
                <div class="{{ $styles['section_card'] }}">
                    <h2 class="{{ $styles['section_title'] }}">Detalles Técnicos y Relato</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="occur_date">Fecha de Ocurrencia</label>
                            <input type="datetime-local" id="occur_date" name="occur_date"
                                class="{{ $styles['input'] }}" value="{{ $sinister->occur_date ? $sinister->occur_date->format('Y-m-d\TH:i') : '' }}" required>
                        </div>

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="report_date">Fecha de Reporte</label>
                            <input type="datetime-local" id="report_date" name="report_date"
                                class="{{ $styles['input'] }}" value="{{ $sinister->report_date ? $sinister->report_date->format('Y-m-d\TH:i') : '' }}" required>
                        </div>

                        <!-- Fecha de Cierre (Dinámica/Readonly) -->
                        <div class="{{ $styles['input_group'] }}" x-show="status === 'Cerrado'" x-cloak>
                            <label class="{{ $styles['label'] }} text-tertiary flex items-center gap-1"
                                for="close_date">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                                Fecha de Cierre Oficial
                            </label>
                            <input type="datetime-local" id="close_date" class="{{ $styles['input_readonly'] }}"
                                value="{{ $sinister->close_date ? $sinister->close_date->format('Y-m-d\TH:i') : '' }}" readonly disabled>
                        </div>
                        <div class="lg:col-span-1" x-show="status !== 'Cerrado'"></div> <!-- Spacer -->

                        <div class="{{ $styles['input_group'] }} md:col-span-2 lg:col-span-3">
                            <label class="{{ $styles['label'] }}" for="location">Ubicación del Siniestro</label>
                            <input type="text" id="location" name="location" value="{{ $sinister->location }}"
                                class="{{ $styles['input'] }}" required>
                        </div>

                        <div class="{{ $styles['input_group'] }} md:col-span-2 lg:col-span-3 mt-2">
                            <label class="{{ $styles['label'] }}" for="description">Relato / Dictamen del
                                Ajustador</label>
                            <textarea id="description" name="description" class="{{ $styles['textarea'] }} text-sm"
                                required>{{ $sinister->description }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Evidencia Visual (Gestión) -->
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
                        <button type="button" class="{{ $styles['btn_secondary'] }} !py-2 !px-4 !text-sm mt-3 sm:mt-0"
                            onclick="document.getElementById('add_photos').click()">
                            Añadir Fotografía Adicional
                        </button>
                        <input type="file" id="add_photos" name="add_files[]" multiple class="hidden"
                            accept="image/*,video/mp4">
                    </div>
                    
                    <!-- Contenedor dinámico de archivos nuevos a subir -->
                    <div id="fileListContainer" class="flex flex-col gap-4 mb-6"></div>

                    <div class="{{ $styles['gallery_grid'] }}">
                        <!-- Iterate stored evidence -->
                        @foreach($evidence as $media)
                            @php
                                $resolved_url = $mediaSrc($media);
                                if(!$resolved_url) continue;
                                $isVideo = $media->type === \App\Enums\SinisterMultimediaTypeEnum::VIDEO;
                            @endphp
                            <div class="{{ $styles['gallery_item'] }}">
                                @if($isVideo)
                                    <video src="{{ $resolved_url }}" controls preload="metadata" class="w-full h-full object-contain bg-black"></video>
                                @else
                                    <img src="{{ $resolved_url }}" class="{{ $styles['gallery_img'] }}" alt="Evidencia">
                                @endif
                                
                                <!-- Helper logic for future delete action -->
                                <button type="button" class="{{ $styles['gallery_delete'] }}" title="Eliminar Archivo" onclick="deleteMedia({{ $media->id }})">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Botones Finales -->
                <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-extra/30">
                    <button type="submit" id="submitBtn" class="{{ $styles['btn_primary'] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Guardar Cambios y Subir
                    </button>
                </div>
            </form>

        </div>
    </x-slot>

    @push('scripts')
    <script>
        const BLOB_MAX_SIZE_BYTES = 5 * 1024 * 1024; // 5 MB
        const fileListContainer = document.getElementById('fileListContainer');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('sinisterEditForm');

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

            const fileBlock = document.createElement('div');
            fileBlock.className = 'file-block-item flex flex-col md:flex-row md:items-center justify-between p-4 bg-secondary/10 border border-extra/30 rounded-2xl shadow-sm gap-4 relative overflow-hidden group';

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
                    <p class="text-sm font-bold text-quaternary truncate text-ellipsis overflow-hidden whitespace-nowrap" style="max-width: 150px;">${file.name}</p>
                    <p class="text-xs text-tertiary font-bold ${file.type.includes('webp') ? 'text-green-600' : ''}">${formatBytes(file.size)} ${file.type.includes('webp') ? '(Comprimida)' : ''}</p>
                </div>
            `;

            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'flex items-center gap-4 w-full md:w-auto shrink-0 bg-white p-2 rounded-xl border border-extra/30';

            const selectContainer = document.createElement('div');
            selectContainer.className = 'flex flex-col flex-1 md:flex-initial';
            
            const typeLabel = document.createElement('label');
            typeLabel.className = 'text-[10px] font-bold text-tertiary uppercase tracking-wide mb-1 px-1';
            typeLabel.innerText = 'Guardar como:';

            const select = document.createElement('select');
            select.name = 'storage_types[]';
            select.className = 'w-full md:w-32 px-2 py-1 bg-secondary/10 rounded-lg border border-transparent focus:outline-none focus:border-accent text-xs font-semibold text-quaternary';
            select.innerHTML = `<option value="url">Ruta Física</option>`;

            const blobOption = document.createElement('option');
            blobOption.value = 'blob';
            blobOption.text = 'Base de Datos';
            
            if (isSizeExceeded) {
                blobOption.disabled = true;
                blobOption.text = 'BLOB (>5MB)';
            }
            
            select.appendChild(blobOption);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'w-8 h-8 flex items-center justify-center bg-white hover:bg-red-50 text-tertiary hover:text-red-500 rounded-lg border border-extra/30 transition-colors shrink-0';
            removeBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>`;
            
            removeBtn.addEventListener('click', function() {
                fileBlock.remove();
            });

            selectContainer.appendChild(typeLabel);
            selectContainer.appendChild(select);
            actionsDiv.appendChild(selectContainer);
            actionsDiv.appendChild(removeBtn);

            fileBlock.appendChild(fileInput);
            fileBlock.appendChild(infoDiv);
            fileBlock.appendChild(actionsDiv);
            fileListContainer.appendChild(fileBlock);
        }

        document.getElementById('add_photos').addEventListener('change', async function(e) {
            const selectedFiles = Array.from(e.target.files);
            if (selectedFiles.length === 0) return;

            const currentBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Procesando...';
            submitBtn.disabled = true;

            for (let file of selectedFiles) {
                const processedFile = await compressImageIfNeeded(file);
                attachSingleFileBlock(processedFile);
            }

            submitBtn.innerHTML = currentBtnHtml;
            submitBtn.disabled = false;
        });

        // Interceptar el submit para subir archivos nuevos antes de actualizar los datos
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const fileBlocks = document.querySelectorAll('.file-block-item');
            if (fileBlocks.length > 0) {
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Subiendo Archivos Nuevos...
                `;
                submitBtn.disabled = true;

                const sinisterId = {{ $sinister->id }};
                
                let count = 0;
                for (let block of fileBlocks) {
                    count++;
                    const innerFileInput = block.querySelector('input[type="file"]');
                    const innerSelect = block.querySelector('select[name="storage_types[]"]');

                    if(innerFileInput.files.length > 0) {
                        const fileParams = innerFileInput.files[0];
                        const CHUNK_SIZE = 1 * 1024 * 1024;
                        const totalChunks = Math.ceil(fileParams.size / CHUNK_SIZE);
                        const fileName = fileParams.name;
                        
                        for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
                            const start = chunkIndex * CHUNK_SIZE;
                            const end = Math.min(start + CHUNK_SIZE, fileParams.size);
                            const chunk = fileParams.slice(start, end);

                            const chunkForm = new FormData();
                            chunkForm.append('_token', '{{ csrf_token() }}');
                            chunkForm.append('sinister_id', sinisterId);
                            chunkForm.append('storage_type', innerSelect.value);
                            chunkForm.append('chunk', chunk);
                            chunkForm.append('chunk_index', chunkIndex);
                            chunkForm.append('total_chunks', totalChunks);
                            chunkForm.append('file_name', fileName);

                            try {
                                const chunkRes = await fetch('{{ route("sinister.uploadChunk") }}', {
                                    method: 'POST',
                                    headers: { 'Accept': 'application/json' },
                                    body: chunkForm
                                });

                                if(!chunkRes.ok) {
                                    throw new Error(`Error en el servidor al subir el archivo ${fileName}`);
                                }
                            } catch (err) {
                                alert(`Error al subir evidencia: ${err.message}`);
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = 'Guardar Cambios y Subir';
                                return;
                            }
                        }
                    }
                }
            }

            // Continuar con el envío del formulario normal
            form.submit();
        });

        async function deleteMedia(mediaId) {
            if(!confirm('¿Estás seguro de que deseas eliminar este archivo de la evidencia?')) return;

            const response = await fetch(`/sinister-media/${mediaId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                window.location.reload();
            } else {
                alert('Hubo un error al eliminar el archivo.');
            }
        }
    </script>
    @endpush
</x-app-layout>