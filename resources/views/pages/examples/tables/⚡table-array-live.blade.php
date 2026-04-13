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
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Table\Cell;
use Tk\Table\IsLivewireTable;
use Tk\Table\IsSearchable;

new #[Layout('pages.main')]
class extends Component {

    use WithPagination, IsLivewireTable, IsSearchable;

    #[Url(except: '')]
    public $roles = '';


    public function boot()
    {
        Breadcrumbs::push('Table Array (Livewire)');

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
        $this->appendCell('created_at')
            ->setHeader('Created')
            ->setSortable();

        $this->appendFilter('roles')
            ->setOptions(['admin' => 'Admin', 'staff' => 'Staff', 'member' => 'Member']);
    }

    #[Computed]
    protected function rows(): array|Builder
    {
        $rows = [
            (object) [
                'id' => 1, 'name' => 'Test 1', 'email' => 'email1@example.com', 'roles' => 'test',
                'created_at' => '2021-01-01 12:23:33'
            ],
            (object) [
                'id' => 2, 'name' => 'Test 2', 'email' => 'email2@example.com', 'roles' => 'admin',
                'created_at' => '2021-01-02 12:23:33'
            ],
            (object) [
                'id' => 3, 'name' => 'Test 3', 'email' => 'email3@example.com', 'roles' => 'staff',
                'created_at' => '2021-03-01 12:23:33'
            ],
            (object) [
                'id' => 4, 'name' => 'Test 4', 'email' => 'email4@example.com', 'roles' => 'member',
                'created_at' => '2021-07-01 12:23:33'
            ],
            (object) [
                'id' => 5, 'name' => 'Test 5', 'email' => 'email5@example.com', 'roles' => 'member',
                'created_at' => '2021-04-25 12:23:33'
            ],
            (object) [
                'id' => 6, 'name' => 'Test 6', 'email' => 'email6@example.com', 'roles' => 'staff',
                'created_at' => '2021-02-03 12:23:33'
            ],
            (object) [
                'id' => 7, 'name' => 'Test 7', 'email' => 'email7@example.com', 'roles' => 'admin',
                'created_at' => '2021-02-01 12:23:33'
            ],
            (object) [
                'id' => 8, 'name' => 'Test 8', 'email' => 'email8@example.com', 'roles' => 'staff',
                'created_at' => '2021-04-12 12:23:33'
            ],
            (object) [
                'id' => 9, 'name' => 'Test 6', 'email' => 'email6@example.com', 'roles' => 'staff',
                'created_at' => '2021-02-03 12:23:33'
            ],
            (object) [
                'id' => 10, 'name' => 'Test 7', 'email' => 'email7@example.com', 'roles' => 'admin',
                'created_at' => '2021-02-01 12:23:33'
            ],
            (object) [
                'id' => 11, 'name' => 'Test 8', 'email' => 'email8@example.com', 'roles' => 'staff',
                'created_at' => '2021-04-12 12:23:33'
            ],
            (object) [
                'id' => 12, 'name' => 'Test 8', 'email' => 'email8@example.com', 'roles' => 'staff',
                'created_at' => '2021-04-12 12:23:33'
            ],
        ];

        // 1. filter results with any filters if available
        if ($this->search) {
            $rows = array_filter($rows, function ($row) {
                return str_contains(strtolower($row->name), strtolower($this->search));
            });
        }

        if (!empty($this->filterVals['roles'])) {
            $rows = array_filter($rows, function ($row) {
                return str_contains(strtolower($row->roles), strtolower($this->filterVals['roles']));
            });
        }

        return $rows;
    }

    public function export(): StreamedResponse
    {
        return $this->exportCsv($this->rows(), 'tableArray.csv');
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
