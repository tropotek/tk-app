<?php

use App\Enum\IdeaStatus;
use App\Models\Idea;
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

    #[Url(except: '')]
    public $status = '';

    public function boot()
    {
        Breadcrumbs::push('Ideas');

        $this->appendCell('title')
            ->setSortable()
            ->addClass('fw-bold')
            ->setView(function (Idea $idea, Cell $cell) {
                return view('tkl-ui::components.table.cells.a', [
                    'href' => route('examples.ideas.edit', $idea->id),
                    'value' => $cell->value($idea)
                ]);
            });

        $this->appendCell('status')
            ->setSortable()
            ->setValue(function (Idea $idea, $cell) {
                return $idea->status->label();
            });

        $this->appendCell('created_at')
            ->setHeader('Created')
            ->setSortable();

        $this->appendCell('updated_at')
            ->setHeader('Updated')
            ->setSortable();

        $this->appendFilter('status')
            ->setOptions(IdeaStatus::getLabels());

    }

    #[Computed]
    protected function rows(): array|Builder
    {
        return Idea::query()
            ->when($this->search, function (Builder $builder) {
                $str = preg_replace("/[^a-zA-Z0-9' -]/", " ", $this->search);
                return $builder->where('title', 'like', "%{$str}%")
                    ->orWhere('description', 'like', "%{$str}%")
                    ->tap($this->resetPage() ?? fn() => null);
            })
            ->when($this->filterVals['status'] ?? null, fn(Builder $query) => $query->where('status', $this->filterVals['status']));
    }

    public function csv()
    {
        return $this->buildCsv($this->query(), 'ideas.csv');
    }

};
?>

<div>
    <h1>{{ $pageName }}</h1>

    <x-tkl-ui::table.tk-filters :table="$this">
        <x-slot name="actions">
            <div class="p-2 ps-0">
                <a href="{{ route('examples.ideas.create') }}" class="btn btn-sm btn-outline-secondary">
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
