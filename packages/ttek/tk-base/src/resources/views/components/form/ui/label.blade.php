{{-- Show a label for a field --}}
@props([
    // required
    'label' => ''
])
<label {{ $attributes->merge([ 'class' => 'form-label mb-1' ]) }}>{{ $label }}</label>
