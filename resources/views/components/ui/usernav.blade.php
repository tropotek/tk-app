<ul {{ $attributes->merge(['class' => 'dropdown-menu']) }}>
    @foreach (menu('UserNav')->getChildren() as $item)
        {{-- Pass initial classes to the component --}}
        <x-tkl-ui::navitem :item="$item" :maxLevel="1" level="0" class="" submenu-class="dropdown-menu" link-class="dropdown-item" />
    @endforeach
</ul>
