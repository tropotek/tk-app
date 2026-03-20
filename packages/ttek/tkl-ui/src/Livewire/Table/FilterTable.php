<?php

namespace Tk\Livewire\Table;

abstract class FilterTable extends Table
{

    public bool $showSearch = true;
    public bool $showLimit = true;


    public function render()
    {
        return view('tkl-ui::livewire.table.filter-table');
    }
}
