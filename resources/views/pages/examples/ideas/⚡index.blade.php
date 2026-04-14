<?php

use App\Enum\IdeaStatus;
use App\Models\Idea;
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

new #[Layout('pages.main')]
class extends \Tk\Table\TableComponent {

    use WithPagination;

    #[Url(except: '')]
    public $status = '';

    public function booted(): void
    {
        Breadcrumbs::push('Ideas');

        $this->appendColumn('title')
            ->setSortable()
            ->addClass('fw-bold')
            ->setView(function (Idea $idea, Column $column) {
                return view('tkl-ui::components.table.columns.a', [
                    'href' => route('examples.ideas.edit', $idea->id),
                    'text' => $column->value($idea)
                ]);
            });

        $this->appendColumn('status')
            ->setSortable()
            ->setValue(function (Idea $idea, $column) {
                return $idea->status->label();
            });

        $this->appendColumn('created_at')
            ->setHeader('Created')
            ->setSortable();

        $this->appendColumn('updated_at')
            ->setHeader('Updated')
            ->setSortable();

        $this->appendFilter('status')
            ->setOptions(IdeaStatus::getLabels());
    }

    #[Computed]
    public function rows(): array|Builder
    {
        return Idea::query()
            ->when($this->search ?? null, function (Builder $builder) {
                $str = preg_replace("/[^a-zA-Z0-9' -]/", " ", $this->search);
                return $builder->where('title', 'like', "%{$str}%")
                    ->orWhere('description', 'like', "%{$str}%")
                    ->tap($this->resetPage() ?? fn() => null);
            })
            ->when($this->filterVals['status'] ?? null,
                fn(Builder $query) => $query->where('status', $this->filterVals['status']));
    }

    public function export(): StreamedResponse
    {
        return $this->exportCsv($this->rows()->get(), 'ideas.csv');
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
    </x-tkl-ui::table.tk-filters>
    <x-tkl-ui::table :table="$this"/>

</div>
