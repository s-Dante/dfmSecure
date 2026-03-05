@php
    $styles = [
        'page_container' => 'w-full max-w-4xl mx-auto space-y-8 pb-12',

        // Header
        'header_section' => 'flex flex-col md:flex-row justify-between items-start md:items-center py-4 gap-4',
        'page_title' => 'text-3xl font-extrabold text-quaternary',
        'page_subtitle' => 'text-tertiary mt-1',

        // Form Section (Card)
        'form_card' => 'bg-white p-6 md:p-10 rounded-3xl shadow-sm border border-extra/30 relative overflow-hidden',
        'form_title' => 'text-xl font-bold text-quaternary mb-8 flex items-center gap-2 border-b border-extra/30 pb-4',

        // Inputs
        'input_group' => 'flex flex-col gap-1.5',
        'label' => 'text-sm font-semibold text-tertiary',
        'input' => 'w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary',
        'input_readonly' => 'w-full px-4 py-3 bg-secondary/30 rounded-xl border border-transparent text-tertiary cursor-not-allowed font-mono tracking-wider',

        // Buttons
        'btn_primary' => 'bg-accent hover:bg-black text-white px-8 py-3 rounded-xl font-bold transition-colors inline-flex items-center gap-2 shadow-sm',
        'btn_secondary' => 'bg-white hover:bg-secondary/20 text-quaternary px-6 py-3 rounded-xl font-semibold transition-colors border border-extra/50',
        'btn_danger' => 'bg-white hover:bg-red-50 text-red-600 px-6 py-3 rounded-xl font-semibold transition-colors border border-red-200 mt-8 sm:mt-0',
    ];

    // Dummy Vehicle to Edit (Simulating retrieval from DB)
    $vehicle = [
        'id' => 1,
        'brand' => 'Ford',
        'sub_brand' => 'Mustang',
        'version' => 'GT V8',
        'year' => '2024',
        'color' => 'Gris Carbono',
        'vin' => '1FA6P8CF4M5123456',
        'plate' => 'ASD-987-X',
        'has_policy' => true
    ];
@endphp

<x-app-layout>
    <x-slot name="content">
        <div class="{{ $styles['page_container'] }}">

            <!-- Encabezado con Botón de Regreso -->
            <header class="{{ $styles['header_section'] }}">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('myVehicle') }}"
                            class="w-10 h-10 rounded-xl bg-white border border-extra/50 flex items-center justify-center text-tertiary hover:text-accent hover:border-accent transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <h1 class="{{ $styles['page_title'] }}">Editar Vehículo</h1>
                    </div>
                    <p class="{{ $styles['page_subtitle'] }}">Actualiza la información de tu {{ $vehicle['brand'] }}
                        {{ $vehicle['sub_brand'] }}</p>
                </div>
            </header>

            <!-- Formulario Principal -->
            <div class="{{ $styles['form_card'] }}">
                <div class="absolute top-0 right-0 w-48 h-48 bg-accent/5 rounded-bl-[150px] -z-10"></div>

                <!-- Notice if vehicle has policy -->
                @if($vehicle['has_policy'])
                    <div class="mb-8 p-4 bg-yellow-50 border border-yellow-200 rounded-2xl flex gap-3 text-yellow-700">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm font-medium">Este vehículo está ligado a una póliza activa. Cierta información
                            como el <strong>Número de Serie (VIN)</strong> no puede ser modificada directamente. Si
                            necesitas actualizarla de urgencia, por favor contacta a soporte.</p>
                    </div>
                @endif

                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <!-- Método Spoofing para futuras integraciones PUT/PATCH -->
                    @method('PUT')

                    <h2 class="{{ $styles['form_title'] }}">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Datos Generales
                    </h2>

                    <!-- Grid de inputs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">

                        <!-- Marca -->
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="brand">Marca</label>
                            <input type="text" id="brand" name="brand" value="{{ $vehicle['brand'] }}"
                                class="{{ $styles['input'] }}" required>
                        </div>

                        <!-- Submarca / Modelo -->
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="sub_brand">Sub-Marca / Modelo</label>
                            <input type="text" id="sub_brand" name="sub_brand" value="{{ $vehicle['sub_brand'] }}"
                                class="{{ $styles['input'] }}" required>
                        </div>

                        <!-- Versión -->
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="version">Versión</label>
                            <input type="text" id="version" name="version" value="{{ $vehicle['version'] }}"
                                class="{{ $styles['input'] }}" required>
                        </div>

                        <!-- Año -->
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="year">Año</label>
                            <input type="number" id="year" name="year" value="{{ $vehicle['year'] }}"
                                class="{{ $styles['input'] }}" required>
                        </div>

                        <!-- Color -->
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="color">Color</label>
                            <input type="text" id="color" name="color" value="{{ $vehicle['color'] }}"
                                class="{{ $styles['input'] }}" required>
                        </div>

                        <!-- Placas -->
                        <div class="{{ $styles['input_group'] }}">
                            <label class="{{ $styles['label'] }}" for="plate">Placas</label>
                            <input type="text" id="plate" name="plate" value="{{ $vehicle['plate'] }}"
                                class="{{ $styles['input'] }}" required>
                        </div>

                        <!-- VIN (Readonly o Deshabilitado if has Policy) -->
                        <div class="{{ $styles['input_group'] }} md:col-span-2 mt-4 pt-4 border-t border-extra/30">
                            <label class="{{ $styles['label'] }}" for="vin">Número de Serie (VIN)</label>
                            @if($vehicle['has_policy'])
                                <input type="text" id="vin" name="vin" value="{{ $vehicle['vin'] }}"
                                    class="{{ $styles['input_readonly'] }}" readonly
                                    title="No modificable por política activa">
                            @else
                                <input type="text" id="vin" name="vin" value="{{ $vehicle['vin'] }}"
                                    class="{{ $styles['input'] }} uppercase" maxlength="17" required>
                            @endif
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div
                        class="flex flex-col sm:flex-row justify-between items-center pt-8 mt-8 border-t border-extra/30">
                        <button type="button" class="{{ $styles['btn_danger'] }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            Eliminar Vehículo
                        </button>

                        <div class="flex gap-4 w-full sm:w-auto">
                            <a href="{{ route('myVehicle') }}"
                                class="{{ $styles['btn_secondary'] }} flex-1 sm:flex-none text-center">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="{{ $styles['btn_primary'] }} flex-1 sm:flex-none justify-center">
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </x-slot>
</x-app-layout>