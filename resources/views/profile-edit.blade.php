@php
    $styles = [
        'page_container' => 'w-full max-w-4xl mx-auto space-y-8 pb-10',
        'page_title'     => 'text-3xl font-extrabold text-quaternary',
        'card'           => 'bg-white p-6 lg:p-8 rounded-3xl shadow-sm border border-extra/30',
        'section_title'  => 'text-lg font-bold text-quaternary mb-6 flex items-center gap-2 border-b border-extra/30 pb-3',
        'grid_2'         => 'grid grid-cols-1 md:grid-cols-2 gap-6',
        'label'          => 'block text-sm font-semibold text-quaternary mb-1.5',
        'input'          => 'w-full px-4 py-2.5 rounded-xl border border-extra/50 bg-secondary/10 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary placeholder-tertiary/70',
        'select'         => 'w-full px-4 py-2.5 rounded-xl border border-extra/50 bg-secondary/10 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary',
        'error'          => 'text-red-500 text-xs mt-1',
        'btn_primary'    => 'px-8 py-3 bg-accent text-white font-bold rounded-xl hover:bg-black transition-colors',
        'btn_secondary'  => 'px-8 py-3 bg-secondary text-quaternary font-semibold rounded-xl hover:bg-extra/30 transition-colors',
    ];

    // Avatar URL actual
    $hasBlob   = !empty($user->profile_picture_blob);
    $avatarUrl = $hasBlob
        ? route('media.profile', $user->id) . '?v=' . time()
        : (!empty($user->profile_picture_url)
            ? asset($user->profile_picture_url)
            : 'https://ui-avatars.com/api/?name=' . urlencode($user->name . '+' . $user->father_lastname) . '&background=92AA74&color=fff&size=256');

    $currentGender = $user->gender instanceof \BackedEnum ? $user->gender->value : (string)($user->gender ?? '');
    $currentRegime = $user->fiscalData?->tax_regime instanceof \BackedEnum ? $user->fiscalData->tax_regime->value : (string)($user->fiscalData?->tax_regime ?? '');
@endphp

