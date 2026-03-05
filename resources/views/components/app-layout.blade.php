@php
    $styles = [
        'body' => 'font-sans antialiased text-text-dark bg-primary h-screen w-full overflow-hidden flex selection:bg-accent selection:text-white',
        'main_content' => 'flex-1 flex flex-col h-full overflow-hidden',
        'scrollable_area' => 'flex-1 overflow-y-auto p-6 lg:p-10',
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head-scripts')
</head>

<body class="{{ $styles['body'] }}">
    <!-- Sidebar Navigation -->
    <x-navigation-bar />

    <!-- Main Content Area -->
    <div class="{{ $styles['main_content'] }}">

        <!-- Header could go here if needed in the future -->

        <!-- Scrollable content -->
        <main class="{{ $styles['scrollable_area'] }}">
            {{ $content ?? $slot ?? '' }}
        </main>

    </div>

    @stack('scripts')
</body>

</html>