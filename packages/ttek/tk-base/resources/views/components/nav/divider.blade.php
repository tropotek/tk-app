@props([
    // optional
    'visible' => true,
])

@if($visible)
    <li class="{{ $attributes }}"><hr class="dropdown-divider"/></li>
@endif
