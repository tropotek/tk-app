<?php

namespace Tk\Table;

use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Notifications\Action;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Tk\Table\Records\RecordsInterface;

class Table
{

    const string PARAM_LIMIT    = 'l';
    const string PARAM_OFFSET   = 'o';
    const string PARAM_PAGE     = 'p';
    const string PARAM_TOTAL    = 't';
    const string PARAM_ORDERBY  = 'ob';

    protected string     $id        = '';
    protected int        $limit     = 50;
    protected int        $page      = 1;
    protected string     $orderBy   = '';
    protected Collection $cells;
    protected Collection $actions;
    protected RecordsInterface $records;
    private ?BuilderContract $queryBuilder = null;
    private ?array $rows = null;        // cached rows


    public function __construct(string $id = 't', $defaultOrderBy = '', $defaultLimit = 50)
    {
        $this->cells = new Collection();
        $this->actions = new Collection();
        $this->setId($id);

        // TODO: how do we handle the defaults?
        // TODO: Should this me moved to a parent class?
        // TODO: what if we want session params?
        /*
         * TODO Add a configurable enabled middleware object to to hide table params
         *      Then we can check for session vars if exists, use them or query string
         *      Might be a good place to start thinking about the table sessions, user
         *      table state saving and such...
         */
        $this->setPage((int)request()->input($this->makeIdKey(self::PARAM_PAGE), $this->page));
        $this->setLimit((int)request()->input($this->makeIdKey(self::PARAM_LIMIT), $this->limit));
        $this->setOrderBy(request()->input($this->makeIdKey(self::PARAM_ORDERBY), $this->orderBy));

        $this->build();
    }


    /**
     * Override this method in your tables to build a query
     * This is where you add the table columns and actions
     */
    protected function build(): void { }

    /**
     * Override this method in your table to build an SQL query
     */
    protected function query(array $filters = []): ?BuilderContract { return null; }

    /**
     * Override this method if you want to create the rows array manually
     * @return array
     */
    public function getRows(array $filters = []): array
    {
        // if no rows attempt to build an SQL query
        if (is_null($this->rows)) {
            if (is_null($this->queryBuilder)) {
                $this->queryBuilder = $this->query($filters);
                $this->fillQuery();
            }
            $this->rows = $this->queryBuilder?->get()->all() ?? [];
        }
        return $this->rows;
    }

    /**
     * Manually set the table rows to display
     */
    public function setRows(?array $rows): static
    {
        $this->rows = $rows;
        return $this;
    }

    public function getRecords(): RecordsInterface
    {
        return $this->records;
    }

    public function setRecords(RecordsInterface $records): static
    {
        $this->records = $records;
        return $this;
    }

    /**
     * fill a query builder object with the table
     * orderBy, limit and page values
     */
    protected function fillQuery(): ?BuilderContract
    {
        if (is_null($this->getQuery())) return null;
        $this->getQuery()->forPage($this->getPage(), $this->getLimit());

        // Add orderBy
        $orders = explode(',', $this->getOrderBy());
        $orders = array_filter(array_map('trim', $orders));

        foreach ($orders as $order) {
            $dir = 'asc';
            if ($order[0] == '-') {     // descending
                $order = substr($order, 1);
                $dir = 'desc';
            }
            $this->getQuery()->orderBy($order, $dir);
        }

        return $this->getQuery();
    }

    public function getQuery(): ?BuilderContract
    {
        return $this->queryBuilder;
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

    public function getCell(string $name): ?Cell
    {
        return $this->cells->get($name);
    }

    public function removeCell(string $name): static
    {
        $this->cells->forget([$name]);
        return $this;
    }

    /**
     * @return Collection<int,Cell>
     */
    public function getCells(): Collection
    {
        return $this->cells;
    }

    /**
     * @return Collection<int,Action>
     */
    public function getActions(): Collection
    {
        return $this->actions;
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
     * Create a table specific key using the table id
     * returns: `{id}_{$key}`
     */
    public function makeIdKey(string $key, string $dash = '_'): string
    {
        return $this->getId() . $dash . $key;
    }
}
