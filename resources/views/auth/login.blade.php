@php
    $image = asset('imgs/auth/auth1.jpg');
    $title = 'Bienvenido a DFM Secure';
    $quote = \Illuminate\Foundation\Inspiring::quotes()->random();
@endphp

<x-auth-layout :image="$image" :title="$title" :quote="$quote">

    <x-slot name="content">
        <h2>Bienvenido, inicia sesión para continuar</h2>
        <form action="{{ route('logIn') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Correo electrónico">
            <a href="{{ route('resetPassword') }}">¿Olvidaste tu contraseña?</a>
            <input type="password" name="password" placeholder="Contraseña">
            <button type="submit">Iniciar sesión</button>
            <p>¿No tienes una cuenta? <a href="{{ route('signIn') }}">Registrate</a></p>
        </form>
    </x-slot>
</x-auth-layout>