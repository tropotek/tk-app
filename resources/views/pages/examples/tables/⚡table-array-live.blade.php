<?php

use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Table\Column;
use Tk\Table\Traits\IsLivewire;
use Tk\Table\Traits\WithSearch;

new #[Layout('pages.main')]
class extends Component {

    use WithPagination, IsLivewire, WithSearch;

    #[Url(except: '')]
    public $roles = '';


    public function boot()
    {
        Breadcrumbs::push('Table Array (Livewire)');

//        $this->setDefaultLimit(2);
//        $this->setDefaultSort('email');

        $this->appendColumn(new Column(
            name: 'name',
            sortable: true,
        ));

        $this->appendColumn(new Column(
            name: 'email',
            sortable: true,
        ));

        $this->appendColumn(new Column(
            name: 'roles',
            sortable: false,
        ));

        // alt method to add columns
        $this->appendColumn('created_at')
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
