@php
$styles = [
'page_container' => 'w-full max-w-7xl mx-auto space-y-8 pb-12',
'header_section' => 'flex flex-col md:flex-row justify-between items-start md:items-center py-4 gap-4',
'page_title' => 'text-3xl font-extrabold text-quaternary',
'page_subtitle' => 'text-tertiary mt-1',

// Buttons
'btn_primary' => 'bg-accent hover:bg-black text-white px-6 py-3 rounded-xl font-bold transition-colors inline-flex items-center gap-2 shadow-sm',
'btn_secondary' => 'bg-secondary/30 hover:bg-accent text-quaternary hover:text-white px-4 py-2 rounded-xl font-semibold transition-colors border border-extra/50 hover:border-accent inline-flex items-center gap-2 text-sm',

// Cards Grid
'grid_container' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6',

// Vehicle Card
'card' => 'bg-white rounded-3xl p-6 shadow-sm border border-extra/30 hover:shadow-md transition-shadow relative overflow-hidden group flex flex-col',
'card_header' => 'flex justify-between items-start mb-4 gap-4 border-b border-extra/30 pb-4',
'car_title' => 'text-xl font-bold text-quaternary leading-tight',
'car_brand' => 'text-sm font-semibold text-tertiary uppercase tracking-wider',
'car_badge' => 'px-3 py-1 bg-secondary/50 text-quaternary rounded-full text-xs font-bold border border-extra/50',

// Card Body Grid
'info_grid' => 'grid grid-cols-2 gap-y-4 gap-x-2 mt-2 mb-6',
'info_item' => 'flex flex-col',
'info_label' => 'text-xs font-semibold text-tertiary uppercase tracking-wider mb-0.5',
'info_value' => 'text-base font-medium text-quaternary',

// Card Footer
'card_footer' => 'mt-auto pt-4 border-t border-extra/30 flex justify-between items-center',

// Add Form Section (Hidden by default using Alpine.js or shown toggle)
'form_section' => 'bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-extra/30 mt-6 relative overflow-hidden',
'form_title' => 'text-xl font-bold text-quaternary mb-6 flex items-center gap-2',
'input_group' => 'flex flex-col gap-1.5',
'label' => 'text-sm font-semibold text-tertiary',
'input' => 'w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary',
];

// Colors can be dynamic but for now we let the user write it, or provide a select.
@endphp

