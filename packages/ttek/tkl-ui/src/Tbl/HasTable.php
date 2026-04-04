<?php

namespace Tk\Tbl;

use Illuminate\Pagination\Paginator;
use Illuminate\Container\Container;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Use this trait to when adding a table to a controller
 *
 * @todo: this is yet to be tested
 */
trait HasTable
{

    protected Table $table;
    public int      $limit = 30;
    public string   $sort  = '';
    public string   $dir   = 'asc';


    public function buildRows(): array { return []; }

    public function rows(): array { return []; }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function toggleDir(): void
    {
        // TODO: change to 3 way sort
        $this->dir = $this->dir === 'asc' ? 'desc' : 'asc';
    }

    public function safeSort(): string
    {
        $sortables = $this->getTable()->getCells()->pluck('sort')->filter()->all();

        // TODO: validate sort options with cell `sort` property

        return $this->sort;
    }

    /**
     * return the paginated results of an array of rows
     */
    public function paginate(array $rows, $pageName = 'page', $currentPage = null)
    {
        $currentPage = $currentPage ?: Paginator::resolveCurrentPage($pageName);
        $total = count($rows);
        $perPage = $this->limit;

        $offset = ($currentPage - 1) * $this->limit;
        $items = array_slice($rows, $offset, $this->limit);

        $options = [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ];

        return Container::getInstance()->makeWith(
            LengthAwarePaginator::class,
            compact('items', 'total', 'perPage', 'currentPage', 'options')
        );
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
