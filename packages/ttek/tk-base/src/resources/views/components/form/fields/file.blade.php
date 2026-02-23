@aware(['mode' => 'view'])
@props([
    // required
    'name'     => '',
    // optional
    'value'    => '',   // markup to display in view mode
    'label'    => '',
    'fieldCss' => '',
    'help'     => ''
])
@php
    $cleanName = str_replace(['[', ']'], '', $name);

    // Help message
    $preText = '';
    $maxBytes = \Tk\Utils\File::getMaxUploadBytes();
    if ($maxBytes) {
        $preText = sprintf('<small>Max File Size: <b>%s</b></small><br/>', \Tk\Utils\File::bytes2String($maxBytes));
    }
@endphp
<x-tk-base::form.ui.field :$preText>
    @if($mode == 'view')
        {{-- TODO Attempt to show a link to the uploaded file if one exists --}}
        <p {{ $attributes->merge([ 'class' => 'form-control-plaintext fw-bold' ]) }}>{!! $value !!}</p>
    @else
        <input type="file" name="{{ $name }}" id="fid_{{ $cleanName }}"
            {{ $attributes->merge([ 'class' => 'form-control' . ( $errors->has($name) ? ' is-invalid' : '') ]) }} />
    @endif
</x-tk-base::form.ui.field>
