@php
    $image = asset('imgs/auth/auth2.jpg');
    $title = 'Regístrate';
    $quote = \Illuminate\Foundation\Inspiring::quotes()->random();

    $styles = [
        'header_container' => 'mb-5',
        'heading' => 'text-3xl font-extrabold text-quaternary mb-1',
        'subheading' => 'text-tertiary text-sm',
        'form' => 'space-y-3',
        'grid_2_cols' => 'grid grid-cols-1 sm:grid-cols-2 gap-3',
        'label' => 'block text-sm font-medium text-quaternary mb-1',
        'input' => 'w-full px-4 py-2 rounded-xl border border-extra focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all',
        'submit_btn' => 'w-full bg-accent hover:bg-[#7d9460] text-white font-bold py-3 px-4 rounded-full transition-all duration-300 transform hover:-translate-y-0.5 shadow-md mt-4',
        'footer_container' => 'mt-6 text-center border-t border-extra/50 pt-4',
        'footer_text' => 'text-sm text-tertiary',
        'footer_link' => 'font-bold text-accent hover:text-[#7d9460] transition-colors ml-1'
    ];
@endphp

<x-auth-layout :image="$image" :title="$title" :quote="$quote">

    <x-slot name="content">
        <div class="{{ $styles['header_container'] }}">
            <h2 class="{{ $styles['heading'] }}">Crear una cuenta</h2>
            <p class="{{ $styles['subheading'] }}">Ingresa tus datos para asegurar tus propiedades.</p>
        </div>

        <form action="{{ route('signIn') }}" method="POST" class="{{ $styles['form'] }}">
            @csrf

            <div class="{{ $styles['grid_2_cols'] }}">
                <div>
                    <label for="name" class="{{ $styles['label'] }}">Nombre</label>
                    <input type="text" name="name" id="name" class="{{ $styles['input'] }}" placeholder="Tu nombre"
                        required>
                </div>
                <div>
                    <label for="username" class="{{ $styles['label'] }}">Usuario</label>
                    <input type="text" name="username" id="username" class="{{ $styles['input'] }}"
                        placeholder="mi_usuario" required>
                </div>
            </div>

            <div class="{{ $styles['grid_2_cols'] }}">
                <div>
                    <label for="father_lastname" class="{{ $styles['label'] }}">Apellido Paterno</label>
                    <input type="text" name="father_lastname" id="father_lastname" class="{{ $styles['input'] }}"
                        placeholder="Pérez" required>
                </div>
                <div>
                    <label for="mother_lastname" class="{{ $styles['label'] }}">Apellido Materno</label>
                    <input type="text" name="mother_lastname" id="mother_lastname" class="{{ $styles['input'] }}"
                        placeholder="García" required>
                </div>
            </div>

            <div class="{{ $styles['grid_2_cols'] }}">
                <div>
                    <label for="email" class="{{ $styles['label'] }}">Correo electrónico</label>
                    <input type="email" name="email" id="email" class="{{ $styles['input'] }}"
                        placeholder="tu@correo.com" required>
                </div>
                <div>
                    <label for="phone" class="{{ $styles['label'] }}">Teléfono</label>
                    <input type="tel" name="phone" id="phone" class="{{ $styles['input'] }}" placeholder="55 1234 5678"
                        required>
                </div>
            </div>

            <div class="{{ $styles['grid_2_cols'] }}">
                <div>
                    <label for="password" class="{{ $styles['label'] }}">Contraseña</label>
                    <input type="password" name="password" id="password" class="{{ $styles['input'] }}"
                        placeholder="••••••••" required>
                </div>
                <div>
                    <label for="password_confirmation" class="{{ $styles['label'] }}">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="{{ $styles['input'] }}" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="{{ $styles['submit_btn'] }}">
                Crear cuenta
            </button>
        </form>

        <div class="{{ $styles['footer_container'] }}">
            <p class="{{ $styles['footer_text'] }}">
                ¿Ya tienes una cuenta?
                <a href="{{ route('logIn') }}" class="{{ $styles['footer_link'] }}">Inicia sesión</a>
            </p>
        </div>
    </x-slot>
</x-auth-layout>