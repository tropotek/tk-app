@props([
	'title'  => '',
	'btnrow' => '',
	'col1'   => '',
	'col2'   => '',
	'col3'   => '',
])

@if (!empty($title) && $title->hasActualContent())
    <h3 class="mb-4">{{ $title }}</h3>
@endif

@if(!empty($btnrow) && $btnrow->hasActualContent())
    <div class="row mb-4">
        <div class="col-md-12">
            {{ $btnrow }}
        </div>
    </div>
@endif

<div class="row">

    {{-- main column --}}
    <div class="col-md-6">
        {{ $col1 }}
    </div>

    {{-- first side column --}}
    <div class="col-md-3">
        {{ $col2 }}
    </div>

    {{-- scond side column --}}
    <div class="col-md-3">
        {{ $col3 }}
    </div>

</div>