<x-app-layout>
    <x-slot name="content">
        <!-- Main container -->
        <div class="{{ $styles['page_container'] }}">

            <!-- Encabezado -->
            <header class="{{ $styles['header_section'] }}">
                <div>
                    <h1 class="{{ $styles['page_title'] }}">Mis Vehículos</h1>
                    <p class="{{ $styles['page_subtitle'] }}">Gestiona la información de tus vehículos registrados.</p>
                </div>

                <button onclick="toggleForm()" id="toggleFormBtn" class="{{ $styles['btn_primary'] }}">
                    <span id="btnTextAdd" class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Añadir Vehículo
                    </span>
                    <span id="btnTextCancel" class="flex items-center gap-2" style="display: none;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar
                    </span>
                </button>
            </header>

            <!-- Formulario para Añadir Vehículo (Oculto por defecto) -->
            <div id="addVehicleForm" style="display: none;" class="{{ $styles['form_section'] }}">

                <div class="absolute top-0 right-0 w-32 h-32 bg-accent/5 rounded-bl-[100px] -z-10"></div>

                <h2 class="{{ $styles['form_title'] }}">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                    </svg>
                    Registrar Nuevo Vehículo
                </h2>

                @if($errors->any())
                <div class="mb-4 px-5 py-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-semibold">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('myVehicles.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <!-- Grid de inputs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="type">Tipo de Vehículo</label>
                            <select id="type" name="type" class="w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary" required>
                                <option value="">— Seleccionar —</option>
                            </select>
                        </div>

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="year">Año</label>
                            <select id="year" name="year" class="w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary disabled:opacity-50" disabled required>
                                <option value="">— Seleccionar —</option>
                            </select>
                        </div>

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="brand">Marca</label>
                            <select id="brand" name="brand" class="w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary disabled:opacity-50" disabled required>
                                <option value="">— Seleccionar —</option>
                            </select>
                        </div>

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="sub_brand">Sub-Marca / Modelo</label>
                            <select id="sub_brand" name="sub_brand" class="w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary disabled:opacity-50" disabled required>
                                <option value="">— Seleccionar —</option>
                            </select>
                        </div>

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="version">Versión</label>
                            <select id="version" name="version" class="w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary disabled:opacity-50" disabled required>
                                <option value="">— Seleccionar —</option>
                            </select>
                        </div>

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="color">Color</label>
                            <input type="text" id="color" name="color" class="w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary" placeholder="Ej. Gris Carbono" required>
                        </div>

                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="plate">Placas</label>
                            <input type="text" id="plate" name="plate" class="w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary uppercase" placeholder="Ej. ASD-987-X" required>
                        </div>

                        <div class="{{ $styles['input_group'] }} md:col-span-2 lg:col-span-2">
                            <label class="{{ $styles['label'] }}" for="vin">Número de Serie (VIN) <span class="text-tertiary font-normal text-xs ml-2">(Caracteres únicos)</span></label>
                            <input type="text" id="vin" name="vin" class="w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary uppercase" placeholder="Ej. 1FA6P8CF4M5123456" maxlength="17" required>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-extra/30">
                        <button type="submit" class="{{ $styles['btn_primary'] }}">
                            Guardar Vehículo
                        </button>
                    </div>
                </form>
            </div>

            <!-- Listado de Vehículos -->
            <div class="{{ $styles['grid_container'] }}">
                @foreach($vehicles as $vehicle)
                <article class="{{ $styles['card'] }}">

                    <div class="{{ $styles['card_header'] }}">
                        <div>
                            <h3 class="{{ $styles['car_brand'] }}">{{ $vehicle->vehicleModel->brand }}</h3>
                            <h2 class="{{ $styles['car_title'] }}">{{ $vehicle->vehicleModel->sub_brand }} {{ $vehicle->vehicleModel->version }}</h2>
                        </div>
                        <span class="{{ $styles['car_badge'] }}">{{ $vehicle->vehicleModel->year }}</span>
                    </div>

                    <div class="{{ $styles['info_grid'] }}">
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Placas</span>
                            <span class="{{ $styles['info_value'] }}">{{ $vehicle->plate }}</span>
                        </div>
                        <div class="{{ $styles['info_item'] }}">
                            <span class="{{ $styles['info_label'] }}">Color</span>
                            <span class="{{ $styles['info_value'] }}">{{ $vehicle->vehicleModel->color }}</span>
                        </div>
                        <div class="{{ $styles['info_item'] }} col-span-2">
                            <span class="{{ $styles['info_label'] }}">Número de Serie (VIN)</span>
                            <span class="{{ $styles['info_value'] }} font-mono tracking-wider">{{ $vehicle->vin }}</span>
                        </div>
                    </div>

                    <!-- Status Policy & Footer -->
                    <div class="{{ $styles['card_footer'] }}">
                        @if($vehicle->policy()->active()->exists())
                        <span class="inline-flex items-center gap-1.5 text-green-600 bg-green-50 px-3 py-1 rounded-full text-xs font-bold border border-green-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Asegurado
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 text-yellow-600 bg-yellow-50 px-3 py-1 rounded-full text-xs font-bold border border-yellow-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            Sin Póliza
                        </span>
                        @endif

                        <div class="flex items-center gap-2">
                            <a href="{{ route('myVehicles.edit', $vehicle->id) }}" class="{{ $styles['btn_secondary'] }} !px-3 !py-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                                Editar
                            </a>
                            @if(!$vehicle->policy()->active()->exists())
                            <form action="{{ route('myVehicles.destroy', $vehicle->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este vehículo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-50 hover:bg-red-500 text-red-500 hover:text-white px-3 py-1.5 rounded-xl font-semibold transition-colors border border-red-200 hover:border-red-500 inline-flex items-center gap-2 text-sm">
                                    Eliminar
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <!-- Empty State (Solo para demostrar si no hubiera vehículos) -->
            @if(count($vehicles) === 0)
            <div class="bg-white/50 backdrop-blur-sm rounded-3xl p-12 text-center border-2 border-dashed border-extra/50 flex flex-col items-center gap-4">
                <div class="w-20 h-20 bg-secondary rounded-full flex items-center justify-center mb-2">
                    <svg class="w-10 h-10 text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-quaternary mb-2">No tienes vehículos registrados</h3>
                    <p class="text-tertiary max-w-md mx-auto">Regístra tu primer vehículo para poder adquirir una póliza y proteger tu patrimonio.</p>
                </div>
                <button onclick="toggleForm()" class="{{ $styles['btn_primary'] }} mt-4">
                    Empezar
                </button>
            </div>
            @endif

        </div>

        <script>
            const vehiclesData = @json($vehiclesJSON);

            // Toggle del formulario
            function toggleForm() {
                const form = document.getElementById('addVehicleForm');
                const btnAdd = document.getElementById('btnTextAdd');
                const btnCancel = document.getElementById('btnTextCancel');

                if (form.style.display === 'none') {
                    form.style.display = 'block';
                    btnAdd.style.display = 'none';
                    btnCancel.style.display = 'flex';
                } else {
                    form.style.display = 'none';
                    btnAdd.style.display = 'flex';
                    btnCancel.style.display = 'none';
                }
            }

            // Poblar tipos de vehículo al cargar
            document.addEventListener('DOMContentLoaded', function() {
                const typeSelect = document.getElementById('type');
                const yearSelect = document.getElementById('year');
                const brandSelect = document.getElementById('brand');
                const subBrandSelect = document.getElementById('sub_brand');
                const versionSelect = document.getElementById('version');

                // Llenar tipos
                Object.keys(vehiclesData).forEach(type => {
                    const option = document.createElement('option');
                    option.value = type;
                    option.textContent = type;
                    typeSelect.appendChild(option);
                });

                // Evento: al cambiar tipo
                typeSelect.addEventListener('change', function() {
                    yearSelect.innerHTML = '<option value="">— Seleccionar —</option>';
                    brandSelect.innerHTML = '<option value="">— Seleccionar —</option>';
                    subBrandSelect.innerHTML = '<option value="">— Seleccionar —</option>';
                    versionSelect.innerHTML = '<option value="">— Seleccionar —</option>';

                    brandSelect.disabled = true;
                    subBrandSelect.disabled = true;
                    versionSelect.disabled = true;

                    if (this.value) {
                        yearSelect.disabled = false;
                        Object.keys(vehiclesData[this.value]).forEach(year => {
                            const option = document.createElement('option');
                            option.value = year;
                            option.textContent = year;
                            yearSelect.appendChild(option);
                        });
                    } else {
                        yearSelect.disabled = true;
                    }
                });

                // Evento: al cambiar año
                yearSelect.addEventListener('change', function() {
                    brandSelect.innerHTML = '<option value="">— Seleccionar —</option>';
                    subBrandSelect.innerHTML = '<option value="">— Seleccionar —</option>';
                    versionSelect.innerHTML = '<option value="">— Seleccionar —</option>';

                    subBrandSelect.disabled = true;
                    versionSelect.disabled = true;

                    const selectedType = typeSelect.value;
                    if (this.value && selectedType) {
                        brandSelect.disabled = false;
                        Object.keys(vehiclesData[selectedType][this.value]).forEach(brand => {
                            const option = document.createElement('option');
                            option.value = brand;
                            option.textContent = brand;
                            brandSelect.appendChild(option);
                        });
                    } else {
                        brandSelect.disabled = true;
                    }
                });

                // Evento: al cambiar marca
                brandSelect.addEventListener('change', function() {
                    subBrandSelect.innerHTML = '<option value="">— Seleccionar —</option>';
                    versionSelect.innerHTML = '<option value="">— Seleccionar —</option>';

                    versionSelect.disabled = true;

                    const selectedType = typeSelect.value;
                    const selectedYear = yearSelect.value;
                    if (this.value && selectedType && selectedYear) {
                        subBrandSelect.disabled = false;
                        Object.keys(vehiclesData[selectedType][selectedYear][this.value]).forEach(model => {
                            const option = document.createElement('option');
                            option.value = model;
                            option.textContent = model;
                            subBrandSelect.appendChild(option);
                        });
                    } else {
                        subBrandSelect.disabled = true;
                    }
                });

                // Evento: al cambiar sub-marca/modelo
                subBrandSelect.addEventListener('change', function() {
                    versionSelect.innerHTML = '<option value="">— Seleccionar —</option>';

                    const selectedType = typeSelect.value;
                    const selectedYear = yearSelect.value;
                    const selectedBrand = brandSelect.value;
                    if (this.value && selectedType && selectedYear && selectedBrand) {
                        versionSelect.disabled = false;
                        const versions = vehiclesData[selectedType][selectedYear][selectedBrand][this.value];
                        versions.forEach(version => {
                            const option = document.createElement('option');
                            option.value = version;
                            option.textContent = version;
                            versionSelect.appendChild(option);
                        });
                    } else {
                        versionSelect.disabled = true;
                    }
                });
            });
        </script>
    </x-slot>
</x-app-layout>