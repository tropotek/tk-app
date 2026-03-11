@props([
    // required
    'text',
    'href',
    // optional
    'visible' => true,
])

@if($visible)
    <li>
        <a {{ $attributes->merge([
            'class' => 'dropdown-item',
            'href' => $href,
        ]) }}>{{ $text }}</a>
    </li>
@endif
