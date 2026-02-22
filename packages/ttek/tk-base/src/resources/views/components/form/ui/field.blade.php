@aware([
    'name',
    'label',
    'fieldCss',
    'help',
])

@props([
    // optional
    'preText' => '',
    'postText' => ''
])

@php
    $cleanName = str_replace(['[', ']'], '', $name);
    $fieldAttrs = new \Illuminate\View\ComponentAttributeBag(['class' => "tk-field tk-{$cleanName} mb-4"]);
    if (empty($label)) {
        $label = \Tk\Utils\Form::makeFieldLabel($name);
    }
@endphp

<div {{ $fieldAttrs->merge(['class' => $fieldCss]) }}>
    @if($label) <x-tk-base::form.ui.label for="fid_{{ $cleanName }}" :$label /> @endif

    {{ $slot }}

    @if($errors->has($name)) <x-tk-base::form.ui.error :message="$errors->first($name)" /> @endif
    @if($help) <x-tk-base::form.ui.help :$help /> @endif
</div>
