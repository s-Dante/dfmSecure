@php
    $styles = [
        'body' => 'font-sans antialiased bg-primary text-text-dark selection:bg-accent selection:text-white lg:overflow-hidden',
        'main' => 'min-h-screen lg:h-screen flex',

        // Left Side
        'left_section' => 'hidden lg:flex lg:w-1/2 relative bg-quaternary overflow-hidden items-end p-12',
        'bg_image' => 'absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-80',
        'bg_overlay' => 'absolute inset-0 bg-gradient-to-t from-quaternary/50 to-transparent z-0',

        'logo_container' => 'absolute top-12 left-12 z-20',
        'logo_bg' => 'bg-white/90 p-3 rounded-2xl shadow-lg',
        'logo_img' => 'h-8 w-auto object-contain',

        'quote_container' => 'relative z-10 w-full max-w-lg mb-8',
        'blockquote' => 'text-white',
        'quote_text' => 'text-3xl font-medium leading-tight mb-4',
        'quote_author' => 'text-secondary font-light text-lg tracking-wide border-t border-white/20 pt-4',

        'decorative_left' => 'absolute top-1/2 left-12 w-24 h-24 bg-accent/20 rounded-full blur-2xl z-0',

        // Right Side
        // Removed lg:overflow-y-auto to prevent scrolling as requested
        'right_section' => 'w-full lg:w-1/2 flex flex-col justify-center items-center p-6 sm:p-8 lg:p-12 bg-white lg:bg-primary/50 relative overflow-hidden',
        'form_container' => 'w-full max-w-xl bg-white p-8 sm:p-10 rounded-3xl shadow-xl shadow-quaternary/5 border border-extra/50 relative z-10',

        'decorative_top_right' => 'absolute top-0 right-0 -m-32 w-96 h-96 bg-accent opacity-[0.03] rounded-full blur-3xl pointer-events-none z-0',
        'decorative_bottom_left' => 'absolute bottom-0 left-0 -m-32 w-96 h-96 bg-tertiary opacity-[0.03] rounded-full blur-3xl pointer-events-none z-0',
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' | ' . config('app.name', 'Laravel') : config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head-scripts')
</head>

<body class="{{ $styles['body'] }}">
    <!-- lg:h-screen prevents scroll on desktop, allowing the layout to fit within the viewport -->
    <main class="{{ $styles['main'] }}">

        <!-- Left Side: Image & Quote (Hidden on small screens) -->
        <section class="{{ $styles['left_section'] }}">
            <!-- Background Image -->
            <img src="{{ $image ?? asset('imgs/auth/auth1.jpg') }}" alt="Imagen {{ $title ?? 'Autenticación' }}"
                class="{{ $styles['bg_image'] }}">

            <!-- Dark Overlay for better text readability -->
            <div class="{{ $styles['bg_overlay'] }}"></div>

            <!-- Logo Moved to Image Side -->
            <div class="{{ $styles['logo_container'] }}">
                <!-- Se asume que el logo claro contrasta con la imagen oscura. Si el logo original es oscuro, se puede agregar clase bg-white/80 p-2 rounded-xl -->
                <div class="{{ $styles['logo_bg'] }}">
                    <img src="{{ asset('/logos/DFM_SECURE_IMG.png') }}" alt="DFM SECURE"
                        class="{{ $styles['logo_img'] }}">
                </div>
            </div>

            <!-- Quote Container -->
            <div class="{{ $styles['quote_container'] }}">
                <blockquote class="{{ $styles['blockquote'] }}">
                    @php
                        $rawQuote = $quote ?? 'Viaja seguro - DFM Secure';
                        $quoteText = Str::beforeLast($rawQuote, ' - ');
                        $quoteAuthor = Str::afterLast($rawQuote, ' - ');

                        if ($quoteText === $quoteAuthor) {
                            $quoteAuthor = 'Anónimo';
                        }
                    @endphp
                    <p class="{{ $styles['quote_text'] }}">
                        "{{ $quoteText }}"
                    </p>
                    <footer class="{{ $styles['quote_author'] }}">
                        - {{ $quoteAuthor }}
                    </footer>
                </blockquote>
            </div>

            <!-- Floating Decorative Element -->
            <div class="{{ $styles['decorative_left'] }}"></div>
        </section>

        <!-- Right Side: Content / Form Area -->
        <section class="{{ $styles['right_section'] }}">
            <!-- Widen form container to max-w-xl (was max-w-md) -->
            <div class="{{ $styles['form_container'] }}">
                {{ $content }}
            </div>

            <!-- Background subtle pattern/shapes for purely white screens -->
            <div class="{{ $styles['decorative_top_right'] }}">
            </div>
            <div class="{{ $styles['decorative_bottom_left'] }}">
            </div>
        </section>

    </main>
</body>

</html>