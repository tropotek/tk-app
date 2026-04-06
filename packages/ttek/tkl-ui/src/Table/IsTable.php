<?php

namespace Tk\Table;

use Illuminate\Pagination\Paginator;
use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Use this trait to when adding a table to a controller
 */
trait IsTable
{

    public string $tableId = 'tbl';
    public int $limit = 30;
    public string $sort = '';
    public string $dir = 'asc';

    protected int $defaultLimit = 30;
    protected string $defaultSort = '';
    protected string $defaultDir = 'asc';

    protected Collection $cells;


    abstract public function rows(): LengthAwarePaginator;

    /**
     * Controller function
     */
    protected function hydrateTableFromRequest(): void
    {
        $this->limit = request()->input($this->tableKey('limit'), $this->defaultLimit);
        $this->sort = request()->input($this->tableKey('sort'), $this->defaultSort);
        $this->dir = request()->input($this->tableKey('dir'), $this->defaultDir);
    }

    /**
     * Return a validated sort column
     */
    protected function safeSort(): string
    {
        $sortables = $this->cells->pluck('sort')->filter()->all();
        if (!in_array($this->sort, $sortables)) {
            return $this->defaultSort;
        }
        return $this->sort;
    }

    public function setDefaultLimit(int $limit): static
    {
        $this->defaultLimit = $limit;
        $this->limit = $limit;
        return $this;
    }

    public function setDefaultSort(string $sort, string $dir = 'asc'): static
    {
        $this->defaultSort = $sort;
        $this->defaultDir = $dir;
        $this->sort = $sort;
        $this->dir = $dir;
        return $this;
    }

    /**
     * @return Collection<int,Cell>
     */
    public function getCells(): Collection
    {
        return $this->cells;
    }

    protected function getCell(string $name): ?Cell
    {
        /** @var Cell $cell */
        $cell = $this->getCells()->get($name);
        return $cell;
    }

    protected function removeCell(string $name): static
    {
        $this->getCells()->forget([$name]);
        return $this;
    }

    protected function appendCell(string|Cell $cell, ?string $after = null): Cell
    {
        if (is_string($cell)) {
            $cell = new Cell($cell);
        }
        if (empty($this->cells)) {
            $this->cells = collect();
        }

        if ($this->getCells()->has($cell->name)) {
            throw new \Exception("Cell with name '{$cell->name}' already exists.");
        }
        $cell->setTable($this);

        if ($after) {
            $idx = $this->getCells()->keys()->search($after);
            // If the key is not found, add at the end
            $idx = ($idx === false) ? $this->getCells()->count() - 1 : $idx;

            $this->cells = $this->getCells()->slice(0, $idx + 1)
                ->merge([$cell->name => $cell])
                ->merge($this->getCells()->slice($idx + 1));
        } else {
            $this->getCells()->put($cell->name, $cell);
        }

        return $cell;
    }

    protected function prependCell(string|Cell $cell, ?string $before = null): Cell
    {
        if (is_string($cell)) {
            $cell = new Cell($cell);
        }
        if (empty($this->getCells())) {
            $this->cells = collect();
        }

        if ($this->getCells()->has($cell->name)) {
            throw new \Exception("Cell with name '{$cell->name}' already exists.");
        }
        $cell->setTable($this);

        if ($before) {
            $idx = $this->getCells()->keys()->search($before);
            // If the key is not found, add at the beginning
            $idx = ($idx === false) ? 0 : $idx;

            $this->cells = $this->getCells()->splice($idx, 0, [$cell->name => $cell]);
        } else {
            $this->getCells()->prepend($cell, $cell->name);
        }

        return $cell;
    }

    /**
     * Create a key from the table id
     * If the table is using the default id, return the key unchanged
     */
    public function tableKey(string $key): string
    {
        return self::makeTableKey($key, $this->tableId);
    }

    /**
     * Create a table-specific key using the table id
     * returns: `{$tableId}_{$key}`
     */
    public static function makeTableKey(string $key, string $tableId = ''): string
    {
        if (str_starts_with($key, $tableId.'_')) {
            return $key;
        }
        return $tableId.'_'.$key;
    }

    /**
     * return the paginated results of an array of rows
     */
    protected function paginateArray(array $rows, $pageName = 'page', $currentPage = null): LengthAwarePaginator
    {
        $currentPage = $currentPage ?: Paginator::resolveCurrentPage($pageName);
        $total = count($rows);
        $perPage = $this->limit ?: $total;
        $pageName = $this->tableKey($pageName);

        if ($this->limit > 0) {
            $offset = ($currentPage - 1) * $this->limit;
            $items = array_slice($rows, $offset, $this->limit);
        } else {
            $items = $rows;
        }

        $options = [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ];

        return Container::getInstance()->makeWith(
            LengthAwarePaginator::class,
            compact('items', 'total', 'perPage', 'currentPage', 'options')
        );
    }


    // TODO: review the below array sort methods

    /**
     * sort array of objects by primary and optional second and third columns
     * $col1, $col2, and $col3 contain column names (properties) in the rows
     * prefix column names with '-' for descending sort
     * returns a sorted array
     *
     * @template K of string|int
     * @template T of mixed
     * @param array<K, T> $rows
     * @return array<K, T>
     */
    protected function sortRows(array $rows, string ...$columns): array
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
                usort($rows, fn($l, $r) => $compare($this->getOrderVal($r, $col) ?? null, $this->getOrderVal($l, $col) ?? null));
            } else {
                usort($rows, fn($l, $r) => $compare($this->getOrderVal($l, $col) ?? null, $this->getOrderVal($r, $col) ?? null));
            }
        }

        return $rows;
    }

    /**
     * @param array<string,mixed>|object $row
     */
    protected function getOrderVal(array|object $row, string $col): mixed
    {
        if (is_array($row)) {
            return $row[$col] ?? null;
        }
        return $row->{$col} ?? null;
    }

}
