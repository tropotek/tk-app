<?php

use App\Form\ProfileForm;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Tk\Support\Facades\Breadcrumbs;

new #[Layout('pages.main')]
class extends Component {

    public ProfileForm $form;

    public function mount()
    {
        Breadcrumbs::push('My Profile');

        $this->form->load(auth()->user());
    }

    public function save()
    {
        $this->form->save();

        session()->flash('success', 'Profile saved.');
    }

    public function changePassword()
    {
        Password::sendResetLink(['email' => auth()->user()->email]);

        session()->flash('success', 'A password change email has been sent to your inbox.');
    }

};
?>

<div>
    <h3 class="mb-4">My Profile</h3>

    <x-tkl-ui::form wire:submit="save" mode="edit">

        <x-slot:buttons>
            <x-tkl-ui::form.buttons.default-btns editLabel="Save Profile" />
            <x-tkl-ui::form.buttons.submit
                type="button"
                label="Change Password"
                class="btn-outline-dark"
                data-bs-toggle="modal"
                data-bs-target="#changePasswordModal"
            />
        </x-slot:buttons>

        <x-slot:fields>

            <x-tkl-ui::form.fields.input name="name" wire:model="form.name" required :value="$form->name" />

            <x-tkl-ui::form.fields.input name="email" type="email" wire:model="form.email" required :value="$form->email" />

        </x-slot:fields>

    </x-tkl-ui::form>

    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="changePasswordModalLabel">Change Password</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>We'll email you a link to reset your password. Do you want to continue?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" wire:click="changePassword">Send Email</button>
                </div>
            </div>
        </div>
    </div>

</div>
