@php
    $text ??= '';
    $icon ??= '';
    if ($icon) {
        $text = sprintf('<i class="%s"></i>', $icon) . ($text ? ' ' : '') . $text;
    }
    // When called via view() rather than as a Blade component, $attributes is not
    // automatically set up as a ComponentAttributeBag, so we build it from $__data.
    if (!isset($attributes) || !($attributes instanceof \Illuminate\View\ComponentAttributeBag)) {
        $attributes = new \Illuminate\View\ComponentAttributeBag(
            collect($__data)
                ->except(['text', 'icon', 'errors', 'component', 'slot', '__slots'])
                ->filter(fn($v) => is_scalar($v) || is_null($v))
                ->all()
        );
    }
@endphp

<a {{ $attributes }}>{!! $text !!}</a>
