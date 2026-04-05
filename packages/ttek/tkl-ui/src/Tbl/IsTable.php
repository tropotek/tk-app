<?php

namespace Tk\Tbl;

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
    public int $tableLimit = 30;
    public string $tableSort = '';
    public string $tableDir = 'asc';

    protected Collection $tableCells;


    abstract public function rows(): LengthAwarePaginator;

    /**
     * Livewire function
     */
    protected function hydrateTableFromRequest(): void
    {
        $this->tableLimit = request()->input($this->tableKey('limit'), $this->tableLimit);
        $this->tableSort = request()->input($this->tableKey('sort'), $this->tableSort);
        $this->tableDir = request()->input($this->tableKey('dir'), $this->tableDir);
    }

    /**
     * Livewire method
     */
    public function toggleDir(): void
    {
        // TODO: change to 3 way sort
        $this->tableDir = $this->tableDir === 'asc' ? 'desc' : 'asc';
    }

    /**
     * Return a validated sort column
     */
    protected function safeSort(): string
    {
        $sortables = $this->tableCells->pluck('tableSort')->filter()->all();

        // TODO: validate sort options with cell `sort` property

        return $this->tableSort;
    }

    /**
     * @return Collection<int,Cell>
     */
    public function getCells(): Collection
    {
        return $this->tableCells;
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
        if (empty($this->tableCells)) {
            $this->tableCells = collect();
        }

        if ($this->getCells()->has($cell->name)) {
            throw new \Exception("Cell with name '{$cell->name}' already exists.");
        }
        $cell->setTable($this);

        if ($after) {
            $idx = $this->getCells()->keys()->search($after);
            // If the key is not found, add at the end
            $idx = ($idx === false) ? $this->getCells()->count() - 1 : $idx;

            $this->tableCells = $this->getCells()->slice(0, $idx + 1)
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
            $this->tableCells = collect();
        }

        if ($this->getCells()->has($cell->name)) {
            throw new \Exception("Cell with name '{$cell->name}' already exists.");
        }
        $cell->setTable($this);

        if ($before) {
            $idx = $this->getCells()->keys()->search($before);
            // If the key is not found, add at the beginning
            $idx = ($idx === false) ? 0 : $idx;

            $this->tableCells = $this->getCells()->splice($idx, 0, [$cell->name => $cell]);
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
        $perPage = $this->tableLimit;
        $pageName = $this->tableKey($pageName);

        $offset = ($currentPage - 1) * $this->tableLimit;
        $items = array_slice($rows, $offset, $this->tableLimit);

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
     * returns sorted array
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
