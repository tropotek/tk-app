{{-- @see \Tk\Menu\MenuBuilder --}}
<?php
/** @var \Tk\Menu\MenuItem $item */
?>
@props([
    'item',
    'maxLevel' => 2,
    'level' => 0,
    'linkClass' => '',
    'submenuClass' => '',
])

<li class="
    {{ $attributes->get('class') }}
    {{ $item->hasChildren() ? 'dropdown' : '' }}
    {{ $item->isDisabled() ? 'disabled' : '' }}"
>
    @if($item->isSeparator())
        <hr class="dropdown-divider">
    @else
        <a {{ $item->getAttributes()->merge([
                'class'    => $linkClass . ($item->hasChildren() ? ' dropdown-toggle' : ''),
                'href'     => $item->getUrl() ?: null,
                'disabled' => $item->isDisabled() ? 'disabled' : null,
                'role'     => $item->hasChildren() ? 'button' : null,
                'target'    => $item->getTarget() ?: null,
                'data-bs-button' => $item->hasChildren() ? 'button' : null,
                'data-bs-toggle' => $item->hasChildren() ? 'dropdown' : null,
            ]) }}
        >
            @if (empty($item->getIcon()))
                {{ $item->getLabel() }}
            @else
                <i class="{{ $item->getIcon() }}"></i>
                @if($item->isTitleVisible()){{ $item->getLabel() }}@endif
            @endif
        </a>
        @if($item->hasChildren())
            <ul class="dropdown-menu {{ $submenuClass }}">
                @foreach ($item->getChildren() as $i => $child)
                    {{-- Prevent more than 1 level of dropdown iteration --}}
                    @if(($child->hasChildren() && ($level == $maxLevel-1)) || !$child->isVisible())
                        @continue
                    @endif
                    {{-- hide a seperator if it is the first or last item, or prev item was a seperator --}}
                    @if($child->isSeparator() && ($i == (count($item->getChildren())-1) || $i == 0 || $item->getChildren()[$i-1]->isSeparator()))
                        @continue
                    @endif
                    {{-- Pass classes down, adding a specific class for the next level --}}
                    <x-tk-base::navitem :item="$child" level="{{ $level + 1 }}" linkClass="dropdown-item" submenuClass="dropdown-menu"/>
                @endforeach
            </ul>
        @endif
    @endif
</li>
