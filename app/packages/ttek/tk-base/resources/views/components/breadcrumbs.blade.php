{{-- @see \Tk\Breadcrumbs\Breadcrumbs --}}
@php
    use Tk\Support\Facades\Breadcrumbs;
    $crumbs = Breadcrumbs::toArray();
    $last = array_key_last($crumbs);
@endphp

<div class="{{ config('app.resources.layout', 'container') }} mt-3">
    <nav class="breadcrumb-component" aria-label="breadcrumb">
        <ol class="breadcrumb p-3 bg-body-tertiary rounded-3">
            @foreach ($crumbs as $title => $url)
                @if($title == $last)
                    <li class="breadcrumb-item">{{ $title }}</li>
                @else
                    <li class="breadcrumb-item"><a href="{{ $url }}">{{ $title }}</a></li>
                @endif
            @endforeach
        </ol>
    </nav>
</div>
