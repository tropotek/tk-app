@php
    use Tk\Support\Facades\Breadcrumbs;
    $breadcrumbs = Breadcrumbs::toArray();
    $lastTitle = array_key_last($breadcrumbs);
@endphp

<nav class="{{ $attributes->merge(['class', 'breadcrumb-component']) }}" aria-label="breadcrumb">
    <ol class="breadcrumb p-3 bg-body-tertiary rounded-3">
        @foreach ($breadcrumbs as $title => $url)
            @if($title == $lastTitle)
                <li class="breadcrumb-item">{!! $title !!}</li>
            @else
                <li class="breadcrumb-item"><a href="{{ $url }}">{!! $title !!}</a></li>
            @endif
        @endforeach
    </ol>
</nav>
