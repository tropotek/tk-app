<?php

namespace Tk\Table;

use Illuminate\Notifications\Action;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Tk\Table\Records\RecordsInterface;

class Table
{

    const string QUERY_LIMIT    = 'tl';
    const string QUERY_PAGE     = 'tp';
    const string QUERY_ORDER    = 'to';

    protected string     $id        = '';
    protected int        $limit     = 50;
    protected int        $page      = 1;
    protected string     $orderBy   = '';
    protected Collection $cells;
    protected Collection $actions;
    protected RecordsInterface $records;
    protected mixed      $rowAttrs = null;


    public function __construct(string $id = 't')
    {
        $this->cells = new Collection();
        $this->actions = new Collection();
        $this->setId($id);

        $this->build();
    }

    /**
     * The pace to update the table from the request or session.
     * Called after the RecordsInterface is set.
     */
    protected function refreshState(): void
    {
        // TODO: how do we handle the defaults?
        // TODO: Should this me moved to a parent class?
        // TODO: what if we want session params?
        /*
         * TODO Add a configurable enabled middleware object to to hide table params
         *      Then we can check for session vars if exists, use them or query string
         *      Might be a good place to start thinking about the table sessions, user
         *      table state saving and such...
         */
        $this->setPage((int)request()->input($this->makeIdKey(self::QUERY_PAGE), $this->page));
        $this->setLimit((int)request()->input($this->makeIdKey(self::QUERY_LIMIT), $this->limit));
        $this->setOrderBy((string)request()->input($this->makeIdKey(self::QUERY_ORDER), $this->orderBy));
    }

    /**
     * Override this method in your parent table objects to build the columns
     */
    protected function build(): void { }

    public function getRecords(): RecordsInterface
    {
        if (!isset($this->records)) {
            throw new \Exception("Cannot access records before they are set.");
        }
        return $this->records;
    }

    public function setRecords(RecordsInterface $records): static
    {
        $this->records = $records;
        $records->setTable($this);
        $this->refreshState();
        return $this;
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
        if (!$this->hasRecords()) return null;
        return $this->getRecords()->getPaginator();
    }

    /**
     * ensure the id is unique if duplicates exist
     */
    protected function setId(string $id): static
    {
        static $instances = [];
        if ($this->getId()) return $this;
        $instances[$id] = isset($instances[$id]) ? $instances[$id]++ : 0;
        $this->id = ($instances[$id] > 0) ? $instances[$id].$id : $id;
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    public function setOrderBy(string $orderBy): static
    {
        $this->orderBy = trim($orderBy);
        // validate orderBy string
        if (!empty($this->orderBy) && !preg_match('/^[a-z0-9_-]+$/i', $this->orderBy)) {
            Log::warning("Invalid orderBy value: " . trim($this->orderBy));
            $this->orderBy = '';
        }
        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): static
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @callable function (mixed $row, Table $table):array { return ['class' => 'test']; }
     */
    public function getRowAttrs(object|array|null $row = null): array
    {
        if (is_callable($this->rowAttrs)) {
            return ($this->rowAttrs)($row, $this) ?? [];
        }
        return $this->rowAttrs ?? [];
    }

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

    /**
     * @return Collection<int,Action>
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }







    /**
     * Create a table specific key using the table id
     * returns: `{id}_{$key}`
     */
    public function makeIdKey(string $key, string $dash = '_'): string
    {
        return $this->getId() . $dash . $key;
    }
}
