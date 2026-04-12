<?php

namespace Tk\Table;

use Illuminate\Pagination\Paginator;
use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\ComponentAttributeBag;

/**
 * Use this trait to add a table to a controller
 */
trait IsTable
{
    const string DEFAULT_TABLE_ID = 'tbl';

    // Table url query names
    const string QUERY_LIMIT    = 'l';
    const string QUERY_PAGE     = 'p';
    const string QUERY_SORT     = 's';
    const string QUERY_DIR      = 'd';
    const string QUERY_SEARCH   = 'sr';
    const string QUERY_FILTER   = 'f';

    const string SORT_ASC = 'asc';
    const string SORT_DESC = 'desc';

    // properties are public for compatibility with Livewire
    // use the getters to read the values
    public string $tableId = self::DEFAULT_TABLE_ID;
    /**
     * rows per page
     */
    public int $limit = 0;
    public string $sort = '';
    public string $dir = '';
    public ?int $totalRows = null;

    protected int $defaultLimit = 30;
    protected string $defaultSort = '';
    protected string $defaultDir = self::SORT_ASC;

    /**
     * @var Collection<int, Cell>
     */
    protected Collection $cells;
    /**
     * @var Collection<int, Filter>
     */
    protected Collection $filters;
    protected mixed $rowAttrs = null;   // null|callable
    protected mixed $paginatedRows = null;


    /**
     * Returns an array with all available rows or a query builder
     * The returned rows can be filtered but not sorted or paginated.
     *
     * @return array<string,mixed>|Builder
     */
    abstract public function rows(): array|Builder;

    /**
     * Return a sorted and paginated object of rows
     *
     * @return LengthAwarePaginator<array<string,mixed>>
     */
    public function paginatedRows(): LengthAwarePaginator
    {
        if (!is_null($this->paginatedRows)) {
            return $this->paginatedRows;
        }

        $rows = $this->rows();
        if (is_array($rows)) {
            $rows = $this->sortArray($rows, ($this->getDir() === self::SORT_DESC ? '-' : '') . $this->safeSort());
            $this->paginatedRows = $this->paginateArray($rows);
        } else {
            $rows = $this->sortQuery($rows);
            $this->paginatedRows = $this->paginateQuery($rows);
        }

        return $this->paginatedRows;
    }

    public function clearPaginatedRows(): void
    {
        $this->paginatedRows = null;
    }

    /**
     * return an array of attributes to send to a ComponentAttributeBag object for a row
     */
    public function rowAttrs(mixed $row): ?ComponentAttributeBag
    {
        if (is_callable($this->rowAttrs)) {
            $attrs = call_user_func($this->rowAttrs, $row, $this);
            return ($attrs instanceof ComponentAttributeBag) ? $attrs : new ComponentAttributeBag($attrs);
        }
        return null;
    }

    /**
     * Set the callable to return attributes for each table row.
     *
     * @callable function (mixed $row, mixed $table): string { }
     */
    public function setRowAttrs(callable $callback): static
    {
        $this->rowAttrs = $callback;
        return $this;
    }

    /**
     * Controller method to set the table properties from the request
     */
    public function hydrateTableFromRequest(): void
    {
        $this->setDefaultLimit(config('sis.default.pagination', 30));

        $this->limit = request()->input($this->tableKey(self::QUERY_LIMIT), 0);
        $this->sort = request()->input($this->tableKey(self::QUERY_SORT), '');
        $this->dir = request()->input($this->tableKey(self::QUERY_DIR), '');

        // TODO: Add filter hydration
        //       I have not looked into how we will implement filters and their dependants within controllers yet.
        //       I think we will need some JavaScript/Alpine to handle the dynamic options.
        //       But it will require a request URL.
    }

