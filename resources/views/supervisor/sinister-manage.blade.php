@php
    $styles = [
        'page_container' => 'w-full max-w-5xl mx-auto pb-10 space-y-6',
        'card'           => 'bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-extra/30',
        'section_title'  => 'text-xl font-bold text-quaternary mb-4 flex items-center gap-2 border-b border-extra/30 pb-3',
        'data_grid'      => 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-6 gap-x-8 mt-6',
        'data_item'      => 'flex flex-col',
        'data_label'     => 'text-sm font-semibold text-tertiary mb-1',
        'data_value'     => 'text-base font-bold text-quaternary',
        'input_group'    => 'flex flex-col gap-1.5 mt-6',
        'label'          => 'text-sm font-semibold text-tertiary',
        'select'         => 'w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary appearance-none font-semibold disabled:opacity-50 disabled:cursor-not-allowed',
        'textarea'       => 'w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary resize-y min-h-[120px]',
        'btn_primary'    => 'bg-accent hover:bg-black text-white px-8 py-3 rounded-xl font-bold transition-colors inline-flex items-center gap-2 justify-center shadow-sm w-full sm:w-auto',
    ];

    // Status details
    $statusRaw = $sinister->status instanceof \BackedEnum ? $sinister->status->value : (string) $sinister->status;
    $statusLabel = $sinister->status instanceof \App\Enums\SinisterStatusEnum
        ? $sinister->status->label()
        : ucfirst(str_replace('_', ' ', $statusRaw));

    // Valid transitions
    $availableStatuses = $sinister->status->nextStatuses();
    $isClosed = empty($availableStatuses);

    // Helpers 
    $vehicle     = $sinister->policy?->vehicle;
    $vm          = $vehicle?->vehicleModel;
    $vehicleName = $vm ? trim(($vm->brand ?? '') . ' ' . ($vm->sub_brand ?? '') . ' ' . ($vm->year ?? '')) : 'N/A';
    $insured     = $sinister->policy?->insured;
    $insuredName = $insured ? trim(($insured->name ?? '') . ' ' . ($insured->father_lastname ?? '')) : 'N/A';
@endphp

