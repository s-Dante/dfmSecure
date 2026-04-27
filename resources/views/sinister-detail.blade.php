@php
$styles = [
'page_container' => 'w-full max-w-7xl mx-auto pb-10',
'main_grid' => 'grid grid-cols-1 lg:grid-cols-12 gap-8 items-start',
'left_column' => 'lg:col-span-8 space-y-8',
'right_column' => 'lg:col-span-4 sticky top-6 self-start',
'card' => 'bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-extra/30',
'section_title' => 'text-xl font-bold text-quaternary mb-4 flex items-center gap-2 border-b border-extra/30 pb-3',
'header_card' => 'bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-extra/30 relative overflow-hidden',
'folio_title' => 'text-3xl font-extrabold text-quaternary mb-2',
'header_meta' => 'flex flex-wrap items-center gap-4 text-sm text-tertiary font-medium',
'header_badge' => 'px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider',
'data_grid' => 'grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 mt-6',
'data_item' => 'flex flex-col',
'data_label' => 'text-sm font-semibold text-tertiary mb-1',
'data_value' => 'text-base font-medium text-quaternary',
'gallery_grid' => 'grid grid-cols-2 md:grid-cols-3 gap-4 mt-6',
'gallery_img_wrapper' => 'aspect-square rounded-2xl overflow-hidden bg-secondary/30 border border-extra/30 cursor-pointer group relative',
'gallery_img' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-110',
'gallery_overlay'=> 'absolute inset-0 bg-quaternary/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center',
'chat_container' => 'flex flex-col h-[calc(100vh-8rem)] min-h-[500px] max-h-[800px]',
'chat_header' => 'p-6 border-b border-extra/30 flex justify-between items-center',
'chat_title' => 'text-lg font-bold text-quaternary',
'chat_body' => 'flex-1 overflow-y-auto p-6 space-y-6 bg-secondary/10',
'msg_wrapper_right' => 'flex flex-col items-end',
'msg_wrapper_left' => 'flex flex-col items-start',
'msg_bubble_right' => 'bg-accent text-white px-5 py-3 rounded-2xl rounded-tr-sm max-w-[85%] shadow-sm',
'msg_bubble_left' => 'bg-white text-quaternary border border-extra/30 px-5 py-3 rounded-2xl rounded-tl-sm max-w-[85%] shadow-sm',
'msg_meta' => 'text-xs text-tertiary mt-1 font-medium',
'chat_footer' => 'p-4 border-t border-extra/30 bg-white rounded-b-3xl',
'chat_input_wrapper' => 'flex items-end gap-2',
'chat_input' => 'w-full px-4 py-3 bg-secondary/20 rounded-2xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent resize-none placeholder-tertiary/70 text-sm',
'chat_btn' => 'p-3 bg-accent text-white rounded-xl hover:bg-black transition-colors shrink-0 flex items-center justify-center h-12 w-12',
];

// Status badge
$statusRaw = $sinister->status instanceof \BackedEnum ? $sinister->status->value : (string) $sinister->status;
$statusLabel = $sinister->status instanceof \App\Enums\SinisterStatusEnum
? $sinister->status->label()
: ucfirst(str_replace('_', ' ', $statusRaw));

$badgeColor = match($statusRaw) {
'in_review' => 'bg-yellow-100 text-yellow-700',
'approved','approved_with_deductible','approved_without_deductible',
'applies_payment_for_repairs','closed' => 'bg-green-100 text-green-700',
'rejected' => 'bg-red-100 text-red-700',
'total_loss' => 'bg-gray-200 text-gray-700',
default => 'bg-secondary text-tertiary',
};

// Vehicle
$vehicle = $sinister->policy?->vehicle;
$vm = $vehicle?->vehicleModel;
$vehicleName = $vm ? trim(($vm->brand ?? '') . ' ' . ($vm->sub_brand ?? '') . ' ' . ($vm->year ?? '')) : 'N/A';
$vehiclePlate= $vehicle?->plate ?? 'N/A';

// Policy
$policy = $sinister->policy;
$plan = $policy?->plan;
$policyEnd = $policy?->end_validity?->format('d / M / Y') ?? 'N/A';

