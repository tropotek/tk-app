<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Table\Cell;
use Tk\Table\IsLivewireTable;
use Tk\Table\IsSearchable;

new #[Layout('pages.main')]
class extends Component {

    use WithPagination, IsLivewireTable, IsSearchable;


    public function boot()
    {
        Breadcrumbs::push('Users');


        $this->appendCell('name')
            ->setSortable()
            ->addClass('fw-bold w-auto')
            ->setView(function (User $user, Cell $cell) {
                return view('tkl-ui::components.table.cells.a', [
                    'href' => route('admin.users.edit', $user),
                    'text' => $cell->value($user)
                ]);
            });

        $this->appendCell('email')
            ->setSortable();

        $this->appendCell('roles')
            ->setValue(function (User $user, $cell) {
                return $user->roles->pluck('name')->implode(', ');
            });

        // alt method to add cells
        $this->appendCell('created_at')
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

    public function csv()
    {
        return $this->buildCsv($this->query(), 'users.csv');
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
        <x-slot name="rightActions">
            <div
                class="p-2 text-primary clickable"
                title="Download CSV"
                wire:click="csv"
            >
                <i class="fa-regular fa-file-excel fa-lg align-middle"></i>
            </div>
        </x-slot>
    </x-tkl-ui::table.tk-filters>
    <x-tkl-ui::table :table="$this"/>

</div>
