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

    public UserForm $form;


    public function mount(?User $user)
    {

        //Breadcrumbs::push($user->name, route('admin.users.show1', $user));
        if ($user) {
            Breadcrumbs::push('Edit User: ' . $user->name);
        } else {
            Breadcrumbs::push('Create User');
        }


        $this->form->load($user);
    }

    public function save()
    {
        $this->form->update();



    }

};
?>

<div>
    <h3 class="mb-4">{{ $pageName }}</h3>


</div>
