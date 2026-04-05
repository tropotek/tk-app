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
use Tk\Tbl\Cell;
use Tk\Tbl\IsLivewireTable;

new #[Layout('pages.main')]
class extends Component {

    use WithPagination, IsLivewireTable;

    #[Url(except: '')]
    public $search = '';

    #[Url(except: '')]
    public $status = '';

    public function boot()
    {
        Breadcrumbs::push('Ideas');

        $this->appendCell(new Cell('title'))
            ->setSortable()
            ->addClass('fw-bold')
            ->setHtml(function (Idea $idea, $cell) {
                return sprintf('<a href="%s">%s</a>', route('examples.ideas.edit', $idea->id), $cell->text($idea));
            });

        $this->appendCell(new Cell('status'))
            ->setSortable()
            ->setText(function (Idea $idea, $cell) {
                return $idea->status->label();
            });

        $this->appendCell(new Cell('created_at'))
            ->setHeader('Created')
            ->setSortable();

        $this->appendCell(new Cell('updated_at'))
            ->setHeader('Updated')
            ->setSortable();
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return Idea::query()
            ->when($this->search, function (Builder $builder) {
                $str = preg_replace("/[^a-zA-Z0-9' -]/", " ", $this->search);
                return $builder->where('title', 'like', "%{$str}%")
                    ->orWhere('description', 'like', "%{$str}%")
                    ->tap($this->resetPage() ?? fn() => null);
            })
            ->when($this->status, fn(Builder $query) => $query->where('status', $this->status))
            ->orderBy($this->safeSort(), $this->dir)
            ->paginate($this->limit);
    }

};
?>

<div>
    <h1>{{ $pageName }}</h1>

    <x-tkl-ui::tbl.livewire.filters :table="$this">
        <x-slot name="filters">
            <x-tkl-ui::tbl.livewire.filters.select
                wire:model.live="status"
                :name="$this->tableKey('status')"
                :options="[ '' => '- All Statuses -'] + IdeaStatus::getLabels()"
                value="{{ $this->status }}"
            />
        </x-slot>

        <x-slot name="actions">
            <div class="p-2 ps-0">
                <a href="{{ route('examples.ideas.create') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-plus-circle"></i> Create
                </a>
            </div>
        </x-slot>
    </x-tkl-ui::tbl.livewire.filters>

    <x-tkl-ui::tbl.livewire :table="$this"/>

</div>
