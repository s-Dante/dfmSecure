@php
    $image = asset('imgs/auth/auth4.jpg');
    $title = 'Recupera tu cuenta';
    $quote = \Illuminate\Foundation\Inspiring::quotes()->random();
@endphp

<x-auth-layout :image="$image" :title="$title" :quote="$quote">

    <x-slot name="content">
        <h2>Ingresa el token que te enviamos a tu correo electrónico</h2>
        <p>Si no has recibido el correo electrónico, por favor revisa tu carpeta de spam</p>
        <form action="{{ route('verifyEmail') }}" method="POST">
            @csrf
            <input type="text" name="token" placeholder="Token">
            <button type="submit">Validar</button>
        </form>
        <p>¿No has recibido el correo electrónico? <a href="{{ route('verifyEmail') }}">Reenviar correo</a></p>
    </x-slot>
</x-auth-layout>