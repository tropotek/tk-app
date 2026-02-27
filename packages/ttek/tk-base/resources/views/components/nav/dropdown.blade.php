@props([
    // required
    'text',
    // optional
    'visible' => true,
])

@if($visible)
    <li class="nav-item dropdown">
        <a {{ $attributes->merge([
            'class' => 'nav-link dropdown-toggle',
            'href' => '',
            'role' => 'button',
            'data-bs-toggle' => 'dropdown',
        ]) }}>{{ $text }}</a>
        <ul class="dropdown-menu">
            {{ $slot }}
        </ul>
    </li>
@endif
