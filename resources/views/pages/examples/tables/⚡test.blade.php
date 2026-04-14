<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tk\Support\Facades\Breadcrumbs;

new #[Layout('pages.main')]
class extends Component {

    use WithPagination;


    public function booted()
    {
        Breadcrumbs::push('Table Test');

    }

    #[Computed]
    protected function rows()
    {
        $query = User::with('roles');
//            ->when($this->search, function (Builder $builder) {
//                $str = preg_replace("/[^a-zA-Z0-9' -]/", " ", $this->search);
//                $email = preg_replace("/[^a-zA-Z0-9@._-]/", "", $this->search);
//                return $builder->where('name', 'like', "%{$str}%")
//                    ->orWhere('email', 'like', "%{$email}%")
//                    ->tap($this->resetPage() ?? fn() => null);
//            })
//            ->when($this->filterVals['roles'] ?? null, fn(Builder $query) => $query->role($this->filterVals['roles']));

        //$query = $query->orderBy('', 'asc');

        return $query->paginate();
    }

};
?>

<div>
    <h1>{{ $pageName }}</h1>

    <p>Testing page</p>

</div>
