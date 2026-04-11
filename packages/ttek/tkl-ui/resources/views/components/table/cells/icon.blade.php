@props([
    // optional
    'text' => '',
    'icon' => '',
    'title' => null,
])
@php
    if ($icon) {
        $text = sprintf('<i class="%s"></i>', $icon) . ($text ? ' ' : '') . $text;
    }
@endphp

<span {{ $attributes->merge(['title' => $title]) }}>{!! $text !!}</span>
