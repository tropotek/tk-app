<?php
/** @var \Tk\Menu\MenuItem $item */
?>
@props([
    'item',
    'maxLevel' => 2,
    'level' => 0,
])

<li class="level-{{ $level }}
        {{ $attributes->get('class') }}
        {{ $item->hasChildren() ? 'dropdown' : '' }}
        {{ $item->isDisabled() ? 'disabled' : '' }}">
    @if($item->isSeparator())
        <hr class="dropdown-divider">
    @else

        <a href="{{ $item->getUrl() }}" class="{{ $attributes->get('link-class') }}
                {{ $item->isDisabled() ? 'disabled' : ''}}
                {{ $item->hasChildren() ? 'dropdown-toggle' : '' }}"
            {!! $item->hasChildren() ? 'role="button" data-bs-toggle="dropdown" aria-expanded="false"' : '' !!}>
            @if (empty($item->getIcon()))
                {{ $item->getLabel() }}
            @else
                <i class="{{ $item->getIcon() }}"></i>
                @if($item->isTitleVisible())
                    {{ $item->getLabel() }}
                @endif
            @endif
        </a>
        @if($item->hasChildren())
            <ul class="dropdown-menu level-{{ $level + 1 }} {{ $attributes->get('submenu-class') }}">
                @foreach ($item->getChildren() as $child)
                    {{-- Prevent more than 1 level of dropdown iteration --}}
                    @if(($child->hasChildren() && ($level == $maxLevel-1)) || !$child->isVisible())
                        @continue
                    @endif
                    {{-- Pass classes down, adding a specific class for the next level --}}
                    <x-tk-base::menu.bootstrap5-navitem :item="$child" level="{{ $level + 1 }}" class="" link-class="dropdown-item"
                                 submenu-class="dropdown-menu"/>
                @endforeach
            </ul>
        @endif
    @endif
</li>
