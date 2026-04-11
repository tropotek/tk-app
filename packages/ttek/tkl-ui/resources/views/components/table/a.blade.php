@props([
    // required
    'href',
    'title' => null,
    'text' => '',
])

<a {{ $attributes->merge(['href' => $href,'title' => $title,]) }}>{!! $text !!}</a>
