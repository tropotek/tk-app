<?php

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

    use WithPagination, IsLivewire, WithSearch;


    public function booted()
    {
        Breadcrumbs::push('Users');


        $this->appendColumn('name')
            ->setSortable()
            ->addClass('fw-bold w-auto')
            ->setView(function (User $user, Column $column) {
                return view('tkl-ui::components.table.columns.a', [
                    'href' => route('admin.users.edit', $user),
                    'text' => $column->value($user)
                ]);
            });

        $this->appendColumn('email')
            ->setSortable();

        $this->appendColumn('role')
            ->setSortable()
            ->setValue(function (User $user, $column) {
                return $user->role->name;
            });

        $this->appendColumn('created_at')
            ->addClass('text-end')
            ->addHeaderClass('text-end')
            ->setHeader('Created')
            ->setSortable();

        $this->appendFilter('role')
            ->setOptions(\App\Enum\Roles::toValueNameArray());
    }

    #[Computed]
    protected function rows(): array|Builder
    {
        return User::query()
            ->when($this->search, function (Builder $builder) {
                $str = preg_replace("/[^a-zA-Z0-9' -]/", " ", $this->search);
                $email = preg_replace("/[^a-zA-Z0-9@._-]/", "", $this->search);
                return $builder->where('name', 'like', "%{$str}%")
                    ->orWhere('email', 'like', "%{$email}%")
                    ->tap($this->resetPage() ?? fn() => null);
            })
            ->when($this->filterVals['role'] ?? null, fn(Builder $query) => $query->where('role', $this->filterVals['role']));
    }

    public function export(): StreamedResponse
    {
        return $this->exportCsv($this->rows()->get(), 'users.csv');
    }

};
?>

<div>
    <h1>{{ $pageName }}</h1>

    <x-tkl-ui::table.tk-filters :table="$this">
        <x-slot name="actions">
            <div class="p-2 ps-0">
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-plus-circle"></i> Create
                </a>
            </div>
        </x-slot>
    </x-tkl-ui::table.tk-filters>
    <x-tkl-ui::table :table="$this"/>

</div>
