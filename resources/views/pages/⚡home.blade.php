<?php

use Livewire\Attributes\Layout;
use Livewire\Component;
use Tk\Support\Facades\Breadcrumbs;

new #[Layout('pages.main')]
class extends Component {

    public function mount()
    {
        Breadcrumbs::push('Home');

        if (request()->has('alert')) {
            $type = request()->input('alert');

            return redirect(request()->fullUrlWithoutQuery(['alert']))->with($type, "This is a {$type} flash message.");
        }
    }

};
?>

<div class="container">
    <div class="col text-center">
        <h1 class="text-primary">Public Home Page</h1>
    </div>

    <p>&nbsp;</p>
    <p><strong><a href="{{ route('login') }}">Login</a> as U: admin@example.com P: password</strong></p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>

    <div>
    </div>
</div>
