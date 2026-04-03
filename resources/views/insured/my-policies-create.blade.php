<x-app-layout>
    <x-slot name="content">
        <div class="w-full max-w-7xl mx-auto space-y-10 pb-12">

            <header class="flex flex-col md:flex-row justify-between items-start md:items-center py-4 gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('myPolicies') }}"
                            class="w-10 h-10 rounded-xl bg-white border border-extra/50 flex items-center justify-center text-tertiary hover:text-accent hover:border-accent transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <h1 class="text-3xl font-extrabold text-quaternary">Adquirir Póliza</h1>
                    </div>
                    <p class="text-tertiary mt-1">Protege tu vehículo seleccionando el plan ideal para tus necesidades.</p>
                </div>
            </header>

            @if($uninsuredVehicles->isEmpty())
            <div class="bg-white/50 backdrop-blur-sm rounded-3xl p-12 text-center border-2 border-dashed border-extra/50 flex flex-col items-center gap-4">
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mb-2">
                    <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-quaternary mb-2">No hay vehículos sin póliza</h3>
                    <p class="text-tertiary max-w-md mx-auto">Para adquirir un seguro, necesitas tener al menos un vehículo registrado que no cuente con una póliza activa.</p>
                </div>
                <a href="{{ route('myVehicles') }}" class="bg-accent hover:bg-black text-white px-6 py-3 rounded-xl font-bold transition-colors shadow-sm mt-4">
                    Ir a Mis Vehículos
                </a>
            </div>
            @else

            <section class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-extra/30 relative overflow-hidden">
                <h2 class="text-xl font-bold text-quaternary mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-accent text-white flex items-center justify-center text-sm">1</span>
                    Selecciona tu Vehículo
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

                <form id="purchaseForm" action="{{ route('myPolicies.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="plan_id" id="input_plan_id">

                    <select name="vehicle_id" id="vehicle_select" class="w-full md:w-1/2 px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary font-bold block" required>
                        <option value="">— Múltiples disponibles, elige uno —</option>
                        @foreach($uninsuredVehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->vehicleModel->brand }} {{ $vehicle->vehicleModel->sub_brand }} ({{ $vehicle->vehicleModel->year }}) - {{ $vehicle->plate }}</option>
                        @endforeach
                    </select>
                </form>
            </section>

            <section id="plans_section" style="display: none;" class="space-y-6">
                <h2 class="text-xl font-bold text-quaternary mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-accent text-white flex items-center justify-center text-sm">2</span>
                    Elige el Nivel de Cobertura
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="plans_grid">
                </div>
            </section>

            <section id="confirmation_section" style="display: none;" class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-extra/30 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-32 h-32 bg-accent/5 rounded-br-[100px] -z-10"></div>

                <h2 class="text-xl font-bold text-quaternary mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-accent text-white flex items-center justify-center text-sm">3</span>
                    Confirmar Adquisición
                </h2>

                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex-1">
                        <div class="bg-secondary/10 p-6 rounded-2xl border border-extra/30">
                            <p class="text-tertiary font-semibold text-sm mb-2">Plan Seleccionado</p>
                            <h3 id="selected_plan_name" class="text-2xl font-extrabold text-quaternary mb-1">-</h3>
                            <p class="text-tertiary text-sm">Vigencia: 1 año desde la fecha de adquisición</p>
                        </div>
                        <p class="text-xs text-tertiary mt-4">Al confirmar aceptarás los <a href="#" class="text-accent underline hover:text-black">Términos y Condiciones</a>.</p>
                    </div>

                    <button type="submit" form="purchaseForm" class="bg-accent hover:bg-black text-white px-8 py-4 rounded-xl font-extrabold transition-colors shadow-md uppercase tracking-wide text-sm flex items-center gap-2 justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Adquirir Póliza
                    </button>
                </div>
            </section>
            @endif
        </div>

        <script>
            // Datos inyectados desde Laravel
            const plansData = @json($plansJSON);
            const dbPlans = @json($dbPlans);

            // Estado de la aplicación
            let state = {
                selectedVehicle: '',
                currentPlanName: null
            };

            document.addEventListener('DOMContentLoaded', function() {
                const vehicleSelect = document.getElementById('vehicle_select');
                const plansSection = document.getElementById('plans_section');
                const confirmationSection = document.getElementById('confirmation_section');
                const plansGrid = document.getElementById('plans_grid');
                const inputPlanId = document.getElementById('input_plan_id');
                const selectedPlanNameEl = document.getElementById('selected_plan_name');

                // 1. Manejar cambio de vehículo
                vehicleSelect.addEventListener('change', function() {
                    state.selectedVehicle = this.value;
                    plansSection.style.display = state.selectedVehicle ? 'block' : 'none';
                    if (!state.selectedVehicle) confirmationSection.style.display = 'none';
                    renderPlans();
                });

                // 2. Función para renderizar los planes
                function renderPlans() {
                    plansGrid.innerHTML = '';
                    plansData.forEach(p => {
                        const isSelected = state.currentPlanName === p.name;
                        const card = document.createElement('div');

                        card.className = `bg-white rounded-3xl p-6 border transition-all duration-300 relative flex flex-col cursor-pointer ${
                            isSelected ? 'border-accent ring-2 ring-accent/20 scale-[1.02] shadow-lg' : 'border-extra/30 hover:border-accent/40 hover:shadow-md'
                        }`;

                        // Generar lista de beneficios
                        const benefitsHtml = p.beneficios.map(bene => `
                            <li class="flex items-start gap-2 text-sm text-quaternary">
                                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>${bene}</span>
                            </li>
                        `).join('');

                        card.innerHTML = `
                            ${isSelected ? `
                                <div class="absolute -top-3 -right-3 w-8 h-8 bg-accent text-white rounded-full flex items-center justify-center shadow-sm z-10">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            ` : ''}
                            <h3 class="text-2xl font-extrabold text-quaternary mb-1">${p.name}</h3>
                            <p class="text-tertiary text-sm font-semibold mb-4 text-accent">Deducible <span>${p.deducible_danos}</span></p>
                            <ul class="space-y-3 mb-6 flex-1">${benefitsHtml}</ul>
                            <div class="pt-4 border-t border-extra/30">
                                <span class="text-lg font-bold text-quaternary">Cobertura Anual</span>
                            </div>
                        `;

                        card.onclick = () => selectPlan(p.name);
                        plansGrid.appendChild(card);
                    });
                }

                // 3. Seleccionar un plan
                function selectPlan(name) {
                    state.currentPlanName = name;
                    inputPlanId.value = dbPlans[name];
                    selectedPlanNameEl.textContent = name;

                    confirmationSection.style.display = 'block';
                    renderPlans(); // Re-renderizar para actualizar estilos de selección

                    // Scroll suave a la confirmación
                    confirmationSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            });
        </script>
    </x-slot>
</x-app-layout>