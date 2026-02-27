{{-- @see \Tk\Breadcrumbs\Breadcrumbs --}}
@php
    use Tk\Support\Facades\Breadcrumbs;
    $crumbs = Breadcrumbs::toArray();
    $lastIdx = count($crumbs)-1;
@endphp

<div class="{{ config('app.resources.layout', 'container') }} mt-3">
    <nav class="breadcrumb-component" aria-label="breadcrumb">
        <ol class="breadcrumb p-3 bg-body-tertiary rounded-3">
            @foreach ($crumbs as $i => $crumb)
                @if($i == $lastIdx)
                    <li class="breadcrumb-item">{{ $crumb['title'] }}</li>
                @else
                    <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['title'] }}</a></li>
                @endif
            @endforeach
        </ol>
    </nav>
</div>
