@aware(['mode' => 'view', 'values' => []])
@props([
    // required
    'name' => '',
    // optional
    'label' => '',
    'default' => '',
    'fieldCss' => '',
    'help' => ''
])
@php
    $cleanName = str_replace(['[', ']'], '', $name);
    $value = $values[$name] ?? $default;

    // Help message
    $preText = '';
    $maxBytes = \Tk\Utils\File::getMaxUploadBytes();
    if ($maxBytes) {
        $preText = sprintf('<small>Max File Size: <b>%s</b></small><br/>', \Tk\Utils\File::bytes2String($maxBytes));
    }
@endphp
<x-tk-base::form.ui.field :$preText>
    @if($mode == 'edit')
        <input type="file" name="{{ $name }}" id="fid_{{ $cleanName }}" value="{{ $value }}"
            {{ $attributes->merge([ 'class' => 'form-control' . ( $errors->has($name) ? ' is-invalid' : '') ]) }} />
    @else
        {{-- TODO Attempt to show a link to the uploaded file if one exists --}}
        <p {{ $attributes->merge([ 'class' => 'form-control-plaintext fw-bold' ]) }}>{{ $value }}</p>
    @endif
</x-tk-base::form.ui.field>
