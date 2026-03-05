@props(['image', 'alt', 'folio', 'vehicle', 'status', 'url'])

<article>
    <img src="{{ $image }}" alt="{{ $alt }}">
    <p>{{ $folio }}</p>
    <span>{{ $vehicle }}</span>
    <span>{{ $status }}</span>
    <a href="{{ $url }}">
        <button>Ver</button>
    </a>
</article>