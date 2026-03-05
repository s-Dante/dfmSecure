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

<body class="">
    <header>
        <img src="{{ asset('/logos/DFM_SECURE_LOGO.png') }}" alt="Logotipo de DFM SECURE">
        <nav>
            <a href="{{ route('logIn') }}">
                <button>
                    Iniciar Sesion
                </button>
            </a>
            <a href="{{ route('signIn') }}">
                <button>
                    Registrarse
                </button>
            </a>
        </nav>
    </header>

    <main>
        <img src="" alt="Imagen inspiradora">

        <section>
            <h2>Nuestrios servicios</h2>
            <p>Protegemos lo que más importa para ti y tu familia.</p>

            <div class="">
                <div class="">
                    <p>Seguro de Auto</p>
                </div>

                <div class="">
                    <p>Seguro de Hogar</p>
                </div>

                <div class="">
                    <p>Seguro de Vida</p>
                </div>

                <div class="">
                    <p>Seguro de Viaje</p>
                </div>
            </div>
        </section>

        <section>
            <article>
                Plan 1
            </article>

            <article>
                Plan 2
            </article>

            <article>
                Plan 3
            </article>
        </section>
    </main>

    <footer>
    </footer>
</body>

</html>