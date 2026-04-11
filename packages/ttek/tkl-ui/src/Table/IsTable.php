<?php

namespace Tk\Table;

use Illuminate\Pagination\Paginator;
use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 * Use this trait to when adding a table to a controller
 */
trait IsTable
{

    public string $tableId = 'tbl';
    public int $limit = 30;
    public string $sort = '';
    public string $dir = 'asc';
    public ?int $totalRows = null;

    protected int $defaultLimit = 30;
    protected string $defaultSort = '';
    protected string $defaultDir = 'asc';
    /**
     * @var Collection<string, Cell>
     */
    protected Collection $cells;


    abstract public function rows(): LengthAwarePaginator;


    /**
     * Controller function
     */
    public function hydrateTableFromRequest(): void
    {
        $this->limit = request()->input($this->tableKey('limit'), $this->defaultLimit);
        $this->sort = request()->input($this->tableKey('sort'), $this->defaultSort);
        $this->dir = request()->input($this->tableKey('dir'), $this->defaultDir);
    }

    /**
     * Return a validated sort column
     */
    public function safeSort(): string
    {
        return in_array($this->sort, $this->sortableKeys())
            ? $this->sort
            : $this->defaultSort;
    }

    /**
     * Allowed sort keys. Defaults to all sortable cell keys.
     */
    public function sortableKeys(): array
    {
        return $this->getCells()
            ->pluck('sort')
            ->filter()
            ->all();
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
        return $this->cells ??= collect();
    }

    public function getCell(string $name): ?Cell
    {
        return $this->getCells()->get($name);
    }

    public function removeCell(string $name): static
    {
        $this->getCells()->forget([$name]);
        return $this;
    }

    public function appendCell(string|Cell $cell, ?string $after = null): Cell
    {
        $cell = is_string($cell) ? new Cell($cell) : $cell;
        $cell->setTable($this);

        if ($this->getCells()->has($cell->name)) {
            throw new \Exception("Cell with name '{$cell->name}' already exists.");
        }

        if (is_null($after)) {
            $this->getCells()->put($cell->name, $cell);
            return $cell;
        }

        $index = $this->getCells()->keys()->search($after);
        if ($index === false) {
            $this->getCells()->put($cell->name, $cell);
            return $cell;
        }

        $this->cells = $this->insertAt($this->getCells(), $index+1, $cell);

        return $cell;
    }

    public function prependCell(string|Cell $cell, ?string $before = null): Cell
    {
        $cell = is_string($cell) ? new Cell($cell) : $cell;
        $cell->setTable($this);

        if ($this->getCells()->has($cell->name)) {
            throw new \Exception("Cell with name '{$cell->name}' already exists.");
        }

        if (is_null($before)) {
            $this->getCells()->prepend($cell, $cell->name);
            return $cell;
        }

        $index = $this->getCells()->keys()->search($before);
        if ($index === false) {
            $this->getCells()->prepend($cell, $cell->name);
            return $cell;
        }

        $this->cells = $this->insertAt($this->getCells(), $index, $cell);

        return $cell;
    }

    /**
     * Collection helper function to insert items at a specific index
     */
    private function insertAt(Collection $col, int $index, Cell $cell): Collection
    {
        $before = $col->slice(0, $index);
        $after = $col->slice($index);

        return $before
            ->put($cell->name, $cell)
            ->merge($after);
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

    public function paginateQuery(Builder $query): LengthAwarePaginator
    {
        $path = '/' . ltrim(Paginator::resolveCurrentPath(), '/');
        return $query->paginate(
            perPage: $this->limit ?: $this->totalRows,
            pageName: $this->tableKey('p'),
        )->withPath($path);
    }

    /**
     * return the paginated results of an array of rows
     */
    public function paginateArray(array $rows, $pageName = 'page', $currentPage = null): LengthAwarePaginator
    {
        $currentPage = $currentPage ?: Paginator::resolveCurrentPage($pageName);
        $total = count($rows);
        $perPage = $this->limit ?: $total;
        $pageName = $this->tableKey($pageName);

        // get the current page of items
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($rows, $offset, $perPage);

        $options = [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ];

        return Container::getInstance()->makeWith(
            LengthAwarePaginator::class,
            compact('items', 'total', 'perPage', 'currentPage', 'options')
        );
    }

    public function isLivewire(): bool
    {
        return false;
    }

    public function buildCsv(array|Collection|Builder $rows, string $fileName = 'unknown.csv')
    {
        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');

            $headers = $this->getCells()->pluck('header')->all();
            fputcsv($handle, $headers);

            if (is_array($rows)) {
                $rows = collect($rows);
            }

            if ($rows instanceof Collection) {
                foreach ($rows as $row) {
                    $row = $this->getCells()
                        ->map(fn(Cell $cell) => $cell->text($row))
                        ->all();
                    fputcsv($handle, $row);
                }
            } else {
                $rows->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        $row = $this->getCells()
                            ->map(fn(Cell $cell) => $cell->text($row))
                            ->all();
                        fputcsv($handle, $row);
                    }
                });
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
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
    public function sortArray(array $rows, string ...$columns): array
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