    /**
     * Return a validated sort column
     */
    public function safeSort(): string
    {
        return ($this->getSort() !== '' && in_array($this->getSort(), $this->sortableKeys()))
            ? $this->getSort()
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

    public function getLimit(): int
    {
        return $this->limit
            // if limit is set to 0, use the default limit or the total rows
            ?: min($this->totalRows, $this->defaultLimit)
                // use the max possible limit as the paginator does not work with 0
                ?: PHP_INT_MAX;
    }

    public function getSort(): string
    {
        return $this->sort ?: $this->defaultSort;
    }

    public function getDir(): string
    {
        return $this->dir ?: $this->defaultDir ?: self::SORT_ASC;
    }

    public function getTableId(): string
    {
        return $this->tableId;
    }

    public function setDefaultLimit(int $limit): static
    {
        $this->defaultLimit = $limit;
        return $this;
    }

    public function setDefaultSort(string $sort, string $dir = ''): static
    {
        $this->defaultSort = $sort;
        $this->defaultDir = ($dir === self::SORT_DESC) ? self::SORT_DESC : self::SORT_ASC;
        return $this;
    }

    /**
     * @return Collection<int, Cell>
     */
    public function getCells(): Collection
    {
        return $this->cells ??= collect();
    }

    /**
     * @return Collection<int, Cell>
     */
    public function getVisibleCells(): Collection
    {
        return $this->getCells()->filter(fn(Cell $cell) => $cell->isVisible());
    }

    public function getCell(string $name): ?Cell
    {
        return $this->getCells()->get($name);
    }

    public function removeCell(string $name): static
    {
        $this->getCells()->forget($name);
        return $this;
    }

    public function appendCell(string|Cell $cell, ?string $after = null): Cell
    {
        $cell = is_string($cell) ? new Cell($cell) : $cell;
        $cell->setTable($this);

        if ($this->getCells()->has($cell->getName())) {
            throw new \Exception("Cell with name '{$cell->getName()}' already exists.");
        }

        if (is_null($after)) {
            $this->getCells()->put($cell->getName(), $cell);
            return $cell;
        }

        $index = $this->getCells()->keys()->search($after);
        if ($index === false) {
            $this->getCells()->put($cell->getName(), $cell);
            return $cell;
        }

        $this->cells = $this->insertAt($this->getCells(), $index+1, $cell->getName(), $cell);

        return $cell;
    }

    public function prependCell(string|Cell $cell, ?string $before = null): Cell
    {
        $cell = is_string($cell) ? new Cell($cell) : $cell;
        $cell->setTable($this);

        if ($this->getCells()->has($cell->getName())) {
            throw new \Exception("Cell with name '{$cell->getName()}' already exists.");
        }

        if (is_null($before)) {
            $this->getCells()->prepend($cell, $cell->getName());
            return $cell;
        }

        $index = $this->getCells()->keys()->search($before);
        if ($index === false) {
            $this->getCells()->prepend($cell, $cell->getName());
            return $cell;
        }

        $this->cells = $this->insertAt($this->getCells(), $index, $cell->getName(), $cell);

        return $cell;
    }

    /**
     * @return Collection<int, Filter>
     */
    public function getFilters(): Collection
    {
        return $this->filters ??= collect();
    }

    /**
     * @return Collection<int, Filter>
     */
    public function getVisibleFilters(): Collection
    {
        return $this->getFilters()->filter(fn(Filter $filter) => $filter->isVisible());
    }

    public function getFilter(string $key): ?Filter
    {
        return $this->getFilters()->get($key);
    }

    public function removeFilter(string $name): static
    {
        $this->getFilters()->forget($name);
        return $this;
    }

    public function appendFilter(string|Filter $filter, ?string $after = null): Filter
    {
        $filter = is_string($filter) ? new Filter($filter) : $filter;
        $filter->setTable($this);

        if ($this->getFilters()->has($filter->getKey())) {
            throw new \Exception("Filter with key '{$filter->getKey()}' already exists.");
        }

        if (is_null($after)) {
            $this->getFilters()->put($filter->getKey(), $filter);
            return $filter;
        }

        $index = $this->getFilters()->keys()->search($after);
        if ($index === false) {
            $this->getFilters()->put($filter->getKey(), $filter);
            return $filter;
        }

        $this->filters = $this->insertAt($this->getFilters(), $index+1, $filter->getKey(), $filter);

        return $filter;
    }

    public function prependFilter(string|Filter $filter, ?string $before = null): Filter
    {
        $filter = is_string($filter) ? new Filter($filter) : $filter;
        $filter->setTable($this);

        if ($this->getFilters()->has($filter->getKey())) {
            throw new \Exception("Filter with key '{$filter->getKey()}' already exists.");
        }

        if (is_null($before)) {
            $this->getFilters()->prepend($filter, $filter->getKey());
            return $filter;
        }

        $index = $this->getFilters()->keys()->search($before);
        if ($index === false) {
            $this->getFilters()->prepend($filter, $filter->getKey());
            return $filter;
        }

        $this->filters = $this->insertAt($this->getFilters(), $index, $filter->getKey(), $filter);

        return $filter;
    }

    /**
     * Collection helper function to insert items at a specific index
     */
    private function insertAt(Collection $col, int $index, string $key, mixed $item): Collection
    {
        $before = $col->slice(0, $index);
        $after = $col->slice($index);

        return $before
            ->put($key, $item)
            ->merge($after);
    }

    public function isSearchable(): bool
    {
        return (in_array(IsSearchable::class, class_uses(static::class)));
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
        if ($tableId == self::DEFAULT_TABLE_ID) return $key;
        if (str_starts_with($key, $tableId.'_')) {
            return $key;
        }
        return $tableId.'_'.$key;
    }

    public function sortQuery(Builder $query): Builder
    {
        $sort = $this->safeSort();
        $dir = $this->getDir();
        return $query->orderBy($sort, $dir);
    }

    public function paginateQuery(Builder $query): LengthAwarePaginator
    {
        // normalizes the URL path to avoid errors
        $path = '/' . ltrim(Paginator::resolveCurrentPath(), '/');

        // only apply sort order if not already ordered
        if (empty($query->getQuery()->orders)) {
            $this->sortQuery($query);
        }

        return $query->paginate(
            perPage: $this->getLimit(),
            pageName: $this->tableKey(self::QUERY_PAGE),
        )->withPath($path);
    }

    /**
     * return the paginated results of an array of rows
     */
    public function paginateArray(array $rows, $pageName = self::QUERY_PAGE, $currentPage = null): LengthAwarePaginator
    {
        $path = '/' . ltrim(Paginator::resolveCurrentPath(), '/');

        $currentPage = $currentPage ?: Paginator::resolveCurrentPage($pageName);
        $total = $this->totalRows = count($rows);
        $perPage = $this->getLimit();
        $pageName = $this->tableKey($pageName);

        // get the current page of items
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($rows, $offset, $perPage);

        $options = compact('path', 'pageName');

        return Container::getInstance()->makeWith(
            LengthAwarePaginator::class,
            compact('items', 'total', 'perPage', 'currentPage', 'options')
        );
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
                        ->map(fn(Cell $cell) => $cell->value($row))
                        ->all();
                    fputcsv($handle, $row);
                }
            } else {
                $rows->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        $row = $this->getCells()
                            ->map(fn(Cell $cell) => $cell->value($row))
                            ->all();
                        fputcsv($handle, $row);
                    }
                });
            }

            fclose($handle);
        };

        // todo mm need a way for bothLivewire and controller csv downloads
        return response()->streamDownload($callback, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function isLivewire(): bool
    {
        return false;
    }

    // todo mm: Refactor the below array sort method, this is just copied from sisV1 for now,
    //          It needs to be refactored to cater for the new `sort` and `dir` properties

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
