<x-app-layout>
    <x-slot name="content">
        <!-- Main container with Alpine data -->
        <div class="w-full max-w-7xl mx-auto space-y-10 pb-12" 
            x-data="{ 
                selectedVehicle: '',
                selectedPlanId: '',
                selectedPeriod: 'anual',
                plans: {{ json_encode($plansJson) }},
                dbPlans: {{ json_encode($dbPlans->mapWithKeys(fn($p) => [$p->name => $p->id])) }},
                currentPlanName: null,
                
                get currentPlan() {
                    if (!this.currentPlanName) return null;
                    return this.plans.find(p => p.name === this.currentPlanName);
                },
                
                get dbId() {
                    return this.currentPlanName ? this.dbPlans[this.currentPlanName] : '';
                },

                selectPlan(name) {
                    this.currentPlanName = name;
                    this.selectedPlanId = this.dbPlans[name];
                }
            }">

            <!-- Encabezado -->
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
                        <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
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

            <!-- Seleccionar Vehículo -->
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
                    <input type="hidden" name="plan_id" x-model="selectedPlanId">
                    <input type="hidden" name="payment_period" x-model="selectedPeriod">

                    <select name="vehicle_id" x-model="selectedVehicle" class="w-full md:w-1/2 px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary font-bold block" required>
                        <option value="">— Múltiples disponibles, elige uno —</option>
                        @foreach($uninsuredVehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->vehicleModel->brand }} {{ $vehicle->vehicleModel->sub_brand }} ({{ $vehicle->vehicleModel->year }}) - {{ $vehicle->plate }}</option>
                        @endforeach
                    </select>

                </form>
            </section>

            <!-- Seleccionar Plan (Pricing Grid) -->
            <section x-show="selectedVehicle" class="space-y-6" x-transition x-cloak>
                <h2 class="text-xl font-bold text-quaternary mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-accent text-white flex items-center justify-center text-sm">2</span>
                    Elige el Nivel de Cobertura
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="p in plans" :key="p.name">
                        <!-- Plan Card -->
                        <div @click="selectPlan(p.name)"
                             :class="currentPlanName === p.name ? 'border-accent ring-2 ring-accent/20 scale-[1.02] shadow-lg' : 'border-extra/30 hover:border-accent/40 hover:shadow-md cursor-pointer'"
                             class="bg-white rounded-3xl p-6 border transition-all duration-300 relative flex flex-col">
                             
                            <!-- Check Seleccionado -->
                            <div x-show="currentPlanName === p.name" class="absolute -top-3 -right-3 w-8 h-8 bg-accent text-white rounded-full flex items-center justify-center shadow-sm z-10" x-transition>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>

                            <h3 class="text-2xl font-extrabold text-quaternary mb-1" x-text="p.name"></h3>
                            <p class="text-tertiary text-sm font-semibold mb-4 text-accent">Deducible <span x-text="p.deducible_danos"></span></p>

                            <ul class="space-y-3 mb-6 flex-1">
                                <template x-for="bene in p.beneficios" :key="bene">
                                    <li class="flex items-start gap-2 text-sm text-quaternary">
                                        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span x-text="bene"></span>
                                    </li>
                                </template>
                            </ul>

                            <div class="pt-4 border-t border-extra/30 flex items-end gap-1">
                                <span class="text-3xl font-extrabold text-quaternary" x-text="'$'+p.costo.anual.toLocaleString('es-MX')"></span>
                                <span class="text-tertiary font-semibold text-sm mb-1 line-through" x-show="p.name === 'Plus' || p.name === 'Completo'">$12,000</span>
                                <span class="text-tertiary font-semibold text-sm mb-1 ml-auto">/ año</span>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <!-- Confirmación de Compra -->
            <section x-show="currentPlanName" class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-extra/30 relative overflow-hidden" x-transition x-cloak>
                <div class="absolute top-0 left-0 w-32 h-32 bg-accent/5 rounded-br-[100px] -z-10"></div>
                
                <h2 class="text-xl font-bold text-quaternary mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-accent text-white flex items-center justify-center text-sm">3</span>
                    Método de Pago y Confirmación
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div>
                        <label class="text-sm font-semibold text-tertiary mb-1.5 block">Selecciona Periodo de Pago</label>
                        <select x-model="selectedPeriod" class="w-full px-4 py-3 bg-secondary/10 rounded-xl border border-extra/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all text-quaternary font-bold block mb-4">
                            <option value="anual">Anual (Pago Único)</option>
                            <option value="semestral">Semestral</option>
                            <option value="trimestral">Trimestral</option>
                            <option value="bimestral">Bimestral</option>
                        </select>
                        <p class="text-xs text-tertiary">Al confirmar y cobrar aceptará los <a href="#" class="text-accent underline hover:text-black">Términos y Condiciones</a>.</p>
                    </div>

                    <div class="bg-secondary/10 p-6 rounded-2xl border border-extra/30 text-right">
                        <p class="text-tertiary font-bold text-sm uppercase tracking-wider mb-1">Total a Pagar ahora</p>
                        <div class="text-4xl font-extrabold text-quaternary mb-4 drop-shadow-sm" x-text="currentPlan ? '$' + currentPlan.costo[selectedPeriod].toLocaleString('es-MX') : '$0'"></div>
                        
                        <button type="submit" form="purchaseForm" class="bg-accent hover:bg-black text-white px-8 py-4 rounded-xl font-extrabold transition-colors shadow-md w-full md:w-auto uppercase tracking-wide text-sm flex items-center gap-2 justify-center ml-auto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Confirmar y Pagar
                        </button>
                    </div>
                </div>
            </section>

            @endif
        </div>
    </x-slot>
</x-app-layout>
