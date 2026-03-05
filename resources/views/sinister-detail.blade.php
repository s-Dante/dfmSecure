@php
    $styles = [
        'page_container' => 'w-full max-w-7xl mx-auto pb-10',
        
        // Grid Layout Base
        'main_grid' => 'grid grid-cols-1 lg:grid-cols-12 gap-8 items-start',
        'left_column' => 'lg:col-span-8 space-y-8',
        'right_column' => 'lg:col-span-4 sticky top-6 self-start',
        
        // Global Card Style
        'card' => 'bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-extra/30',
        'section_title' => 'text-xl font-bold text-quaternary mb-4 flex items-center gap-2 border-b border-extra/30 pb-3',
        
        // Header Section
        'header_card' => 'bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-extra/30 relative overflow-hidden',
        'folio_title' => 'text-3xl font-extrabold text-quaternary mb-2',
        'header_meta' => 'flex flex-wrap items-center gap-4 text-sm text-tertiary font-medium',
        'header_badge' => 'px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider',
        // Example dynamic badge logic (mocked)
        'status_pending' => 'bg-yellow-100 text-yellow-700',
        
        // Data Grids inside cards
        'data_grid' => 'grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 mt-6',
        'data_item' => 'flex flex-col',
        'data_label' => 'text-sm font-semibold text-tertiary mb-1',
        'data_value' => 'text-base font-medium text-quaternary',
        
        // Multimedia Gallery
        'gallery_grid' => 'grid grid-cols-2 md:grid-cols-3 gap-4 mt-6',
        'gallery_img_wrapper' => 'aspect-square rounded-2xl overflow-hidden bg-secondary/30 border border-extra/30 cursor-pointer group relative',
        'gallery_img' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-110',
        'gallery_overlay' => 'absolute inset-0 bg-quaternary/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center',
        
        // Chat Thread Container
        'chat_container' => 'flex flex-col h-[calc(100vh-8rem)] min-h-[500px] max-h-[800px]',
        'chat_header' => 'p-6 border-b border-extra/30 flex justify-between items-center',
        'chat_title' => 'text-lg font-bold text-quaternary',
        'chat_body' => 'flex-1 overflow-y-auto p-6 space-y-6 bg-secondary/10',
        
        // Chat Messages
        'msg_wrapper_right' => 'flex flex-col items-end',
        'msg_wrapper_left' => 'flex flex-col items-start',
        'msg_bubble_right' => 'bg-accent text-white px-5 py-3 rounded-2xl rounded-tr-sm max-w-[85%] shadow-sm',
        'msg_bubble_left' => 'bg-white text-quaternary border border-extra/30 px-5 py-3 rounded-2xl rounded-tl-sm max-w-[85%] shadow-sm',
        'msg_meta' => 'text-xs text-tertiary mt-1 font-medium',
        
        // Chat Input Footer
        'chat_footer' => 'p-4 border-t border-extra/30 bg-white rounded-b-3xl',
        'chat_input_wrapper' => 'flex items-end gap-2',
        'chat_input' => 'w-full px-4 py-3 bg-secondary/20 rounded-2xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent resize-none placeholder-tertiary/70 text-sm h-12',
        'chat_btn' => 'p-3 bg-accent text-white rounded-xl hover:bg-black transition-colors shrink-0 flex items-center justify-center h-12 w-12',
    ];

    // Dummy Data for the view
    $sinister = [
        'folio' => 'SIN-2023-002',
        'status' => 'En Revisión (Ajuste)',
        'date' => '24 / Oct / 2023 - 14:35 hrs',
        'location' => 'Av. Insurgentes Sur 1234, Col. Del Valle, CDMX',
        'description' => 'Colisión por alcance en semáforo. El vehículo asegurado fue impactado en la parte trasera por una camioneta de carga que no logró frenar a tiempo debido a lluvia ligera. No se reportan heridos de gravedad.',
        'third_party' => 'Camioneta Nissan NP300 Cargo (Placas: HZ-123-BC)',
        'vehicle' => 'Ford Mustang 2024 (Placas: ASD-987-X)',
        'policy_type' => 'Cobertura Amplia Premium',
        'policy_expiry' => '12 / Dic / 2024',
        'deductible' => '5% Daños Materiales',
    ];

    // Dummy Gallery Images
    $gallery = [
        'https://images.unsplash.com/photo-1543398933-2195f2fc4de7?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1520108398463-5490bc89a7da?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1502877338535-766e1452684a?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1600587713431-70fb905c088f?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
    ];
@endphp

