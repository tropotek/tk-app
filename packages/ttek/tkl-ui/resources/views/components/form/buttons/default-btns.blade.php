@aware(['mode'])
@props([
    // required
    'editRoute'   => '',
    'cancelRoute' => '',
    // optional
    'viewLabel'   => 'Make changes',
    'viewCss'     => 'btn-outline-primary',
    'editLabel'   => 'Save Changes',
    'editCss'     => 'btn-outline-primary',
    'createLabel' => 'Create',
    'createCss'   => 'btn-outline-primary',
    'cancelLabel' => 'Cancel',
    'cancelCss'   => 'btn-outline-dark',
])

@switch ($mode)
    @case ('view')
        @if($cancelRoute)
            <x-tkl-ui::form.buttons.link
                :label="$cancelLabel"
                :href="$cancelRoute"
                class="{{ $cancelCss }}"
            />
        @endif
        @if($editRoute)
            <x-tkl-ui::form.buttons.link
                :label="$viewLabel"
                :href="$editRoute"
                class="{{ $viewCss }}"
            />
        @endif
    @break;

    @case ('edit')
        @if($cancelRoute)
            <x-tkl-ui::form.buttons.link
                :label="$cancelLabel"
                :href="$cancelRoute"
                class="{{ $cancelCss }}"
            />
        @endif
        <x-tkl-ui::form.buttons.submit
            :label="$editLabel"
            class="{{ $editCss }}"
            {{ $attributes }}
        />
    @break;

    @case ('create')
        @if($cancelRoute)
            <x-tkl-ui::form.buttons.link
                :label="$cancelLabel"
                :href="$cancelRoute"
                class="{{ $cancelCss }}"
            />
        @endif
        <x-tkl-ui::form.buttons.submit
            :label="$createLabel"
            class="{{ $createCss }}"
            {{ $attributes }}
        />
    @break;
@endswitch

