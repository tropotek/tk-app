<?php

namespace Tk\Livewire\Table;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use Tk\Livewire\Table\Records\RecordsInterface;
use Tk\Traits\HasAttributes;

abstract class Table extends Component
{
    use WithPagination;
    use HasAttributes;

    const string DEFAULT_ID = 'table';

    protected string $paginationTheme = 'bootstrap';

    // Table query properties
    #const string QUERY_ID       = '_tid';   // reserved param for table queries
    #const string QUERY_RESET    = 'tr_';
    const string QUERY_LIMIT = 'tl_';
    const string QUERY_PAGE = 'tp_';
    const string QUERY_SORT = 'ts_';
    // sort direction
    const string QUERY_DIR = 'td_';

    public string $id = self::DEFAULT_ID;
    public int $limit = 50;
    //public int $page = 1;
    public string $sort = '';
    public string $dir = '';

    public bool $showPaginator = true;
    protected Collection $cells;
    protected RecordsInterface $records;
    protected mixed $rowAttrs = null;

    /**
     * Add your cells and query/array results here
     */
    abstract public function boot(): void;

    public function getQueryString(): array
    {
        return [
            #$this->key(self::QUERY_RESET) => ['except' => ''],
            $this->key(self::QUERY_LIMIT) => ['except' => ['', 0]],
            $this->key(self::QUERY_PAGE) => ['except' => ['', 1]],
            $this->key(self::QUERY_SORT) => ['except' => ''],
            $this->key(self::QUERY_DIR) => ['except' => ''],
        ];
    }

    public function getRecords(): RecordsInterface
    {
        if (!isset($this->records)) {
            throw new \Exception("Cannot access records before they are set.");
        }
        return $this->records;
    }

    public function setRecords(RecordsInterface $records): RecordsInterface
    {
        $this->records = $records;
        $records->setTable($this);
        return $records;
    }

    /**
     * return the number of records on the page
     */
    public function count(): int
    {
        return $this->getRecords()->count();
    }

    /**
     * return the total number of records available
     */
    public function countAll(): int
    {
        return $this->getRecords()->countAll();
    }

    public function hasRecords(): bool
    {
        return isset($this->records) && $this->count() > 0;
    }

    public function getPaginator(): ?AbstractPaginator
    {
        if (!$this->hasRecords()) {
            return null;
        }
        return $this->getRecords()->getPaginator();
    }

    public function getRowAttrs(object|array|null $row = null): array
    {
        if (is_callable($this->rowAttrs) && !is_null($row)) {
            return ($this->rowAttrs)($row, $this) ?? [];
        }
        return $this->rowAttrs ?? [];
    }

    /**
     * @callable function (mixed $row, Table $table):array { return ['class' => 'test']; }
     */
    public function addRowAttrs(callable|array|null $rowAttrs): static
    {
        $this->rowAttrs = $rowAttrs;
        return $this;
    }

    public function getCell(string $name): ?Cell
    {
        /** @var Cell $cell */
        $cell = $this->cells->get($name);
        return $cell;
    }

    public function removeCell(string $name): static
    {
        $this->cells->forget([$name]);
        return $this;
    }

    /**
     * @return Collection<int,Cell>
     */
    public function getCells(object|array|null $row = null): Collection
    {
        if (!is_null($row)) {
            foreach ($this->cells as $cell) {
                $cell->setRow($row);
            }
        }
        return $this->cells;
    }

    public function appendCell(string|Cell $cell, ?string $after = null): Cell
    {
        if (is_string($cell)) {
            $cell = new Cell($cell);
        }
        if (empty($this->cells)) {
            $this->cells = collect();
        }

        if ($this->getCells()->has($cell->getName())) {
            throw new \Exception("Cell with name '{$cell->getName()}' already exists.");
        }
        $cell->setTable($this);

        if ($after) {
            $idx = $this->cells->keys()->search($after);
            // If the key is not found, add at the end
            $idx = ($idx === false) ? $this->cells->count() - 1 : $idx;

            $this->cells = $this->cells->slice(0, $idx + 1)
                ->merge([$cell->getName() => $cell])
                ->merge($this->cells->slice($idx + 1));
        } else {
            $this->cells->put($cell->getName(), $cell);
        }

        return $cell;
    }

    public function prependCell(string|Cell $cell, ?string $before = null): Cell
    {
        if (is_string($cell)) {
            $cell = new Cell($cell);
        }
        if (empty($this->cells)) {
            $this->cells = collect();
        }

        if ($this->getCells()->has($cell->getName())) {
            throw new \Exception("Cell with name '{$cell->getName()}' already exists.");
        }
        $cell->setTable($this);

        if ($before) {
            $idx = $this->cells->keys()->search($before);
            // If the key is not found, add at the beginning
            $idx = ($idx === false) ? 0 : $idx;

            $this->cells = $this->cells->splice($idx, 0, [$cell->getName() => $cell]);
        } else {
            $this->cells->prepend($cell, $cell->getName());
        }

        return $cell;
    }

    public function render()
    {
        return view('tkl-ui::livewire.table.index');
    }

    /**
     * Create a key from the table id
     * If the table is using the default id, return the key unchanged
     */
    public function key(string $key): string
    {
        if ($this->id == self::DEFAULT_ID) {
            return $key;
        }
        return self::makeKey($key, $this->id);
    }

    /**
     * Create a table-specific key using the table id
     * returns: `{$tableId}_{$key}`
     */
    public static function makeKey(string $key, string $tableId = ''): string
    {
        if (str_starts_with($key, $tableId.'_')) {
            return $key;
        }
        return $tableId.'_'.$key;
    }
}
