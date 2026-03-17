{{-- Render common field markup --}}
@aware([
    'name',
    'label',
    'fieldCss',
    'help',
])
@props([
    // optional
    'preText'   => '',
    'postText'  => '',
    'errorText' => '',
    'withField' => true,
])

@php
    $cleanName = str_replace(['[', ']'], '', $name);
    $fieldAttrs = new \Illuminate\View\ComponentAttributeBag(['class' => "tk-field tk-{$cleanName} mb-4"]);
    if (empty($label)) {
        $label = \Tk\Utils\Form::makeFieldLabel($name);
    }
@endphp

@if ($withField === false)
    {{ $slot }}
@else
    <div {{ $fieldAttrs->merge(['class' => $fieldCss]) }}>
            @if($label) <x-tkl-ui::form.ui.label for="fid_{{ $cleanName }}" :$label /> @endif

            {{ $slot }}

            <x-tkl-ui::form.ui.error :message="$errors->first($name) ?: ($errorText ?: 'Please enter a valid value')" />
            @if($help) <x-tkl-ui::form.ui.help :$help /> @endif
    </div>
@endif
