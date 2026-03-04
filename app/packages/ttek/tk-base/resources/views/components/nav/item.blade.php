@props([
    // required
    'text',
    'href',
    // optional
    'visible' => true,
])

@if($visible)
    <li class="'nav-item'">
        <a {{ $attributes->merge([
            'class' => 'nav-link',
            'href' => $href,
        ]) }}>{{ $text }}</a>
    </li>
@endif
