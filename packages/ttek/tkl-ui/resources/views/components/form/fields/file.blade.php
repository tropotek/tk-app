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
    $errorKey = $attributes->get('wire:model') ?: $cleanName;

    // Help message
    $preText = '';
    $maxBytes = \Tk\Utils\File::getMaxUploadBytes();
    if ($maxBytes) {
        $preText = sprintf('<small>Max File Size: <b>%s</b></small><br/>', \Tk\Utils\File::bytes2String($maxBytes));
    }
@endphp
<x-tkl-ui::form.ui.field :$preText :$errorText :$errorKey>
    @if($mode == 'view')
        <p {{ $attributes->merge([ 'class' => 'form-control-plaintex' ]) }}>{!! $value !!}</p>
    @else
        <input {{ $attributes->merge([
                'type'     => 'file',
                'name'     => $cleanName,
                'id'       => 'fid-'.$cleanName,
                'class'    => 'form-control' . ( $errors->has($errorKey) ? ' is-invalid' : ''),
            ]) }}
        />
    @endif
</x-tkl-ui::form.ui.field>
