<?php

use App\Form\SettingForm;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Tk\Support\Facades\Breadcrumbs;

new #[Layout('pages.main')]
class extends Component {

    public SettingForm $form;

    public function mount()
    {
        $this->authorize('view', Setting::class);

        Breadcrumbs::push('Settings');

        $this->form->load(Setting::current());
    }

    public function save()
    {
        $this->form->save();

        session()->flash('success', 'Settings saved.');
    }

};
?>

<div>
    <h3 class="mb-4">{{ $pageName }}</h3>

    <x-tkl-ui::form wire:submit="save" mode="edit">

        <x-slot:buttons>
            <x-tkl-ui::form.buttons.default-btns editLabel="Save Settings" />
        </x-slot:buttons>

        <x-slot:fields>

            <x-tkl-ui::form.fields.input name="site_title" wire:model="form.site_title" required :value="$form->site_title" />

            <x-tkl-ui::form.fields.input name="site_short_title" wire:model="form.site_short_title"
                help="Shown in the nav bar instead of the site title, if not empty."
                :value="$form->site_short_title" />

            <x-tkl-ui::form.fields.input name="site_email" type="email" wire:model="form.site_email" required
                help="Used as the from address for all outgoing emails."
                :value="$form->site_email" />

            <x-tkl-ui::form.fields.checkbox
                name="enable_user_reg"
                label="Enable User Registration"
                :options="['1' => 'Show the registration link on public pages']"
                :isSwitch="true"
                wire:model="form.enable_user_reg"
                :value="$form->enable_user_reg ? '1' : ''"
            />

        </x-slot:fields>

    </x-tkl-ui::form>

</div>
