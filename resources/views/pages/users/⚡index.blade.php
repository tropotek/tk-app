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

new #[Layout('pages.main')]
class extends Component {

    use WithPagination, IsLivewireTable;

    #[Url(except: '')]
    public $roles = '';


    public function boot()
    {
        Breadcrumbs::push('Users');

        $this->appendCell('name')
            ->setSortable()
            ->addClass('fw-bold w-auto')
            ->setHtml(function (User $user, $cell) {
                return sprintf('<a href="%s">%s</a>', route('admin.users.edit', $user->id), $cell->text($user));
            });

        $this->appendCell('email')
            ->setSortable();

        $this->appendCell('roles')
            ->setText(function (User $user, $cell) {
                return $user->roles->pluck('name')->implode(', ');
            });

        // alt method to add cells
        $this->appendCell('created_at')
            ->setHeader('Created')
            ->setSortable();
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return $this->query()->paginate($this->limit ?: null);
    }

    protected function query(): Builder
    {
        return User::with('roles')
            ->when($this->search, function (Builder $builder) {
                $str = preg_replace("/[^a-zA-Z0-9' -]/", " ", $this->search);
                $email = preg_replace("/[^a-zA-Z0-9@._-]/", "", $this->search);
                return $builder->where('name', 'like', "%{$str}%")
                    ->orWhere('email', 'like', "%{$email}%")
                    ->tap($this->resetPage() ?? fn() => null);
            })
            ->when($this->roles, fn(Builder $query) => $query->role($this->roles))
            ->orderBy($this->safeSort(), $this->dir);
    }

    public function csv()
    {
        return $this->buildCsv($this->query(), 'users.csv');
    }

};
?>

<div>
    <h1>{{ $pageName }}</h1>

    <x-tkl-ui::table.filters :table="$this">
        <x-slot name="filters">
            <x-tkl-ui::table.filters.select
                wire:model.live="roles"
                :name="$this->tableKey('roles')"
                :options="[ '' => '- All Roles -', 'test' => 'Test', 'admin' => 'Admin', 'staff' => 'Staff', 'member' => 'Member']"
                value="{{ $this->roles }}"
            />
        </x-slot>
        <x-slot name="actions2">
            <button
                type="button"
                class="btn btn-link btn-sm"
                title="Download CSV"
                wire:click="csv"
            >
                <i class="fa-regular fa-file-excel fa-lg"></i>
            </button>
        </x-slot>

        <x-slot name="actions">
            <div class="p-2 ps-0">
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-plus-circle"></i> Create
                </a>
            </div>
        </x-slot>
    </x-tkl-ui::table.filters>

    <x-tkl-ui::table :table="$this"/>

</div>
