<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

@php
$plansData = json_decode(file_get_contents(database_path('data/plans.json')), true);

$styles = [
'body' => 'bg-primary text-text-dark font-sans antialiased selection:bg-accent selection:text-white',

// Header
'header' => 'w-full bg-white shadow-sm sticky top-0 z-50',
'header_container' => 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
'header_flex' => 'flex justify-between items-center h-20',
'logo_img_mobile' => 'h-10 w-auto object-contain md:hidden',
'logo_img_desktop' => 'hidden md:block h-16 lg:h-40 w-auto object-contain',
'nav' => 'flex space-x-2 md:space-x-6 items-center',
'btn_ghost' => 'text-quaternary font-medium hover:text-accent transition-colors duration-200 flex items-center justify-center p-2 md:p-0',
'btn_primary' => 'bg-accent hover:bg-[#7d9460] text-white font-medium py-2 px-3 md:px-6 rounded-full transition-all duration-300 transform hover:-translate-y-0.5 shadow-md flex items-center justify-center',

// Hero
'hero_section' => 'relative bg-quaternary text-white overflow-hidden max-w-7xl mx-auto rounded-3xl mt-6 sm:mt-8 shadow-xl',
'hero_overlay' => 'absolute inset-0 bg-gradient-to-l from-quaternary/40 via-quaternary/20 to-transparent z-10',
'hero_img' => 'absolute inset-0 w-full h-full object-cover mix-blend-overlay',
'hero_content' => 'relative z-20 px-8 sm:px-12 lg:px-20 py-24 sm:py-32 lg:py-48 flex flex-col justify-center items-end text-right ml-auto w-full md:w-3/4 lg:w-2/3',
'hero_h1' => 'text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight mb-4 sm:mb-6',
'hero_accent' => 'text-accent',
'hero_p' => 'mt-4 text-lg sm:text-xl md:text-2xl text-secondary mb-8 sm:mb-10 font-light',
'hero_buttons' => 'flex flex-col sm:flex-row gap-4 justify-end w-full sm:w-auto',
'btn_hero_primary' => 'bg-accent hover:bg-[#7d9460] text-white font-bold py-3 px-8 rounded-full transition-all duration-300 transform hover:-translate-y-1 shadow-lg text-lg text-center',
'btn_hero_secondary' => 'bg-transparent border-2 border-white hover:bg-white hover:text-quaternary text-white font-bold py-3 px-8 rounded-full transition-all duration-300 text-lg text-center',

// General Sections
'section_bg' => 'py-20 sm:py-24 bg-primary',
'container' => 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
'section_header' => 'text-center mb-16',
'section_subtitle' => 'text-sm font-bold text-accent uppercase tracking-wider mb-2',
'section_title' => 'text-3xl md:text-4xl font-extrabold text-quaternary',
'section_desc' => 'mt-4 max-w-2xl mx-auto text-lg text-tertiary',

// Services
'services_grid' => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6 xl:gap-10 mt-12',
'service_card' => 'bg-white rounded-2xl shadow-sm border border-extra p-6 sm:p-8 text-center hover:shadow-xl transition-shadow duration-300 mt-6 sm:mt-0',
'service_icon_wrap' => 'w-16 h-16 bg-gradient-to-br from-white to-gray-200 -mt-12 sm:-mt-14 mx-auto rounded-full flex items-center justify-center shadow-md mb-6 border border-extra',
'service_icon' => 'w-8 h-8 text-tertiary',
'service_h4' => 'text-lg sm:text-xl font-bold text-quaternary mb-3',
'service_p' => 'text-tertiary text-sm leading-relaxed',

// Plans
'plans_bg' => 'py-20 sm:py-24 bg-white border-t border-extra',
'plans_grid' => 'grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto',
'plan_card' => 'bg-primary rounded-3xl p-8 flex flex-col shadow-sm border border-extra transition-transform hover:-translate-y-1 relative',
'plan_card_featured' => 'bg-gradient-to-br from-[#243350] to-quaternary text-white rounded-3xl p-8 flex flex-col shadow-lg border border-accent/30 transition-transform hover:-translate-y-1 relative transform md:-translate-y-4 md:hover:-translate-y-5',
'plan_badge' => 'absolute -top-4 left-1/2 transform -translate-x-1/2 bg-accent text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wide shadow-md whitespace-nowrap',
'plan_h3' => 'text-2xl font-bold text-quaternary mb-2 text-center',
'plan_h3_featured' => 'text-2xl font-bold text-white mb-2 text-center',
'plan_deductible_box' => 'text-center my-6 py-4 border-y border-extra/50',
'plan_deductible_box_featured' => 'text-center my-6 py-4 border-y border-white/10',
'plan_deductible_label' => 'text-xs font-semibold uppercase tracking-wider text-tertiary mb-1',
'plan_deductible_label_featured' => 'text-xs font-semibold uppercase tracking-wider text-secondary mb-1',
'plan_deductible' => 'text-4xl font-extrabold font-sans text-quaternary',
'plan_deductible_featured' => 'text-4xl font-extrabold font-sans text-white',
'plan_desc' => 'text-tertiary text-sm text-center mb-8',
'plan_desc_featured' => 'text-secondary text-sm text-center mb-8',
'plan_list' => 'space-y-4 mb-8 flex-grow',
'plan_li' => 'flex items-start text-quaternary text-sm',
'plan_li_featured' => 'flex items-start text-white text-sm',
'plan_icon_list' => 'w-5 h-5 text-accent mr-3 flex-shrink-0 mt-0.5',
'plan_btn' => 'w-full bg-white border border-extra text-quaternary font-bold py-3 px-6 rounded-full hover:bg-gray-50 transition-colors mt-auto text-center',
'plan_btn_featured' => 'w-full bg-accent hover:bg-[#7d9460] text-white font-bold py-3 px-6 rounded-full transition-colors shadow-md mt-auto text-center',

// Footer
'footer' => 'bg-quaternary py-12 border-t border-tertiary/30',
'footer_container' => 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center',
'footer_logo' => 'h-10 w-auto opacity-70 mb-6 grayscale hover:grayscale-0 transition-all duration-300',
'footer_text' => 'text-tertiary text-sm text-center',
];
@endphp

