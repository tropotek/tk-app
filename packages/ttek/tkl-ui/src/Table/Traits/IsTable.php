<?php

namespace Tk\Table\Traits;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\View\ComponentAttributeBag;
use Tk\Table\Column;

/**
 * Use this trait to add a table to a controller
 */
trait IsTable
{
    use WithSearch,
        HasAttrs,
        HasColumns,
        HasFilters,
        WithExport;


    const string DEFAULT_TABLE_ID = 'tbl';

    // Table url query names
    const string QUERY_LIMIT    = 'l';
    const string QUERY_PAGE     = 'p';
    const string QUERY_SORT     = 's';
    const string QUERY_DIR      = 'd';
    const string QUERY_SEARCH   = 'sr';
    const string QUERY_FILTER   = 'f';
    const string QUERY_RESET    = 'rst';

    const string SORT_ASC = 'asc';
    const string SORT_DESC = 'desc';


    public int $limit = 0;      // rows per-page
    public string $sort = '';
    public string $dir = '';
    public array $filterVals = [];
    public ?int $totalRows = null;

    protected int $defaultLimit = 30;
    protected string $defaultSort = '';
    protected string $defaultDir = self::SORT_ASC;
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
     * Controller method to set the table properties from the request
     */
    public function hydrateTableFromRequest(): void
    {
        $this->setDefaultLimit(config('sis.default.pagination', 30));

        $this->limit = (int)request()->input($this->tableKey(self::QUERY_LIMIT), 0);
        $this->sort = (string)request()->input($this->tableKey(self::QUERY_SORT), '');
        $this->dir = (string)request()->input($this->tableKey(self::QUERY_DIR), '');
        $this->filterVals = request()->input($this->tableKey(self::QUERY_FILTER), []) ?? [];
    }

    /**
     * Override this method to change your table ID
     */
    public function tableId(): string
    {
        return self::DEFAULT_TABLE_ID;
    }

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
     * Return a validated sort column
     */
    public function safeSort(): string
    {
        return ($this->getSort() !== '' && in_array($this->getSort(), $this->sortableKeys()))
            ? $this->getSort()
            : $this->defaultSort;
    }

    /**
     * Allowed sort keys. Defaults to all sortable column keys.
     */
    public function sortableKeys(): array
    {
        return $this->getColumns()
            ->filter(fn(Column $column) => $column->isSortable())
            ->pluck('sort')
            ->filter()
            ->all();
    }

    public function getLimit(): int
    {
        return $this->limit ?: $this->defaultLimit;
    }

    public function getSort(): string
    {
        return $this->sort ?: $this->defaultSort;
    }

    public function getDir(): string
    {
        return $this->dir ?: $this->defaultDir;
    }

    public function setDefaultLimit(int $limit): static
    {
        $this->defaultLimit = $limit;
        return $this;
    }

    public function setDefaultSort(string $sort, ?string $dir = null): static
    {
        $this->defaultSort = $sort;
        if ($dir) {
            $this->setDefaultDir($dir);
        }
        return $this;
    }

    public function setDefaultDir(string $dir = self::SORT_ASC): static
    {
        $this->defaultDir = in_array($dir, [self::SORT_ASC, self::SORT_DESC]) ? $dir : self::SORT_ASC;
        return $this;
    }

    /**
     * Create a key from the table id
     * If the table is using the default id, return the key unchanged
     */
    public function tableKey(string $key): string
    {
        return self::makeTableKey($key, $this->tableId());
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
        // only apply sort order if not already ordered
        if (empty($query->getQuery()->orders)) {
            $this->sortQuery($query);
        }

        $paginator = $query->paginate(
            perPage: $this->getLimit() ?: PHP_INT_MAX,
            pageName: $this->tableKey(self::QUERY_PAGE),
        )->withPath(Paginator::resolveCurrentPath());

        if (! $this->isLivewire()) {
            $paginator->appends(request()->except($this->tableKey(self::QUERY_PAGE)));
        }

        return $paginator;
    }

    /**
     * return the paginated results of an array of rows
     */
    public function paginateArray(array $rows, $pageName = self::QUERY_PAGE, $currentPage = null): LengthAwarePaginator
    {
        $path = Paginator::resolveCurrentPath();
        $pageName = $this->tableKey($pageName);

        $currentPage = $currentPage ?: Paginator::resolveCurrentPage($pageName);
        $total = $this->totalRows = count($rows);
        $perPage = $this->getLimit() ?: PHP_INT_MAX;

        // get the current page of items
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($rows, $offset, $perPage);

        $options = compact('path', 'pageName');

        $paginator = Container::getInstance()->makeWith(
            LengthAwarePaginator::class,
            compact('items', 'total', 'perPage', 'currentPage', 'options')
        );

        if (! $this->isLivewire()) {
            $paginator->appends(request()->except($pageName));
        }

        return $paginator;
    }

    /**
     * remove all table query keys and any extra keys from the $params array
     */
    public function urlWithoutTableParams(array $params = []): string
    {
        $remainingParams = collect(request()->query())->reject(
            $this->tableId() !== self::DEFAULT_TABLE_ID
                ? fn($v, $k) => str_starts_with($k, $this->tableId() . '_') || in_array($k, $params)
                : fn($v, $k) => in_array($k, array_merge([
                self::QUERY_PAGE, self::QUERY_SORT, self::QUERY_DIR,
                self::QUERY_LIMIT, self::QUERY_FILTER, self::QUERY_SEARCH, self::QUERY_RESET
            ], $params))
        )->all();

        return $remainingParams
            ? url()->current() . '?' . http_build_query($remainingParams)
            : url()->current();
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
