@props(['allName', 'email', 'phone', 'address', 'url'])

<div>
    <p>{{ $allName }}</p>
    <p>{{ $email }}</p>
    <p>{{ $phone }}</p>
    <p>{{ $address }}</p>
    <a href="{{ $url }}">
        <button>Editar</button>
    </a>
</div>