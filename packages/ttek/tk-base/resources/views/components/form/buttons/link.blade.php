@aware(['mode', 'values'])
@props([
    'label',
])
<a {{ $attributes->merge(['class' => 'btn']) }}>
    <span>{{ $label }}</span>
</a>
