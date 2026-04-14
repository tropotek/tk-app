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

        $rows = session()->get('_side-table-cache');

        if (!$rows) {
            $faker = \Faker\Factory::create();
            $rows = array_map(fn($i) => [
                'id' => $i,
                'name' => $faker->company(),
                'email' => $faker->email(),
                'roles' => $faker->randomElement(['admin', 'staff', 'member']),
                'created_at' => $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d H:i:s'),
            ], range(1, 50));
            session()->put('_side-table-cache', $rows);
        }

        // 1. filter results with any filters if available
        if ($this->search) {
            $rows = array_filter($rows, function ($row) {
                return str_contains(strtolower($row['name']), strtolower($this->search));
            });
        }

        if (!empty($this->filterVals['roles'])) {
            $rows = array_filter($rows, function ($row) {
                return str_contains(strtolower($row['roles']), strtolower($this->filterVals['roles']));
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
