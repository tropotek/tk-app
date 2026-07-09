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
    // the error-bag key to check, if it differs from $name (eg. a Livewire
    // Form object binds "site_title" as "form.site_title" in the error bag)
    'errorKey'  => null,
])

@php
    $cleanName = str_replace(['[', ']'], '', $name);
    $errorKey = $errorKey ?? $name;
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

            @if ($errors->has($errorKey))
                <x-tkl-ui::form.ui.error :message="$errors->first($errorKey) ?: ($errorText ?: 'Please enter a valid value')" />
            @endif
            @if($help) <x-tkl-ui::form.ui.help :$help /> @endif
    </div>
@endif
