@aware(['mode', 'values'])
@props([
    'label',
])
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn']) }}>
    <span>{{ $label }}</span>
</button>
