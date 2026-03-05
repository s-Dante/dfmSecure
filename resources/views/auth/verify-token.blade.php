@php
    $image = asset('imgs/auth/auth4.jpg');
    $title = 'Validar Token';
    $quote = \Illuminate\Foundation\Inspiring::quotes()->random();

    $styles = [
        'header_container' => 'mb-8',
        'heading' => 'text-3xl font-extrabold text-quaternary mb-2',
        'subheading' => 'text-tertiary',
        'form' => 'space-y-6',
        'label' => 'block text-sm font-medium text-quaternary mb-1',
        'input' => 'w-full px-4 py-3 rounded-xl border border-extra focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-center tracking-widest font-mono text-lg',
        'submit_btn' => 'w-full bg-accent hover:bg-[#7d9460] text-white font-bold py-3 px-4 rounded-full transition-all duration-300 transform hover:-translate-y-0.5 shadow-md',
        'footer_container' => 'mt-8 text-center pt-6',
        'footer_text' => 'text-sm text-tertiary',
        'footer_link' => 'font-bold text-accent hover:text-[#7d9460] transition-colors ml-1'
    ];
@endphp

<x-auth-layout :image="$image" :title="$title" :quote="$quote">

    <x-slot name="content">
        <div class="{{ $styles['header_container'] }}">
            <h2 class="{{ $styles['heading'] }}">Ingresa tu código</h2>
            <p class="{{ $styles['subheading'] }}">Hemos enviado un token de verificación a tu correo electrónico.
                Revisa tu bandeja de entrada o spam.</p>
        </div>

        <form action="{{ route('verifyEmail') }}" method="POST" class="{{ $styles['form'] }}">
            @csrf

            <div>
                <label for="token" class="{{ $styles['label'] }}">Código de Seguridad (Token)</label>
                <input type="text" name="token" id="token" class="{{ $styles['input'] }}" placeholder="XXXXXX" required>
            </div>

            <button type="submit" class="{{ $styles['submit_btn'] }}">
                Validar Token
            </button>
        </form>

        <div class="{{ $styles['footer_container'] }}">
            <p class="{{ $styles['footer_text'] }}">
                ¿No recibiste el correo?
                <a href="{{ route('verifyEmail') }}" class="{{ $styles['footer_link'] }}">Reenviar código</a>
            </p>
        </div>
    </x-slot>
</x-auth-layout>