<x-app-layout>
    <x-slot name="content">
        <div class="{{ $styles['page_container'] }}">

            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="{{ $styles['page_title'] }}">Editar Perfil</h1>
                    <p class="text-tertiary mt-1">Actualiza tu información personal y preferencias.</p>
                </div>
                <a href="{{ route('profile') }}"
                    class="px-5 py-2.5 border-2 border-quaternary text-quaternary font-semibold rounded-xl hover:bg-quaternary hover:text-white transition-colors text-sm">
                    ← Volver al Perfil
                </a>
            </div>

            {{-- Flash mensajes --}}
            @if(session('success'))
                <div class="px-5 py-3 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-semibold">
                    ✓ {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PATCH')

                {{-- ── FOTO DE PERFIL ── --}}
                <div class="{{ $styles['card'] }}">
                    <h2 class="{{ $styles['section_title'] }}">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Foto de Perfil
                    </h2>

                    <div class="flex flex-col md:flex-row gap-8 items-center">
                        {{-- Preview actual --}}
                        <div class="w-28 h-28 rounded-full border-4 border-white shadow-lg overflow-hidden bg-secondary shrink-0">
                            <img id="avatar-preview" src="{{ $avatarUrl }}" alt="Avatar actual" class="w-full h-full object-cover">
                        </div>

                        <div class="flex-1 space-y-4">
                            <div>
                                <label for="photo" class="{{ $styles['label'] }}">Seleccionar nueva foto</label>
                                <input type="file" id="photo" name="photo" accept="image/*"
                                    class="w-full text-sm text-tertiary file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-accent/10 file:text-accent file:font-semibold hover:file:bg-accent/20 transition-all cursor-pointer"
                                    onchange="previewPhoto(this)">
                                @error('photo') <p class="{{ $styles['error'] }}">{{ $message }}</p> @enderror
                            </div>

                            {{-- Radio: cómo guardar --}}
                            <div>
                                <p class="{{ $styles['label'] }}">¿Cómo guardar la foto?</p>
                                <div class="flex gap-6 mt-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="photo_storage" value="url"
                                            {{ old('photo_storage', 'url') === 'url' ? 'checked' : '' }}
                                            class="accent-accent">
                                        <span class="text-sm font-medium text-quaternary">Como archivo (URL)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="photo_storage" value="blob"
                                            {{ old('photo_storage') === 'blob' ? 'checked' : '' }}
                                            class="accent-accent">
                                        <span class="text-sm font-medium text-quaternary">Como binario (BLOB)</span>
                                    </label>
                                </div>
                                <p class="text-xs text-tertiary mt-1">
                                    URL: más eficiente para mostrar. BLOB: se almacena directo en la base de datos.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── DATOS PERSONALES ── --}}
                <div class="{{ $styles['card'] }}">
                    <h2 class="{{ $styles['section_title'] }}">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Datos Personales
                    </h2>

                    <div class="{{ $styles['grid_2'] }}">
                        <div>
                            <label for="name" class="{{ $styles['label'] }}">Nombre(s) <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="{{ $styles['input'] }}" required>
                            @error('name') <p class="{{ $styles['error'] }}">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="father_lastname" class="{{ $styles['label'] }}">Apellido Paterno <span class="text-red-500">*</span></label>
                            <input type="text" id="father_lastname" name="father_lastname" value="{{ old('father_lastname', $user->father_lastname) }}" class="{{ $styles['input'] }}" required>
                            @error('father_lastname') <p class="{{ $styles['error'] }}">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="mother_lastname" class="{{ $styles['label'] }}">Apellido Materno</label>
                            <input type="text" id="mother_lastname" name="mother_lastname" value="{{ old('mother_lastname', $user->mother_lastname) }}" class="{{ $styles['input'] }}">
                        </div>
                        <div>
                            <label for="phone" class="{{ $styles['label'] }}">Teléfono</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="{{ $styles['input'] }}" placeholder="+52 55 1234 5678">
                        </div>
                        <div>
                            <label for="birth_date" class="{{ $styles['label'] }}">Fecha de Nacimiento</label>
                            <input type="date" id="birth_date" name="birth_date"
                                value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"
                                class="{{ $styles['input'] }}">
                        </div>
                        <div>
                            <label for="gender" class="{{ $styles['label'] }}">Género</label>
                            <select id="gender" name="gender" class="{{ $styles['select'] }}">
                                <option value="">— Sin especificar —</option>
                                @foreach($genderOptions as $g)
                                    <option value="{{ $g->value }}" {{ old('gender', $currentGender) === $g->value ? 'selected' : '' }}>
                                        {{ method_exists($g, 'label') ? $g->label() : ucfirst($g->value) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ── DIRECCIÓN (solo asegurado) ── --}}
                @if($user->isInsured())
                <div class="{{ $styles['card'] }}">
                    <h2 class="{{ $styles['section_title'] }}">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Dirección
                    </h2>
                    <div class="{{ $styles['grid_2'] }}">
                        <div class="md:col-span-2">
                            <label for="address_type" class="{{ $styles['label'] }}">Tipo de Dirección</label>
                            <select id="address_type" name="type" class="{{ $styles['select'] }}">
                                <option value="">— Seleccionar tipo —</option>
                                @foreach(\App\Enums\AddressTypeEnum::cases() as $type)
                                    <option value="{{ $type->value }}" {{ old('type', $user->address?->type?->value ?? $user->address?->type) === $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="street" class="{{ $styles['label'] }}">Calle</label>
                            <input type="text" id="street" name="street" value="{{ old('street', $user->address?->street) }}" class="{{ $styles['input'] }}">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="external_number" class="{{ $styles['label'] }}">Núm. Ext.</label>
                                <input type="text" id="external_number" name="external_number" value="{{ old('external_number', $user->address?->external_number) }}" class="{{ $styles['input'] }}">
                            </div>
                            <div>
                                <label for="internal_number" class="{{ $styles['label'] }}">Núm. Int.</label>
                                <input type="text" id="internal_number" name="internal_number" value="{{ old('internal_number', $user->address?->internal_number) }}" class="{{ $styles['input'] }}">
                            </div>
                        </div>
                        <div>
                            <label for="neighborhood" class="{{ $styles['label'] }}">Colonia</label>
                            <input type="text" id="neighborhood" name="neighborhood" value="{{ old('neighborhood', $user->address?->neighborhood) }}" class="{{ $styles['input'] }}">
                        </div>
                        <div>
                            <label for="zip_code" class="{{ $styles['label'] }}">Código Postal</label>
                            <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code', $user->address?->zip_code) }}" maxlength="10" class="{{ $styles['input'] }}">
                        </div>
                        <div class="md:col-span-2">
                            <label for="country" class="{{ $styles['label'] }}">País</label>
                            <select id="country" name="country" class="{{ $styles['select'] }}" onchange="loadStates(this.options[this.selectedIndex].dataset.id)">
                                <option value="">— Seleccionar País —</option>
                            </select>
                        </div>
                        <div>
                            <label for="state" class="{{ $styles['label'] }}">Estado</label>
                            <select id="state" name="state" class="{{ $styles['select'] }}" onchange="loadCities(this.options[this.selectedIndex].dataset.id)" disabled>
                                <option value="">— Seleccione primero un país —</option>
                            </select>
                        </div>
                        <div>
                            <label for="city" class="{{ $styles['label'] }}">Ciudad</label>
                            <select id="city" name="city" class="{{ $styles['select'] }}" disabled>
                                <option value="">— Seleccione primero un estado —</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ── DATOS FISCALES (solo asegurado) ── --}}
                <div class="{{ $styles['card'] }}">
                    <h2 class="{{ $styles['section_title'] }}">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Datos Fiscales
                        <span class="text-xs font-normal text-tertiary ml-2">(Opcional)</span>
                    </h2>
                    <div class="{{ $styles['grid_2'] }}">
                        <div>
                            <label for="rfc" class="{{ $styles['label'] }}">RFC</label>
                            <input type="text" id="rfc" name="rfc" value="{{ old('rfc', $user->fiscalData?->rfc) }}" maxlength="13" placeholder="XXXX000000XXX" class="{{ $styles['input'] }}">
                            @error('rfc') <p class="{{ $styles['error'] }}">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="company_name" class="{{ $styles['label'] }}">Razón Social</label>
                            <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $user->fiscalData?->company_name) }}" class="{{ $styles['input'] }}">
                        </div>
                        <div class="md:col-span-2">
                            <label for="tax_regime" class="{{ $styles['label'] }}">Régimen Fiscal</label>
                            <select id="tax_regime" name="tax_regime" class="{{ $styles['select'] }}">
                                <option value="">— Seleccionar régimen —</option>
                                @foreach($taxRegimeOptions as $regime)
                                    <option value="{{ $regime->value }}" {{ old('tax_regime', $currentRegime) === $regime->value ? 'selected' : '' }}>
                                        {{ $regime->value }} — {{ $regime->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Botones de acción --}}
                <div class="flex gap-4 justify-end">
                    <a href="{{ route('profile') }}" class="{{ $styles['btn_secondary'] }}">Cancelar</a>
                    <button type="submit" class="{{ $styles['btn_primary'] }}">Guardar Cambios</button>
                </div>

            </form>

        </div>
    </x-slot>

    @push('scripts')
    <script>
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        // Dynamic Locations Loader
        const baseUrl = '{{ asset("storage/locations") }}';
        
        let initialCountry = "{{ old('country', $user->address?->country) }}";
        let initialState = "{{ old('state', $user->address?->state) }}";
        let initialCity = "{{ old('city', $user->address?->city) }}";

        document.addEventListener('DOMContentLoaded', function() {
            // Load all countries
            fetch(`${baseUrl}/countries.json`)
                .then(r => r.json())
                .then(data => {
                    const countrySelect = document.getElementById('country');
                    countrySelect.innerHTML = '<option value="">— Seleccionar País —</option>';
                    let selectedId = null;
                    data.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.name;
                        opt.dataset.id = c.id;
                        opt.textContent = c.name;
                        if (c.name === initialCountry) {
                            opt.selected = true;
                            selectedId = c.id;
                        }
                        countrySelect.appendChild(opt);
                    });
                    if (selectedId) loadStates(selectedId);
                });
        });

        function loadStates(countryId) {
            const stateSelect = document.getElementById('state');
            const citySelect = document.getElementById('city');
            
            stateSelect.innerHTML = '<option value="">Cargando...</option>';
            stateSelect.disabled = true;
            citySelect.innerHTML = '<option value="">— Seleccione primero un estado —</option>';
            citySelect.disabled = true;

            if (!countryId) return;

            fetch(`${baseUrl}/states/${countryId}.json`)
                .then(r => r.ok ? r.json() : [])
                .then(data => {
                    stateSelect.innerHTML = '<option value="">— Seleccionar Estado —</option>';
                    let selectedId = null;
                    data.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.name;
                        opt.dataset.id = s.id;
                        opt.textContent = s.name;
                        if (s.name === initialState) {
                            opt.selected = true;
                            selectedId = s.id;
                        }
                        stateSelect.appendChild(opt);
                    });
                    stateSelect.disabled = false;
                    if (selectedId) loadCities(selectedId);
                })
                .catch(() => stateSelect.innerHTML = '<option value="">Sin estados</option>');
        }

        function loadCities(stateId) {
            const citySelect = document.getElementById('city');
            citySelect.innerHTML = '<option value="">Cargando...</option>';
            citySelect.disabled = true;

            if (!stateId) return;

            fetch(`${baseUrl}/cities/${stateId}.json`)
                .then(r => r.ok ? r.json() : [])
                .then(data => {
                    citySelect.innerHTML = '<option value="">— Seleccionar Ciudad —</option>';
                    data.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.name;
                        opt.dataset.id = c.id;
                        opt.textContent = c.name;
                        if (c.name === initialCity) opt.selected = true;
                        citySelect.appendChild(opt);
                    });
                    citySelect.disabled = false;
                })
                .catch(() => citySelect.innerHTML = '<option value="">Sin ciudades</option>');
        }
    </script>
    @endpush
</x-app-layout>
