@props([
	'title' => '',
	'buttons',
	'col1' => '',
	'col2' => '',
])

@if (!empty($title) && $title->hasActualContent())
    <h3 class="mb-4">{{ $title }}</h3>
@endif

@if(!empty($buttons) && $buttons->hasActualContent())
    <div class="row">
        <div class="col-md-12">
            {{ $buttons }}
        </div>
    </div>
@endif

<div class="row">

    {{-- first column --}}
    <div class="col-md-9">
        {{ $col1 }}
    </div>

    {{-- second column --}}
    <div class="col-md-3">
        {{ $col2 }}
    </div>

</div>