// Helper: obtener src de un media (blob → URL de streaming, path → asset)
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
        <div class="{{ $styles['page_container'] }}">

            {{-- Flash éxito --}}
            @if(session('success'))
            <div class="mb-6 px-5 py-3 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-semibold">
                ✓ {{ session('success') }}
            </div>
            @endif

            <div class="{{ $styles['main_grid'] }}">

                <!-- COLUMNA IZQUIERDA -->
                <div class="{{ $styles['left_column'] }}">

                    <!-- Encabezado -->
                    <div class="{{ $styles['header_card'] }}">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-4">
                            <div>
                                <h1 class="{{ $styles['folio_title'] }}">
                                    {{ $sinister->sinister_number ?? ('SIN-' . str_pad($sinister->id, 5, '0', STR_PAD_LEFT)) }}
                                </h1>
                                <div class="{{ $styles['header_meta'] }}">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Reportado: {{ $sinister->report_date?->format('d / M / Y') ?? 'N/A' }}
                                    </span>
                                    @if($sinister->occur_date)
                                    <span>Ocurrido: {{ $sinister->occur_date->format('d / M / Y') }}</span>
                                    @endif
                                </div>
                            </div>
                            <!-- Agregado de Botón Supervisor -->
                            @if(auth()->check() && auth()->user()->isSupervisor())
                            <div class="mt-4 md:mt-0 flex gap-2">
                                <a href="{{ route('supervisor.sinisterManage', $sinister->id) }}" class="bg-quaternary hover:bg-black text-white px-5 py-2.5 rounded-xl font-bold transition-colors shadow flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Dictaminar / Gestionar Estatus
                                </a>
                            </div>
                            @endif

                            <!-- Botón Editar Ajustador -->
                            @if(auth()->check() && auth()->user()->isAdjuster() && in_array($statusRaw, ['reported', 'in_review']))
                            <div class="mt-4 md:mt-0 flex gap-2">
                                <a href="{{ route('sinisterEdit', $sinister->id) }}" class="bg-accent hover:bg-black text-white px-5 py-2.5 rounded-xl font-bold transition-colors shadow flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar Siniestro
                                </a>
                            </div>
                            @endif
                            <span class="{{ $styles['header_badge'] }} {{ $badgeColor }}">{{ $statusLabel }}</span>
                        </div>
                        @if($sinister->location)
                        <div class="flex items-start gap-2 text-tertiary bg-secondary/20 p-4 rounded-2xl">
                            <svg class="w-5 h-5 text-accent shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm font-medium">{{ $sinister->location }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Relato -->
                    <div class="{{ $styles['card'] }}">
                        <h2 class="{{ $styles['section_title'] }}">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Relato y Detalles del Incidente
                        </h2>
                        <p class="text-tertiary leading-relaxed text-sm md:text-base mt-4">
                            {{ $sinister->description ?? 'Sin descripción registrada.' }}
                        </p>
                    </div>

                    <!-- Póliza y Vehículo -->
                    <div class="{{ $styles['card'] }}">
                        <h2 class="{{ $styles['section_title'] }}">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Datos de Póliza
                        </h2>
                        <div class="{{ $styles['data_grid'] }}">
                            <div class="{{ $styles['data_item'] }}">
                                <span class="{{ $styles['data_label'] }}">Vehículo Asegurado</span>
                                <span class="{{ $styles['data_value'] }}">{{ $vehicleName }} ({{ $vehiclePlate }})</span>
                            </div>
                            <div class="{{ $styles['data_item'] }}">
                                <span class="{{ $styles['data_label'] }}">Plan / Cobertura</span>
                                <span class="{{ $styles['data_value'] }}">{{ $plan?->name ?? 'N/A' }}</span>
                            </div>
                            <div class="{{ $styles['data_item'] }}">
                                <span class="{{ $styles['data_label'] }}">Folio de Póliza</span>
                                <span class="{{ $styles['data_value'] }}">{{ $policy?->folio ?? $policy?->policy_number ?? 'N/A' }}</span>
                            </div>
                            <div class="{{ $styles['data_item'] }}">
                                <span class="{{ $styles['data_label'] }}">Vencimiento de Póliza</span>
                                <span class="{{ $styles['data_value'] }}">{{ $policyEnd }}</span>
                            </div>
                            @if($sinister->adjuster)
                            <div class="{{ $styles['data_item'] }}">
                                <span class="{{ $styles['data_label'] }}">Ajustador Asignado</span>
                                <span class="{{ $styles['data_value'] }}">{{ $sinister->adjuster->name }} {{ $sinister->adjuster->father_lastname }}</span>
                            </div>
                            @endif
                            @if($sinister->close_date)
                            <div class="{{ $styles['data_item'] }}">
                                <span class="{{ $styles['data_label'] }}">Fecha de Cierre</span>
                                <span class="{{ $styles['data_value'] }}">{{ $sinister->close_date->format('d / M / Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Galería: Fotos -->
                    @php
                    $photos = $sinister->multimedia->filter(fn($m) => $m->type === \App\Enums\SinisterMultimediaTypeEnum::PHOTO);
                    $videos = $sinister->multimedia->filter(fn($m) => $m->type === \App\Enums\SinisterMultimediaTypeEnum::VIDEO);
                    @endphp

                    @if($photos->count() > 0)
                    <div class="{{ $styles['card'] }}">
                        <h2 class="{{ $styles['section_title'] }}">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Evidencia Fotográfica
                        </h2>
                        <div class="{{ $styles['gallery_grid'] }}">
                            @foreach($photos as $media)
                            @php $src = $mediaSrc($media); @endphp
                            @if($src)
                            <div class="{{ $styles['gallery_img_wrapper'] }}">
                                <img src="{{ $src }}" alt="Evidencia {{ $media->id }}" class="{{ $styles['gallery_img'] }}">
                                <div class="{{ $styles['gallery_overlay'] }}">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                    </svg>
                                </div>
                            </div>
                            @endif
                            @endforeach

                            @if(auth()->user()->isAdjuster() || auth()->user()->isSupervisor() || auth()->user()->isAdmin())
                            <div class="{{ $styles['gallery_img_wrapper'] }} !bg-secondary/10 border-dashed border-2 flex flex-col items-center justify-center text-tertiary hover:text-accent hover:border-accent hover:bg-accent/5 transition-colors">
                                <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="font-bold text-sm">Añadir Evidencia</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Videos -->
                    @if($videos->count() > 0)
                    <div class="{{ $styles['card'] }}">
                        <h2 class="{{ $styles['section_title'] }}">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Evidencia en Video
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                            @foreach($videos as $media)
                            @php $src = $mediaSrc($media); @endphp
                            @if($src)
                            <div class="rounded-2xl overflow-hidden bg-black border border-extra/30">
                                <video controls class="w-full max-h-64 object-contain" preload="metadata">
                                    <source src="{{ $src }}" type="{{ $media->mime ?? 'video/mp4' }}">
                                    Tu navegador no soporta reproducción de video.
                                </video>
                                @if($media->description)
                                <p class="text-xs text-tertiary p-3">{{ $media->description }}</p>
                                @endif
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>

                <!-- COLUMNA DERECHA: COMENTARIOS -->
                <div class="{{ $styles['right_column'] }}">
                    <div class="{{ $styles['card'] }} !p-0 overflow-hidden {{ $styles['chat_container'] }}">

                        <div class="{{ $styles['chat_header'] }}">
                            <h3 class="{{ $styles['chat_title'] }} flex items-center gap-2">
                                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Conversación
                            </h3>
                            <span class="text-xs text-tertiary font-medium">{{ $sinister->comments->count() }} mensajes</span>
                        </div>

                        <div class="{{ $styles['chat_body'] }}" id="chat-body">
                            @forelse($sinister->comments->sortBy('created_at') as $comment)
                            @php $isOwn = $comment->user_id === auth()->id(); @endphp
                            <div class="{{ $isOwn ? $styles['msg_wrapper_right'] : $styles['msg_wrapper_left'] }}">
                                <div class="{{ $isOwn ? $styles['msg_bubble_right'] : $styles['msg_bubble_left'] }}">
                                    <p class="text-sm">{{ $comment->comment }}</p>
                                </div>
                                <span class="{{ $styles['msg_meta'] }}">
                                    {{ $isOwn ? 'Tú' : ($comment->user?->name . ' ' . $comment->user?->father_lastname) }}
                                    • {{ $comment->created_at?->format('d/M H:i') }}
                                </span>
                            </div>
                            @empty
                            <div class="text-center text-tertiary text-sm py-8">
                                Aún no hay mensajes en esta conversación.
                            </div>
                            @endforelse
                        </div>

                        <!-- Formulario de comentario -->
                        <div class="{{ $styles['chat_footer'] }}">
                            @error('comment')
                            <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
                            @enderror
                            <form action="{{ route('sinisterComment', $sinister->id) }}" method="POST"
                                class="{{ $styles['chat_input_wrapper'] }}">
                                @csrf
                                <textarea id="comment" name="comment"
                                    placeholder="Escribe un mensaje..."
                                    class="{{ $styles['chat_input'] }}"
                                    rows="2"
                                    maxlength="1000"
                                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.form.submit();}">{{ old('comment') }}</textarea>
                                <button type="submit" class="{{ $styles['chat_btn'] }}" title="Enviar">
                                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </x-slot>

    {{-- Auto-scroll al final del chat --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatBody = document.getElementById('chat-body');
            if (chatBody) chatBody.scrollTop = chatBody.scrollHeight;
        });
    </script>
    @endpush
</x-app-layout>