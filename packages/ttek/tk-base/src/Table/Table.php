<?php

namespace Tk\Table;

use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Notifications\Action;
use Illuminate\Support\Collection;

class Table
{

    const string PARAM_LIMIT    = 'limit';
    const string PARAM_OFFSET   = 'offset';
    const string PARAM_PAGE     = 'page';
    const string PARAM_TOTAL    = 'total';
    const string PARAM_ORDERBY  = 'orderBy';

    protected string     $id        = '';
    protected int        $limit     = 50;
    protected int        $page      = 1;
    protected string     $orderBy   = '';
    protected Collection $cells;
    protected Collection $actions;

    // cached rows
    private ?array $rows = null;


    public function __construct(string $id = 'thetable')
    {
        $this->setId($id);
        $this->page = (int)request()->input($this->makeIdKey(self::PARAM_PAGE), $this->page);
        $this->limit = (int)request()->input($this->makeIdKey(self::PARAM_LIMIT), $this->limit);
        $this->orderBy = (int)request()->input($this->makeIdKey(self::PARAM_ORDERBY), $this->orderBy);

        $this->cells = new Collection();
        $this->actions = new Collection();

        $this->build();
    }


    /**
     * Override this method in your tables to build a query
     */
    protected function build(): void { }

    /**
     * Override this method in your tables to build an SQL query
     */
    protected function query(array $filters = []): ?BuilderContract { return null; }

    /**
     * Override this method if you want to create the rows array yourself
     * @return array
     */
    public function getRows(array $filters = []): array
    {
        // if no rows attempt to build an SQL query
        if (is_null($this->rows)) {
            $this->rows = $this->query($filters)?->get()->all() ?? [];
        }
        return $this->rows;
    }


    /**
     * Manually set the table rows to display
     */
    public function setRows(?array $rows): Table
    {
        $this->rows = $rows;
        return $this;
    }

    /**
     * ensure the id is unique
     */
    protected function setId(string $id): static
    {
        static $instances = [];
        if ($this->getId()) return $this;
        if (isset($instances[$id])) {
            $instances[$id]++;
        } else {
            $instances[$id] = 0;
        }
        if ($instances[$id] > 0) $id = $instances[$id].$id;
        $this->id = $id;
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
        $this->orderBy = strval(preg_replace("/(\r|\n)/", '', trim($orderBy)));
        return $this;
    }

    public function getOrderBySql(): string
    {
        $orders = explode(',', $this->getOrderBy());
        $orders = array_map('trim', $orders);

        $sql = [];
        foreach ($orders as $order) {
            $order = trim($order);
            if ($order[0] == '-') {     // descending
                $col = substr($order, 1);
                $order = "$col DESC";
            }
            $sql[] = trim($order);
        }
        return implode(', ', $sql);
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
        $this->cells->forget($name);
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
     * sort array of objects by primary and optional second and third columns
     * $col1, $col2, and $col3 contain column names (properties) in the rows
     * prefix column names with '-' for descending sort
     * returns sorted array
     *
     * @template K of string|int
     * @template T of mixed
     * @param array<K, T> $rows
     * @return array<K, T>
     * @todo Sorts current rows array, may not need that, should be a helper function somewhere???
     */
    public static function sortRows(array $rows, string ...$columns): array
    {
        if (count($rows) < 2) return $rows;

        // generalized comparison function for sorting two values
        $compare = fn(mixed $a, mixed $b): int => match(true) {
            is_null($a) && is_null($b) => 0,
            // nulls always sort after non-nulls
            is_null($a) => -1,
            is_null($b) => 1,
            is_numeric($a) && is_numeric($b) => $a <=> $b,
            ($a instanceof \BackedEnum) && ($b instanceof \BackedEnum) => $a->value <=> $b->value,
            // DateTime and DateTimeImmutable objects support comparison operators
            // ($a instanceof \DateTimeInterface) && ($b instanceof \DateTimeInterface) => $a <=> $b,
            // sortable objects must support string conversion (Stringable interface and __toString method)
            // string sort case-insensitive
            default => strcasecmp(strval($a), strval($b)),
        };

        // determine ascending/descending and eliminate redundant sorts
        $cols = [];
        foreach (array_reverse($columns) as $col) {
            $desc = false;
            if (empty($col)) continue;
            if ($col[0] == '-') {
                $desc = true;
                $col = substr($col, 1);
            } elseif ($col[0] == '+') {
                $col = substr($col, 1);
            }

            $cols[$col] = $desc;
        }

        // sort rows from last-level sort to top-level sort
        // relies on PHP 8 stable sorting
        foreach ($cols as $col => $desc) {
            if ($desc) {
                usort($rows, fn($l, $r) => $compare(self::getOrderVal($r, $col) ?? null, self::getOrderVal($l, $col) ?? null));
            } else {
                usort($rows, fn($l, $r) => $compare(self::getOrderVal($l, $col) ?? null, self::getOrderVal($r, $col) ?? null));
            }
        }

        return $rows;
    }

    /**
     * @param array<string,mixed>|object $row
     */
    private static function getOrderVal(array|object $row, string $col): mixed
    {
        if (is_array($row)) {
            return $row[$col] ?? null;
        } elseif (isset($row->{$col})) {
            return $row->{$col} ?? null;
        } elseif (method_exists($row, $col)) {
            return $row->$col() ?? null;
        }
        return null;
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