<x-app-layout>
    <x-slot name="content">
        <div class="{{ $styles['page_container'] }}">
            
            <div class="{{ $styles['main_grid'] }}">
                
                <!-- COLUMNA IZQUIERDA: INFORMACIÓN PRINCIPAL -->
                <div class="{{ $styles['left_column'] }}">
                    
                    <!-- Tarjeta de Encabezado -->
                    <div class="{{ $styles['header_card'] }}">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-4">
                            <div>
                                <h1 class="{{ $styles['folio_title'] }}">{{ $sinister['folio'] }}</h1>
                                <div class="{{ $styles['header_meta'] }}">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $sinister['date'] }}
                                    </span>
                                </div>
                            </div>
                            <!-- Badge Status -->
                            <span class="{{ $styles['header_badge'] }} {{ $styles['status_pending'] }}">
                                {{ $sinister['status'] }}
                            </span>
                        </div>
                        <div class="flex items-start gap-2 text-tertiary bg-secondary/20 p-4 rounded-2xl">
                            <svg class="w-5 h-5 text-accent shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-sm font-medium">{{ $sinister['location'] }}</span>
                        </div>
                    </div>

                    <!-- Detalles Generales del Incidente -->
                    <div class="{{ $styles['card'] }}">
                        <h2 class="{{ $styles['section_title'] }}">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Relato y Detalles del Incidente
                        </h2>
                        <p class="text-tertiary leading-relaxed text-sm md:text-base mt-4">
                            {{ $sinister['description'] }}
                        </p>
                        
                        <div class="mt-6 p-4 rounded-2xl border border-extra/30 bg-secondary/10">
                            <h3 class="text-sm font-bold text-quaternary mb-2 uppercase tracking-wider">Tercero Involucrado Registrado</h3>
                            <p class="text-quaternary font-medium">{{ $sinister['third_party'] }}</p>
                        </div>
                    </div>

                    <!-- Detalles de la Póliza -->
                    <div class="{{ $styles['card'] }}">
                        <h2 class="{{ $styles['section_title'] }}">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Datos de Póliza
                        </h2>
                        <div class="{{ $styles['data_grid'] }}">
                            <div class="{{ $styles['data_item'] }}">
                                <span class="{{ $styles['data_label'] }}">Vehículo Asegurado</span>
                                <span class="{{ $styles['data_value'] }}">{{ $sinister['vehicle'] }}</span>
                            </div>
                            <div class="{{ $styles['data_item'] }}">
                                <span class="{{ $styles['data_label'] }}">Tipo de Cobertura</span>
                                <span class="{{ $styles['data_value'] }}">{{ $sinister['policy_type'] }}</span>
                            </div>
                            <div class="{{ $styles['data_item'] }}">
                                <span class="{{ $styles['data_label'] }}">Deducible Aplicable</span>
                                <span class="{{ $styles['data_value'] }}">{{ $sinister['deductible'] }}</span>
                            </div>
                            <div class="{{ $styles['data_item'] }}">
                                <span class="{{ $styles['data_label'] }}">Vencimiento de Póliza</span>
                                <span class="{{ $styles['data_value'] }}">{{ $sinister['policy_expiry'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Galería de Evidencia (Multimeda) -->
                    <div class="{{ $styles['card'] }}">
                        <h2 class="{{ $styles['section_title'] }}">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Evidencia Fotográfica
                        </h2>
                        <div class="{{ $styles['gallery_grid'] }}">
                            @foreach($gallery as $img)
                            <div class="{{ $styles['gallery_img_wrapper'] }}">
                                <img src="{{ $img }}" alt="Evidencia de siniestro" class="{{ $styles['gallery_img'] }}">
                                <div class="{{ $styles['gallery_overlay'] }}">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                </div>
                            </div>
                            @endforeach
                            
                            <!-- Botón para ver más o subir fotos (según rol, por ahora visible) -->
                            <div class="{{ $styles['gallery_img_wrapper'] }} !bg-secondary/10 border-dashed border-2 flex flex-col items-center justify-center text-tertiary hover:text-accent hover:border-accent hover:bg-accent/5 transition-colors">
                                <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span class="font-bold text-sm">Añadir Evidencia</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- COLUMNA DERECHA: HILO DE CONVERSACIÓN (STICKY) -->
                <div class="{{ $styles['right_column'] }}">
                    <div class="{{ $styles['card'] }} !p-0 overflow-hidden {{ $styles['chat_container'] }}">
                        
                        <!-- Header del Chat -->
                        <div class="{{ $styles['chat_header'] }}">
                            <h3 class="{{ $styles['chat_title'] }} flex items-center gap-2">
                                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                Conversación
                        </div>

                        <!-- Área de Mensajes (Scrollable) -->
                        <div class="{{ $styles['chat_body'] }}">
                            
                            <!-- Mensaje Sistema / Ajustador -->
                            <div class="{{ $styles['msg_wrapper_left'] }}">
                                <div class="{{ $styles['msg_bubble_left'] }}">
                                    <p class="text-sm">Buenas tardes, ya he llegado al lugar del incidente. Comenzaré a documentar los daños.</p>
                                </div>
                                <span class="{{ $styles['msg_meta'] }}">Ajustador: Roberto M. • 14:45 hrs</span>
                            </div>

                            <!-- Mensaje Asegurado (Tú) -->
                            <div class="{{ $styles['msg_wrapper_right'] }}">
                                <div class="{{ $styles['msg_bubble_right'] }}">
                                    <p class="text-sm">Enterado, estoy aquí junto a la grúa blanca.</p>
                                </div>
                                <span class="{{ $styles['msg_meta'] }}">Tú (Asegurado) • 14:47 hrs</span>
                            </div>
                            
                            <!-- Mensaje Sistema / Ajustador -->
                            <div class="{{ $styles['msg_wrapper_left'] }}">
                                <div class="{{ $styles['msg_bubble_left'] }}">
                                    <p class="text-sm">He subido las primeras 4 fotografías. El peritaje inicial indica que el Taller "Mecánica Express del Valle" es la mejor opción. ¿Estás de acuerdo para generar la orden?</p>
                                </div>
                                <span class="{{ $styles['msg_meta'] }}">Ajustador: Roberto M. • 15:10 hrs</span>
                            </div>

                        </div>

                        <!-- Footer Input de Chat -->
                        <div class="{{ $styles['chat_footer'] }}">
                            <form action="#" class="{{ $styles['chat_input_wrapper'] }}">
                                <textarea placeholder="Escribe un mensaje..." class="{{ $styles['chat_input'] }}" rows="1"></textarea>
                                <button type="submit" class="{{ $styles['chat_btn'] }}">
                                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </x-slot>
</x-app-layout>