<?php

use App\Form\UserForm;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Table\Column;
use Tk\Table\TableComponent;
use Tk\Table\Traits\IsLivewire;
use Tk\Table\Traits\WithSearch;

new #[Layout('pages.main')]
class extends Component {


    public string $username;

    public UserForm $form;


    public function mount(User $user)
    {
        Breadcrumbs::push($user->name, route('admin.users.show'));
        Breadcrumbs::push('Edit User');


        $this->username = $user->user->username;

        logger([$user]);

        $this->form->load($user);
    }

    public function save()
    {
        $this->form->update();

        // The recommendation is to use a string literal to check SuperAdmin
        // abilities. See https://github.com/eMedSIS/sisv2/pull/118
        if (auth('staff')->user()->can('ChangeUsername')) {
            $this->validate([
                'username' => ['required', new Username($this->form->staff->user)],
            ]);

            $this->form->staff->user->update(['username' => $this->username]);
        }

        $this->toastSuccess('Staff updated.');
    }

};
?>

<div>
    <h1>{{ $pageName }}</h1>


</div>
