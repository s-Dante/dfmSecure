@php
    $styles = [
        'page_container' => 'w-full max-w-7xl mx-auto space-y-8 pb-12',
        'header_section' => 'flex flex-col md:flex-row justify-between items-start md:items-center py-4 gap-4',
        'page_title' => 'text-3xl font-extrabold text-quaternary',
        'page_subtitle' => 'text-tertiary mt-1',
        
        'card' => 'bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-extra/30 relative overflow-hidden',
        'card_title' => 'text-xl font-bold text-quaternary mb-6 flex items-center gap-2 border-b border-extra/30 pb-4',
        
        'info_grid' => 'grid grid-cols-1 md:grid-cols-2 gap-6',
        'info_item' => 'flex flex-col',
        'info_label' => 'text-xs font-semibold text-tertiary uppercase tracking-wider mb-1',
        'info_value' => 'text-base font-bold text-quaternary',
        
        'btn_primary' => 'bg-accent hover:bg-black text-white px-6 py-3 rounded-xl font-bold transition-colors inline-flex items-center justify-center gap-2 shadow-sm',
    ];

    $label = method_exists($policy->status, 'label') ? $policy->status->label() : $policy->status->value;
    $badgeStyle = match ($label) {
        'Activa', 'Activo' => 'bg-green-100 text-green-700 border-green-200',
        'Por Vencer', 'Pendiente' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'Vencida', 'Inactiva' => 'bg-red-100 text-red-700 border-red-200',
        default => 'bg-gray-100 text-gray-700 border-gray-200'
    };

    $info = $policy->plan->info ?? [];
    $danosMat = $info['deducible_danos'] ?? 'N/A';
    $roboT = 'No Amparado';
    if (isset($info['cobertura_vehiculo']['robo_total'])) {
        $roboT = $info['cobertura_vehiculo']['robo_total'] ? ($info['deducible_robo'] ?? '10%') : 'No Amparado';
    }
@endphp

