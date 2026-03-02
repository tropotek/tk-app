@aware(['mode' => 'view'])
@props([
    // required
    'name'     => '',
    // optional
    'value'    => '',   // markup to display in view mode
    'label'    => '',
    'fieldCss' => '',
    'help'     => '',
    'errorText' => '',
])
@php
    $cleanName = str_replace(['[', ']'], '', $name);
    $value = $value ?? '';

    // Help message
    $preText = '';
    $maxBytes = \Tk\Utils\File::getMaxUploadBytes();
    if ($maxBytes) {
        $preText = sprintf('<small>Max File Size: <b>%s</b></small><br/>', \Tk\Utils\File::bytes2String($maxBytes));
    }
@endphp
<x-tk-base::form.ui.field :$preText :$errorText>
    @if($mode == 'view')
        <p {{ $attributes->merge([ 'class' => 'form-control-plaintex' ]) }}>{!! $value !!}</p>
    @else
        <input {{ $attributes->merge([
                'type'     => 'file',
                'name'     => $cleanName,
                'id'       => 'fid-'.$cleanName,
                'class'    => 'form-control' . ( $errors->has($name) ? ' is-invalid' : ''),
            ]) }}
        />
    @endif
</x-tk-base::form.ui.field>
