@aware(['mode', 'values'])

@props([
    'viewLabel'   => 'Make changes',
    'editRoute'   => '',
    'viewCss'     => 'btn-outline-primary',
    'editLabel'   => 'Save Changes',
    'editCss'     => 'btn-outline-primary',
    'createLabel' => 'Create',
    'createCss'   => 'btn-outline-primary',
    'cancelLabel' => 'Cancel',
    'cancelRoute' => '',
    'cancelCss'   => 'btn-outline-dark',
])

@switch ($mode)
    @case ('view')
        @if($editRoute)
            <x-tk-base::form.buttons.link :label="$viewLabel" :href="$editRoute" class="{{ $viewCss }}"/>
        @endif
    @break;

    @case ('edit')
        @if($editRoute)
            <x-tk-base::form.buttons.link :label="$cancelLabel" :href="$cancelRoute" class="{{ $cancelCss }}"/>
        @endif
        <x-tk-base::form.buttons.submit :label="$editLabel" class="{{ $editCss }}"/>
    @break;

    @case ('create')
        @if($editRoute)
            <x-tk-base::form.buttons.link :label="$cancelLabel" :href="$cancelRoute" class="{{ $cancelCss }}"/>
        @endif
        <x-tk-base::form.buttons.submit :label="$createLabel" class="{{ $createCss }}"/>
    @break;
@endswitch

