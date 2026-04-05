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

    #[Url(except: '')]
    public $roles = '';


    public function boot()
    {
        Breadcrumbs::push('Sisv2 Array');

//        $this->setDefaultLimit(2);
//        $this->setDefaultSort('email');

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
        ));

        // alt method to add cells
        $this->appendCell(new Cell('created_at'), 'roles')
            ->setHeader('Created')
            ->setSortable()
        ;
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $rows = [
            (object)['id' => 1, 'name' => 'Test 1', 'email' => 'email1@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 2, 'name' => 'Test 2', 'email' => 'email2@example.com', 'roles' => 'test', 'created_at' => '2021-01-02 12:23:33'],
            (object)['id' => 3, 'name' => 'Test 3', 'email' => 'email3@example.com', 'roles' => 'test', 'created_at' => '2021-03-01 12:23:33'],
            (object)['id' => 4, 'name' => 'Test 4', 'email' => 'email4@example.com', 'roles' => 'test', 'created_at' => '2021-07-01 12:23:33'],
            (object)['id' => 5, 'name' => 'Test 5', 'email' => 'email5@example.com', 'roles' => 'test', 'created_at' => '2021-04-25 12:23:33'],
            (object)['id' => 6, 'name' => 'Test 6', 'email' => 'email6@example.com', 'roles' => 'test', 'created_at' => '2021-02-03 12:23:33'],
            (object)['id' => 7, 'name' => 'Test 7', 'email' => 'email7@example.com', 'roles' => 'test', 'created_at' => '2021-02-01 12:23:33'],
            (object)['id' => 8, 'name' => 'Test 8', 'email' => 'email8@example.com', 'roles' => 'test', 'created_at' => '2021-04-12 12:23:33'],
        ];

        // 1. filter results with any filters if available
        if ($this->search) {
            $rows = array_filter($rows, function ($row) {
                return str_contains(strtolower($row->name), strtolower($this->search));
            });
        }

        if ($this->roles) {
            $rows = array_filter($rows, function ($row) {
                return str_contains(strtolower($row->roles), strtolower($this->roles));
            });
        }

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

    <x-tkl-ui::tbl.livewire.filters :table="$this">
        <x-slot name="filters">
            <x-tkl-ui::tbl.livewire.filters.select
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
    </x-tkl-ui::tbl.livewire.filters>

    <x-tkl-ui::tbl.livewire :table="$this" />

</div>
