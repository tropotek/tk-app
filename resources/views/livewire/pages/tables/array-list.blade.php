<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Tbl\Cell;
use Tk\Tbl\Table;
use Tk\Tbl\IsTable;

new #[Layout('pages.main')]
class extends Component {

    use WithPagination, IsTable;

    #[Url(except: '')]
    public $search = '';


    public function mount()
    {
        Breadcrumbs::push('Manage Staff');
    }

    public function boot()
    {
        $this->table = new Table();

        $this->table->appendCell(new Cell(
            name: 'name',
            sortable: true,
        ));

        $this->table->appendCell(new Cell(
            name: 'email',
            sortable: true,
        ));

        $this->table->appendCell(new Cell(
            name: 'roles',
            sortable: false,
//            text: function ($row) {
//                return $row->roles->pluck('name')->implode(', ');
//            },
        ));

        // alt method to add cells
        $this->table->appendCell(new Cell('created_at'), 'roles')
            ->setHeader('Created')
            ->setSortable()
//            ->setText(function ($row) {
//                return $row->created_at->format('Y-m-d h:i');
//            })
        ;
    }

    public function clearFilters(): void
    {
        $this->reset();
    }

    #[Computed]
    public function rows(): \Traversable
    {

        $rows = [
            (object)['id' => 1, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 2, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 3, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 4, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 5, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 6, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 7, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 8, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
        ];
        
        // 1. filter results with any filters if available


        // 2. sort results (todo: using Gregs sort method for now, review)
        $sortCol = ($this->dir == 'desc' ? '-' : '') . $this->safeSort();
        $rows = $this->sortRows($rows, $sortCol);

        // 3. return paginated results
        return $this->paginate($rows);

//        return User::query()
//            //->search($this->search, fn() => $this->resetPage())
//            //->when($this->country, fn($query) => $query->where('country', $this->country))
//            ->when($this->search, function (Builder $builder) {
//                $str = preg_replace("/[^a-zA-Z0-9' -]/", " ", $this->search);
//                $email = preg_replace("/[^a-zA-Z0-9@._-]/", "", $this->search);
//                return $builder->where('name', 'like', "%{$str}%")
//                    ->orWhere('email', 'like', "%{$email}%")
//                    ->tap($this->resetPage() ?? fn () => null); // noop
//            })
//            ->orderBy($this->safeSort(), $this->dir)
//            ->paginate($this->limit);
    }

};
?>

<div>
    <h1>Manage Staff</h1>

    <div class="row my-2">
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
                class="btn btn-link btn-sm pb-1 ms-1"
                title="Clear Filters & Search"
                wire:click="clearFilters"
            >
                <i class="fa fa-circle-xmark fa-lg pb-2"></i>
            </button>

            <p>
                <a href="{{route('admin.users.create')}}" class="btn btn-primary">
                    New User
                </a>
            </p>

            <div class="flex-grow-1 text-end small">
                <button
                    type="button"
                    class="btn btn-link btn-sm pb-1 ms-1"
                    title="Download CSV"
                    {{--                    wire:click="csv"--}}
                >
                    <i class="fa-regular fa-file-excel fa-lg pb-2"></i>
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

    <table class="table table-striped table-hover">
        <thead>
        <tr>
            @foreach ($this->table->getCells()->filter(fn($r) => $r->isVisible()) as $cell)
                <th class="{{ $cell->isSortable() ? 'col-sort'  : '' }}">
                    @if ($cell->sortable)
                        <button class="btn btn-link ms-2 px-0 py-0 fw-bold text-decoration-underline"
                                wire:click="{{ ($this->sort === $cell->getName()) ? 'toggleDir' : '$set("sort", "'.$cell->name.'")' }}">
                            {{ $cell->getHeader() }}
                            @if ($this->sort === $cell->getName())
                                <i class="fa {{ ($this->dir === 'asc') ? 'fa-sort-down' : 'fa-sort-up' }}"></i>
                            @endif
                        </button>
                    @else
                        <span class="ms-2 fw-bold">{{ $cell->getHeader() }}</span>
                    @endif
                </th>
            @endforeach
            <th class="text-muted"><i class="fa-solid fa-pen-to-square"></i></th>

        </tr>
        </thead>
        <tbody class="table-group-divider">
        @foreach ($this->rows as $user)
            <tr wire:key="{{ $user->id }}">
                @foreach($this->table->getCells()->filter(fn($r) => $r->isVisible()) as $cell)
                    @if ($cell->getName() == 'name')
                        <td class="fw-bold">
                            <a href="{{ route('admin.users.edit', $user->id) }}">{{ $cell->html($user) }}</a>
                        </td>
                    @else
                        <td class="tt">{{ $cell->html($user) }}</td>
                    @endif
                @endforeach

                <td>
                    <a href="{{ route('admin.users.edit', $user->id) }}">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $this->rows->links() }}

</div>
