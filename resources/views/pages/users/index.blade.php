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
use Tk\Tbl\Cell;
use Tk\Tbl\IsLivewireTable;

new #[Layout('pages.main')]
class extends Component {

    use WithPagination, IsLivewireTable;

    #[Url(except: '')]
    public $search = '';


    public function mount()
    {
        Breadcrumbs::push('Manage Users');
    }

    public function boot()
    {
        $this->appendCell(new Cell(
            name: 'name',
            sortable: true,
        ))->addClass('fw-bold')
        ->setHtml(function ($user) {
            return '<a href="' . route('admin.users.edit', $user->id) . '">' . e($user->name) . '</a>';
        });

        $this->appendCell(new Cell(
            name: 'email',
            sortable: true,
        ));

        $this->appendCell(new Cell(
            name: 'roles',
            sortable: false,
            text: function ($row) {
                return $row->roles->pluck('name')->implode(', ');
            },
        ));

        // alt method to add cells
        $this->appendCell(new Cell('created_at'), 'roles')
            ->setHeader('Created')
            ->setSortable()
            ->setText(function ($row) {
                return $row->created_at->format('Y-m-d h:i');
            });
    }

    public function clearFilters(): void
    {
        $this->reset();
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return User::query()
            //->search($this->search, fn() => $this->resetPage())
            //->when($this->country, fn($query) => $query->where('country', $this->country))
            ->when($this->search, function (Builder $builder) {
                $str = preg_replace("/[^a-zA-Z0-9' -]/", " ", $this->search);
                $email = preg_replace("/[^a-zA-Z0-9@._-]/", "", $this->search);
                return $builder->where('name', 'like', "%{$str}%")
                    ->orWhere('email', 'like', "%{$email}%")
                    ->tap($this->resetPage() ?? fn() => null); // noop
            })
            ->when($this->dir, function (Builder $builder) {
                return $builder->orderBy($this->safeSort(), $this->dir);
            })
            ->paginate($this->limit);
    }

};
?>

<div>
    <h1>Manage Staff</h1>

    <div class="row mb-4">
        <div class="d-flex flex-nowrap text-nowrap gap-2 align-items-center">

            <div x-data="{ q: '' }">
                <input type="text" class="form-control form-control-sm w-auto"
                       placeholder="Name, Email"
                       x-model="q"
                       @input.debounce.250ms="if (q.trim().length >= 3 || q.trim() === '') $wire.set('search', q.trim())"
                />
            </div>

            <div class="text-nowrap text-primary">
                <small>Filter By:</small>
            </div>

            <select class="form-select form-select-sm w-auto">
                <option value="">All Countries</option>
            </select>

            <button
                type="button"
                class="btn btn-link btn-sm"
                title="Clear Filters & Search"
                wire:click="clearFilters"
            >
                <i class="fa fa-circle-xmark fa-lg pb-2"></i>
            </button>

            <div>
                <a href="{{route('admin.users.create')}}" class="btn btn-primary btn-sm">
                    New User
                </a>
            </div>

            <div class="flex-grow-1 text-end small">
                <button
                    type="button"
                    class="btn btn-link btn-sm"
                    title="Download CSV"
                    {{--                    wire:click="csv"--}}
                >
                    <i class="fa-regular fa-file-excel fa-lg"></i>
                </button>

                <span class="text-secondary">
                    Showing
                    <span class="fw-semibold">{{ $this->rows->firstItem() }}</span>
                    to
                    <span class="fw-semibold">{{ $this->rows->lastItem() }}</span>
                    of
                    <span class="fw-semibold">{{ $this->rows->total() }}</span>
                    results
                </span>
            </div>
        </div>
    </div>

    <x-tkl-ui::tbl.livewire :table="$this" />

</div>
