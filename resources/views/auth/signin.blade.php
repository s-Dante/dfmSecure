@php
    $image = asset('imgs/auth/auth2.jpg');
    $title = 'Registrate en DFM Secure';
    $quote = \Illuminate\Foundation\Inspiring::quotes()->random();
@endphp

<x-auth-layout :image="$image" :title="$title" :quote="$quote">

    <x-slot name="content">
        <h2>Eres nuevo, registrate para asegurar tus propiedades</h2>
        <form action="{{ route('signIn') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Nombre">
            <input type="text" name="father_lastname" placeholder="Apellido Paterno">
            <input type="text" name="mother_lastname" placeholder="Apellido Materno">
            <input type="text" name="username" placeholder="Nombre de usuario">
            <input type="email" name="email" placeholder="Correo electrónico">
            <input type="password" name="password" placeholder="Contraseña">
            <input type="password" name="password_confirmation" placeholder="Confirmar contraseña">
            <input type="tel" name="phone" placeholder="Teléfono">
            <button type="submit">Registrarse</button>
            <p>¿Ya tienes una cuenta? <a href="{{ route('logIn') }}">Inicia sesión</a></p>
        </form>
    </x-slot>
</x-auth-layout>