@php
    $image = asset('imgs/auth/auth3.jpg');
    $title = 'Recupera tu cuenta';
    $quote = \Illuminate\Foundation\Inspiring::quotes()->random();
@endphp

<x-auth-layout :image="$image" :title="$title" :quote="$quote">

    <x-slot name="content">
        <h2>Ingresa tu correo electrónico para recuperar tu cuenta</h2>
        <p>Te enviaremos un correo electrónico con un token para verificar tu cuenta</p>
        <form action="{{ route('verifyEmail') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Correo electrónico">
            <button type="submit">Validar</button>
            <p>¿Ya tienes una cuenta? <a href="{{ route('logIn') }}">Inicia sesión</a></p>
        </form>
    </x-slot>
</x-auth-layout>