<body class="{{ $styles['body'] }}">
    <!-- Header Navigation -->
    <header class="{{ $styles['header'] }}">
        <div class="{{ $styles['header_container'] }}">
            <div class="{{ $styles['header_flex'] }}">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <img class="{{ $styles['logo_img_mobile'] }}" src="{{ asset('/logos/DFM_SECURE_IMG.png') }}" alt="DFM SECURE">
                    <img class="{{ $styles['logo_img_desktop'] }}" src="{{ asset('/logos/DFM_SECURE_LOGO.png') }}" alt="DFM SECURE">
                </div>

                <!-- Navigation -->
                <nav class="{{ $styles['nav'] }}">
                    <a href="{{ route('logIn') }}" class="{{ $styles['btn_ghost'] }}" title="Iniciar Sesión">
                        <svg class="w-6 h-6 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="hidden md:inline">Iniciar Sesión</span>
                    </a>
                    <a href="{{ route('signIn') }}" class="{{ $styles['btn_primary'] }}" title="Registrarse">
                        <svg class="w-6 h-6 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        <span class="hidden md:inline">Registrarse</span>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <section class="{{ $styles['hero_section'] }}">
            <div class="{{ $styles['hero_overlay'] }}"></div>

            <img src="{{ asset('/imgs/landingPage/heroImg.png') }}" alt="DFM Secure" class="{{ $styles['hero_img'] }}">

            <div class="{{ $styles['hero_content'] }}">
                <h1 class="{{ $styles['hero_h1'] }}">
                    Protege tu camino,<br />
                    <span class="{{ $styles['hero_accent'] }}">Asegura tu tranquilidad</span>
                </h1>
                <p class="{{ $styles['hero_p'] }}">
                    Cobertura inteligente y confiable para tu automóvil. Viaja seguro sabiendo que estamos contigo en
                    cada kilómetro.
                </p>
                <div class="{{ $styles['hero_buttons'] }}">
                    <a href="#planes" class="{{ $styles['btn_hero_primary'] }}">
                        Ver Planes
                    </a>
                    <a href="#services" class="{{ $styles['btn_hero_secondary'] }}">
                        Saber más
                    </a>
                </div>
            </div>
        </section>

        <section id="services" class="{{ $styles['section_bg'] }}">
            <div class="{{ $styles['container'] }}">
                <div class="{{ $styles['section_header'] }}">
                    <h2 class="{{ $styles['section_subtitle'] }}">Por qué elegirnos</h2>
                    <h3 class="{{ $styles['section_title'] }}">Nuestros Servicios Destacados</h3>
                    <p class="{{ $styles['section_desc'] }}">
                        Diseñamos coberturas pensando en tus necesidades reales, ofreciendo asistencia rápida y efectiva
                        cuando más la necesitas.
                    </p>
                </div>

                <div class="{{ $styles['services_grid'] }}">
                    <!-- Service 1 -->
                    <div class="{{ $styles['service_card'] }}">
                        <div class="{{ $styles['service_icon_wrap'] }}">
                            <svg class="{{ $styles['service_icon'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="{{ $styles['service_h4'] }}">Asistencia 24/7</h4>
                        <p class="{{ $styles['service_p'] }}">Reporte de siniestros y apoyo vial a cualquier hora del
                            día, todos los días del año.</p>
                    </div>

                    <!-- Service 2 -->
                    <div class="{{ $styles['service_card'] }}">
                        <div class="{{ $styles['service_icon_wrap'] }}">
                            <svg class="{{ $styles['service_icon'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="{{ $styles['service_h4'] }}">Protección Total</h4>
                        <p class="{{ $styles['service_p'] }}">Cobertura contra robos, daños materiales y responsabilidad
                            civil para viajar sin preocupaciones.</p>
                    </div>

                    <!-- Service 3 -->
                    <div class="{{ $styles['service_card'] }}">
                        <div class="{{ $styles['service_icon_wrap'] }}">
                            <svg class="{{ $styles['service_icon'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h4 class="{{ $styles['service_h4'] }}">Respuesta Rápida</h4>
                        <p class="{{ $styles['service_p'] }}">Gestión de siniestros ágil a través de nuestra plataforma,
                            para que vuelvas al camino pronto.</p>
                    </div>

                    <!-- Service 4 (New) -->
                    <div class="{{ $styles['service_card'] }}">
                        <div class="{{ $styles['service_icon_wrap'] }}">
                            <svg class="{{ $styles['service_icon'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3">
                                </path>
                            </svg>
                        </div>
                        <h4 class="{{ $styles['service_h4'] }}">Asesoría Legal</h4>
                        <p class="{{ $styles['service_p'] }}">Abogados expertos listos para representarte y defender tus
                            intereses en caso de percance.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Plans Section -->
        <section id="planes" class="{{ $styles['plans_bg'] }}">
            <div class="{{ $styles['container'] }}">
                <div class="{{ $styles['section_header'] }}">
                    <h2 class="{{ $styles['section_title'] }}">Explora Nuestros Planes</h2>
                    <p class="{{ $styles['section_desc'] }}">
                        Encuentra la cobertura perfecta que se adapte a tu estilo de vida y al modelo de tu coche.
                    </p>
                </div>

                <div class="{{ $styles['plans_grid'] }}">
                    @foreach ($plansData as $plan)
                    @php
                    // Determine if this is the featured plan ('Plus' or according to design needs)
                    $isFeatured = $plan['name'] === 'Plus';

                    $cardClass = $isFeatured ? $styles['plan_card_featured'] : $styles['plan_card'];
                    $h3Class = $isFeatured ? $styles['plan_h3_featured'] : $styles['plan_h3'];
                    $descClass = $isFeatured ? $styles['plan_desc_featured'] : $styles['plan_desc'];
                    $deductibleBoxClass = $isFeatured ? $styles['plan_deductible_box_featured'] : $styles['plan_deductible_box'];
                    $deductibleLabelClass = $isFeatured ? $styles['plan_deductible_label_featured'] : $styles['plan_deductible_label'];
                    $deductibleClass = $isFeatured ? $styles['plan_deductible_featured'] : $styles['plan_deductible'];
                    $liClass = $isFeatured ? $styles['plan_li_featured'] : $styles['plan_li'];
                    $btnClass = $isFeatured ? $styles['plan_btn_featured'] : $styles['plan_btn'];

                    $descriptions = [
                    'Básico' => 'La protección legal e indispensable para circular.',
                    'Plus' => 'Protección equilibrada y recomendada para tu patrimonio.',
                    'Amplio' => 'La cobertura total para máxima tranquilidad.'
                    ];
                    $desc = $descriptions[$plan['name']] ?? 'La mejor cobertura para ti.';
                    @endphp

                    <div class="{{ $cardClass }}">
                        @if($isFeatured)
                        <div class="{{ $styles['plan_badge'] }}">Más Popular</div>
                        @endif
                        <h3 class="{{ $h3Class }}">{{ $plan['name'] }}</h3>
                        <p class="{{ $descClass }}">{{ $desc }}</p>

                        <div class="{{ $deductibleBoxClass }}">
                            <div class="{{ $deductibleLabelClass }}">Deducible Daños</div>
                            <div class="{{ $deductibleClass }}">{{ $plan['deducible_danos'] }}</div>
                        </div>

                        <ul class="{{ $styles['plan_list'] }}">
                            @foreach($plan['beneficios'] as $benefit)
                            <li class="{{ $liClass }}">
                                <svg class="{{ $styles['plan_icon_list'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ $benefit }}</span>
                            </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('signIn') }}" class="{{ $btnClass }}">Seleccionar {{ $plan['name'] }}</a>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="{{ $styles['footer'] }}">
        <div class="{{ $styles['footer_container'] }}">
            <img class="{{ $styles['footer_logo'] }}" src="{{ asset('/logos/DFM_SECURE_LOGO.png') }}" alt="DFM SECURE">
            <p class="{{ $styles['footer_text'] }}">
                &copy; {{ date('Y') }} DFM SECURE. Todos los derechos reservados.<br>
                Tranquilidad para tu camino.
            </p>
        </div>
    </footer>
</body>

</html>