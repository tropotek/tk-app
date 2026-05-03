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


        $this->appendColumn('actions')
            ->setSortable()
            ->addClass('fw-bold w-auto')
            ->setView(function (User $user, Column $column) {
                return sprintf('<a href="%s"><i class="fa fa-fw fa-pencil"></i></a>',
                    route('admin.users.edit1', $user)
                );
            });

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

        $this->appendColumn('roles')
            ->setValue(function (User $user, $column) {
                return $user->roles->pluck('name')->implode(', ');
            });

        $this->appendColumn('created_at')
            ->setHeader('Created')
            ->setSortable();

        $this->appendFilter('roles')
            ->setOptions(['admin' => 'Admin', 'staff' => 'Staff', 'member' => 'Member']);
    }

    #[Computed]
    protected function rows(): array|Builder
    {
        return User::with('roles')
            ->when($this->search, function (Builder $builder) {
                $str = preg_replace("/[^a-zA-Z0-9' -]/", " ", $this->search);
                $email = preg_replace("/[^a-zA-Z0-9@._-]/", "", $this->search);
                return $builder->where('name', 'like', "%{$str}%")
                    ->orWhere('email', 'like', "%{$email}%")
                    ->tap($this->resetPage() ?? fn() => null);
            })
            ->when($this->filterVals['roles'] ?? null, fn(Builder $query) => $query->role($this->filterVals['roles']));
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
