<?php

use Livewire\Attributes\Rule;
use Livewire\Component;

new class extends Component {

    public string $rand = '--';

    #[Rule('required', message: 'Empty TODO\'s are an oxymoron')]
    #[Rule('min:3', message: ' text to short')]
    public string $todo = '';
    public array $list = [];


    public function add(): void
    {
        $this->validate();

        $this->list[] = $this->todo;

        $this->reset('todo');
    }

    public function delete(int $idx): void
    {
        if (isset($this->list[$idx])) {
            unset($this->list[$idx]);
            $this->list = array_values($this->list);
        }
    }

    public function makeRand(): void
    {
        $this->rand = rand(1000, 100000);
    }

    public function clearRand(): void
    {
        $this->reset('rand');
    }


};
?>

<div class="card mb-3 border-info">
    <div class="card-header text-bg-info">
        <h6 class="mb-0">
            <a href="#collapse-example" id="heading-example" role="button"
                class="d-block text-decoration-none text-white" data-bs-toggle="collapse">
                <i class="fa fa-chevron-down text-white-50 float-end"></i>
                Livewire Examples
            </a>
        </h6>
    </div>
    <div id="collapse-example" class="collapse show">
        <div class="card-body">
            <p>WOW! I am a basic livewire component.</p>
            <p>State: <b>{{ $rand }}</b></p>

            <button wire:click="makeRand()" class="btn btn-sm btn-outline-secondary"><i class="fa fa-refresh"></i>
            </button>
            <button wire:click="clearRand()" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>

            <hr/>
            <h4>Todo List</h4>
            <form class="mb-3" wire:submit="add()">
                <div class="input-group">
                    <input type="text" class="form-control" wire:model="todo" placeholder="Add new Todo">
                    <button class="btn btn-outline-secondary" type="submit">Add</button>
                </div>
                @error('todo')<em class="text-danger">{{ $message }}</em>  @enderror
            </form>
            <ul>
                @foreach ($list as $idx => $todo)
                    <li wire:key="{{ $idx }}">
                        <button type="button" class="btn btn-sm btn-white" title="Delete"
                            wire:click="delete({{ $idx }})"><i class="fa fa-trash-alt"></i></button>
                        {{ $todo }}
                    </li>
                @endforeach
            </ul>

        </div>
    </div>
</div>
