@php
    $image = asset('imgs/auth/auth5.jpeg');
    $title = 'Restablece tu contraseña';
    $quote = \Illuminate\Foundation\Inspiring::quotes()->random();
@endphp

<x-auth-layout :image="$image" :title="$title" :quote="$quote">

    <x-slot name="content">
        <h2>Ingresa tu nueva contraseña</h2>
        <p>Ingresa tu nueva contraseña para restablecerla</p>
        <form action="{{ route('verifyEmail') }}" method="POST">
            @csrf
            <input type="password" name="password" placeholder="Contraseña">
            <input type="password" name="password_confirmation" placeholder="Confirmar contraseña">
            <button type="submit">Restablecer</button>
        </form>
    </x-slot>
</x-auth-layout>