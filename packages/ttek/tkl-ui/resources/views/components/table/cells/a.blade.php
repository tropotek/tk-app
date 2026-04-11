@props([
    // required
    'href',
    // optional
    'text' => '',
    'title' => null,
    'icon' => ''
])
@php
    if ($icon) {
        $text = sprintf('<i class="%s"></i>', $icon) . ($text ? ' ' : '') . $text;
    }
@endphp

<a {{ $attributes->merge(['href' => $href,'title' => $title,]) }}>{!! $text !!}</a>
