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
        Breadcrumbs::push('Sisv2 Array');
    }

    public function boot()
    {
        $this->appendCell(new Cell(
            name: 'name',
            sortable: true,
        ));

        $this->appendCell(new Cell(
            name: 'email',
            sortable: true,
        ));

        $this->appendCell(new Cell(
            name: 'roles',
            sortable: false,
//            text: function ($row) {
//                return $row->roles->pluck('name')->implode(', ');
//            },
        ));

        // alt method to add cells
        $this->appendCell(new Cell('created_at'), 'roles')
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
    public function rows(): LengthAwarePaginator
    {

        $rows = [
            (object)['id' => 1, 'name' => 'Test 1', 'email' => 'email1@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 2, 'name' => 'Test 2', 'email' => 'email2@example.com', 'roles' => 'test', 'created_at' => '2021-01-02 12:23:33'],
            (object)['id' => 3, 'name' => 'Test 3', 'email' => 'email3@example.com', 'roles' => 'test', 'created_at' => '2021-03-01 12:23:33'],
            (object)['id' => 4, 'name' => 'Test 4', 'email' => 'email4@example.com', 'roles' => 'test', 'created_at' => '2021-07-01 12:23:33'],
            (object)['id' => 5, 'name' => 'Test 6', 'email' => 'email6@example.com', 'roles' => 'test', 'created_at' => '2021-04-25 12:23:33'],
            (object)['id' => 6, 'name' => 'Test 8', 'email' => 'email8@example.com', 'roles' => 'test', 'created_at' => '2021-02-03 12:23:33'],
            (object)['id' => 7, 'name' => 'Test 9', 'email' => 'email9@example.com', 'roles' => 'test', 'created_at' => '2021-02-01 12:23:33'],
            (object)['id' => 8, 'name' => 'Test 19', 'email' => 'email10@example.com', 'roles' => 'test', 'created_at' => '2021-04-12 12:23:33'],
        ];

        // 1. filter results with any filters if available


        // 2. sort results (todo: using Greg's sort method for now, review)
        $sortCol = ($this->dir == 'desc' ? '-' : '') . $this->safeSort();
        $rows = $this->sortRows($rows, $sortCol);

        // 3. return paginated results
        return $this->paginateArray($rows);
    }

};
?>

<div>
    <h1>Sis Style Array List</h1>

    <div class="row">
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
                <i class="fa fa-circle-xmark fa-lg"></i>
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

    <table class="table table-striped table-hover">
        <thead>
        <tr>
            @foreach ($this->getCells()->filter(fn($r) => $r->isVisible()) as $cell)
                <th class="{{ $cell->isSortable() ? 'col-sort'  : '' }}">
                    @if ($cell->isSortable())
                        <button
                            class="btn btn-link fw-bold {{ ($this->sort === $cell->getName()) ? $this->dir : ''  }}"
                            wire:click="{{ ($this->sort === $cell->getName()) ? 'toggleDir' : '$set("sort", "' . $cell->getName() . '")' }}">
                            {{ $cell->getHeader() }}
                        </button>
                    @else
                        <span class="fw-bold">{{ $cell->getHeader() }}</span>
                    @endif
                </th>
            @endforeach
            <th class="text-muted"><i class="fa-solid fa-pen-to-square"></i></th>

        </tr>
        </thead>
        <tbody class="table-group-divider">
        @foreach ($this->rows as $user)
            <tr wire:key="{{ $user->id }}">
                @foreach($this->getCells()->filter(fn($r) => $r->isVisible()) as $cell)
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
