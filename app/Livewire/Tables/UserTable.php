<?php

namespace App\Livewire\Tables;

use App\Models\User;
use Tk\Livewire\Table\Table;

class UserTable extends Table
{
    public function columns(): void
    {
        $this->appendCell('name');
        $this->appendCell('email');
        $this->appendCell('created_at')->setHeader('Created');
    }

    public function query()
    {
        return User::query();
    }
}