<x-app-layout>
    <x-slot name="content">
        <div class="{{ $styles['page_container'] }}">
            
            <header class="{{ $styles['header_section'] }}">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('myPolicies') }}"
                            class="w-10 h-10 rounded-xl bg-white border border-extra/50 flex items-center justify-center text-tertiary hover:text-accent hover:border-accent transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <h1 class="{{ $styles['page_title'] }}">Detalle de Póliza</h1>
                    </div>
                    <p class="{{ $styles['page_subtitle'] }}">Información completa de tu seguro vehicular.</p>
                </div>
                
                <a href="{{ route('myPolicies.download', $policy->id) }}" class="{{ $styles['btn_primary'] }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Descargar Póliza (PDF)
                </a>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Columna Principal -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- Resumen General -->
                    <section class="{{ $styles['card'] }}">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-accent/5 rounded-bl-[100px] -z-10"></div>
                        <h2 class="{{ $styles['card_title'] }}">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Información de la Póliza
                        </h2>
                        
                        <div class="{{ $styles['info_grid'] }}">
                            <div class="{{ $styles['info_item'] }}">
                                <span class="{{ $styles['info_label'] }}">Folio / Certificado</span>
                                <span class="{{ $styles['info_value'] }} font-mono">{{ $policy->folio }}</span>
                            </div>
                            <div class="{{ $styles['info_item'] }}">
                                <span class="{{ $styles['info_label'] }}">Estatus</span>
                                <div>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $badgeStyle }}">
                                        {{ $label }}
                                    </span>
                                </div>
                            </div>
                            <div class="{{ $styles['info_item'] }}">
                                <span class="{{ $styles['info_label'] }}">Inicio de Vigencia</span>
                                <span class="{{ $styles['info_value'] }}">{{ $policy->begin_validity->format('d/m/Y') }}</span>
                            </div>
                            <div class="{{ $styles['info_item'] }}">
                                <span class="{{ $styles['info_label'] }}">Fin de Vigencia</span>
                                <span class="{{ $styles['info_value'] }}">{{ $policy->end_validity->format('d/m/Y') }}</span>
                            </div>
                            <div class="{{ $styles['info_item'] }}">
                                <span class="{{ $styles['info_label'] }}">Plan Contratado</span>
                                <span class="{{ $styles['info_value'] }} text-accent">{{ $policy->plan->name }}</span>
                            </div>
                        </div>
                    </section>

                    <!-- Datos del Vehículo -->
                    <section class="{{ $styles['card'] }}">
                        <h2 class="{{ $styles['card_title'] }}">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            Vehículo Asegurado
                        </h2>
                        
                        <div class="{{ $styles['info_grid'] }}">
                            <div class="{{ $styles['info_item'] }}">
                                <span class="{{ $styles['info_label'] }}">Marca y Modelo</span>
                                <span class="{{ $styles['info_value'] }}">{{ $policy->vehicle->vehicleModel->brand }} {{ $policy->vehicle->vehicleModel->sub_brand }}</span>
                            </div>
                            <div class="{{ $styles['info_item'] }}">
                                <span class="{{ $styles['info_label'] }}">Versión / Año</span>
                                <span class="{{ $styles['info_value'] }}">{{ $policy->vehicle->vehicleModel->version }} ({{ $policy->vehicle->vehicleModel->year }})</span>
                            </div>
                            <div class="{{ $styles['info_item'] }}">
                                <span class="{{ $styles['info_label'] }}">Placas</span>
                                <span class="{{ $styles['info_value'] }}">{{ $policy->vehicle->plate }}</span>
                            </div>
                            <div class="{{ $styles['info_item'] }}">
                                <span class="{{ $styles['info_label'] }}">Número de Serie (VIN)</span>
                                <span class="{{ $styles['info_value'] }} font-mono">{{ $policy->vehicle->vin }}</span>
                            </div>
                        </div>
                    </section>

                </div>

                <!-- Columna Secundaria -->
                <div class="space-y-8">
                    
                    <!-- Coberturas -->
                    <section class="{{ $styles['card'] }} bg-secondary/5 border-secondary/20">
                        <h2 class="{{ $styles['card_title'] }}">Coberturas y Deducibles</h2>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center border-b border-extra/20 pb-2">
                                <span class="text-sm font-semibold text-tertiary">Daños Materiales</span>
                                <span class="font-bold text-quaternary">{{ $danosMat }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-extra/20 pb-2">
                                <span class="text-sm font-semibold text-tertiary">Robo Total</span>
                                <span class="font-bold text-quaternary">{{ $roboT }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-extra/20 pb-2">
                                <span class="text-sm font-semibold text-tertiary">Responsabilidad Civil</span>
                                <span class="font-bold text-quaternary">Amparada</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-extra/20 pb-2">
                                <span class="text-sm font-semibold text-tertiary">Gastos Médicos</span>
                                <span class="font-bold text-quaternary">Amparada</span>
                            </div>
                            <div class="flex justify-between items-center pb-2">
                                <span class="text-sm font-semibold text-tertiary">Asistencia Legal</span>
                                <span class="font-bold text-quaternary">Amparada</span>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Condiciones Generales (Mock) -->
                    <section class="{{ $styles['card'] }}">
                        <h2 class="{{ $styles['card_title'] }}">Condiciones Generales</h2>
                        <div class="space-y-4 text-sm text-tertiary leading-relaxed">
                            <p><strong>1. Alcance de Cobertura:</strong> Esta póliza ampara los riesgos descritos en el contrato, sujeto al pago de la prima correspondiente.</p>
                            <p><strong>2. Exclusiones principales:</strong> DFM-SECURE no cubrirá daños generados si el conductor se encuentra en estado de ebriedad, carece de licencia de conducir vigente, o participa en carreras clandestinas.</p>
                            <p><strong>3. Reporte de Siniestros:</strong> En caso de accidente, es indispensable no mover el vehículo (salvo por indicación de autoridades) y comunicarse inmediatamente al centro de atención DFM-SECURE.</p>
                            <p><strong>4. Pago de Deducible:</strong> En caso de pérdida total por daños o robo, el deducible se aplicará sobre el valor comercial del vehículo en la fecha del siniestro.</p>
                            <div class="mt-4 p-4 bg-quaternary/5 rounded-xl border border-quaternary/10">
                                <p class="font-bold text-quaternary mb-1">Centro de Atención 24/7</p>
                                <p class="text-accent font-bold text-lg">800-DFM-SECU (800-336-7328)</p>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
            
        </div>
    </x-slot>
</x-app-layout>
