<?php

use App\Enum\Roles;
use App\Form\UserForm;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Tk\Support\Facades\Breadcrumbs;

new #[Layout('pages.main')]
class extends Component {

    public UserForm $form;

    public string $mode = 'create';

    public function mount(?User $user)
    {
        $this->authorize($user ? 'update' : 'create', $user ?? User::class);

        if ($user) {
            $this->mode = 'edit';
            Breadcrumbs::push('Edit User: ' . $user->name);
        } else {
            Breadcrumbs::push('Create User');
        }

        $this->form->load($user);
    }

    #[Computed]
    public function roleOptions(): array
    {
        $options = Roles::toValueNameArray();

        if (! auth()->user()->isAdmin()) {
            unset($options[Roles::Admin->value]);
        }

        return $options;
    }

    public function save()
    {
        $this->form->save();

        session()->flash('success', 'User saved.');

        return redirect()->route('admin.users.index');
    }

};
?>

<div>
    <h3 class="mb-4">{{ $pageName }}</h3>

    <x-tkl-ui::form wire:submit="save" :mode="$mode">

        <x-slot:buttons>
            <x-tkl-ui::form.buttons.default-btns
                :cancelRoute="route('admin.users.index')" />
        </x-slot:buttons>

        <x-slot:fields>

            <x-tkl-ui::form.fields.input name="name" wire:model="form.name" required :value="$form->name" />

            <x-tkl-ui::form.fields.input name="email" type="email" wire:model="form.email" required :value="$form->email" />

            <x-tkl-ui::form.fields.select
                name="role"
                wire:model="form.role"
                :options="$this->roleOptions"
                :value="$form->role?->value"
            />

        </x-slot:fields>

    </x-tkl-ui::form>

</div>