<x-app-layout>
    <x-slot name="content">
        <div class="{{ $styles['page_container'] }}">

            {{-- Header --}}
            <header class="flex flex-col sm:flex-row justify-between sm:items-end gap-4 mb-2">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('sinisterDetail', $sinister->id) }}"
                            class="w-10 h-10 rounded-xl bg-white border border-extra/50 flex items-center justify-center text-tertiary hover:text-accent hover:border-accent transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        </a>
                        <h1 class="text-3xl font-extrabold text-quaternary">Dictaminar Siniestro</h1>
                    </div>
                    <p class="text-tertiary mt-1">Folio: <strong class="font-mono text-quaternary">{{ $sinister->folio }}</strong></p>
                </div>
                <div class="bg-secondary/20 px-4 py-2 rounded-xl flex items-center gap-2 border border-extra/50">
                    <span class="text-xs font-bold text-tertiary uppercase tracking-wider">Estatus Actual:</span>
                    <span class="text-sm font-extrabold text-accent">{{ $statusLabel }}</span>
                </div>
            </header>

            {{-- Errores --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-6">
                    <ul class="list-disc pl-5 text-sm font-semibold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('supervisor.updateStatus', $sinister->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Contexto General --}}
                <div class="{{ $styles['card'] }}">
                    <h2 class="{{ $styles['section_title'] }}">Contexto y Datos Generales</h2>
                    <div class="{{ $styles['data_grid'] }}">
                        <div class="{{ $styles['data_item'] }}">
                            <span class="{{ $styles['data_label'] }}">Póliza Involucrada</span>
                            <span class="{{ $styles['data_value'] }}">{{ $sinister->policy?->policy_number ?? 'N/A' }}</span>
                        </div>
                        <div class="{{ $styles['data_item'] }}">
                            <span class="{{ $styles['data_label'] }}">Vehículo</span>
                            <span class="{{ $styles['data_value'] }}">{{ $vehicleName }} ({{ $vehicle?->plate ?? 'N/A' }})</span>
                        </div>
                        <div class="{{ $styles['data_item'] }}">
                            <span class="{{ $styles['data_label'] }}">Asegurado</span>
                            <span class="{{ $styles['data_value'] }}">{{ $insuredName }}</span>
                        </div>
                        <div class="{{ $styles['data_item'] }}">
                            <span class="{{ $styles['data_label'] }}">Fecha Ocurrencia</span>
                            <span class="{{ $styles['data_value'] }}">{{ $sinister->occur_date ? $sinister->occur_date->format('d/M/Y H:i') : 'N/A' }}</span>
                        </div>
                        <div class="{{ $styles['data_item'] }}">
                            <span class="{{ $styles['data_label'] }}">Fecha Reporte</span>
                            <span class="{{ $styles['data_value'] }}">{{ $sinister->report_date ? $sinister->report_date->format('d/M/Y H:i') : 'N/A' }}</span>
                        </div>
                        @if($isClosed && $sinister->close_date)
                        <div class="{{ $styles['data_item'] }}">
                            <span class="{{ $styles['data_label'] }}">Cerrado Oficialmente</span>
                            <span class="{{ $styles['data_value'] }} text-green-600">{{ $sinister->close_date->format('d/M/Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="{{ $styles['data_item'] }} mt-6">
                        <span class="{{ $styles['data_label'] }}">Ubicación del Incidente</span>
                        <span class="{{ $styles['data_value'] }}">{{ $sinister->location ?? 'N/A' }}</span>
                    </div>
                </div>

                {{-- Actualización de Estado --}}
                <div class="{{ $styles['card'] }}">
                    <h2 class="{{ $styles['section_title'] }}">Resolución Administrativa</h2>
                    
                    @if($isClosed)
                        <div class="px-5 py-3 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl text-sm font-semibold mb-6 flex gap-3 items-center">
                            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Este siniestro se encuentra catalogado como CERRADO bajo reglas de negocio estrictas. Ya no admite transiciones de estado.
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="{{ $styles['input_group'] }} !mt-0">
                            <label class="{{ $styles['label'] }}" for="status">Estatus Operativo del Siniestro</label>
                            <div class="relative">
                                <select name="status" id="status" class="{{ $styles['select'] }}" {{ $isClosed ? 'disabled' : 'required' }}>
                                    <option value="{{ $statusRaw }}" selected disabled class="text-tertiary">Estatus Actual: {{ $statusLabel }}</option>
                                    @foreach($availableStatuses as $val => $label)
                                        <option value="{{ $val }}" {{ old('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <svg class="w-5 h-5 absolute z-10 right-4 top-3.5 text-tertiary pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            <p class="text-xs text-tertiary mt-2">Selecciona un estatus para evolucionar el siniestro con base en el flujo autorizado.</p>
                        </div>

                        <div class="{{ $styles['input_group'] }} !mt-0">
                            <label class="{{ $styles['label'] }}" for="comment">Dictamen o Comentario (Opcional)</label>
                            <textarea id="comment" name="comment" class="{{ $styles['textarea'] }}" placeholder="Añade instrucciones al ajustador, razones del dictamen o resoluciones formales...">{{ old('comment') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-4 mt-2">
                    <a href="{{ route('sinisterDetail', $sinister->id) }}" class="px-6 py-3 bg-white border border-extra/50 rounded-xl font-bold text-tertiary hover:bg-secondary/20 transition-colors">Cancelar</a>
                    <button type="submit" class="{{ $styles['btn_primary'] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Guardar Dictamen
                    </button>
                </div>
            </form>

        </div>
    </x-slot>
</x-app-layout>