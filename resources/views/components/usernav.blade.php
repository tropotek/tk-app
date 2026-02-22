<ul {{ $attributes->merge(['class' => 'dropdown-menu']) }}>
    @foreach (menu('UserNav') as $item)
        {{-- Pass initial classes to the component --}}
        <x-tk-base::menu.bootstrap5-navitem :item="$item" :maxLevel="1" level="0" class="" submenu-class="dropdown-menu" link-class="dropdown-item" />
    @endforeach
</ul>
