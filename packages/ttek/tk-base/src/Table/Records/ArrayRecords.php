<?php

namespace Tk\Table\Records;


use Illuminate\Container\Container;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Populate a table with rows from an array.
 */
class ArrayRecords extends RecordsInterface
{
    protected ?array $rows = null;

    public function __construct(?array $rows = null)
    {
        $this->rows = $rows;
    }

    /**
     * filter, sort, and paginate the array returning the results
     */
    protected function initArray(): array
    {
        if (!$this->getTable() || is_null($this->rows)) {
            throw new \Exception('Table or rows not set');
        }

        $this->total = count($this->rows);

        // filter results using callable
        if ($this->getFilter()) {
            $this->rows = call_user_func($this->getFilter(), $this->rows);
        }

        // sort results
        $columns = array_filter(array_map('trim', explode(',', $this->getTable()->getOrderBy())));
        $this->rows = $this->sortRows($this->rows, ...$columns);

        // paginate results
        return $this->paginateRows($this->rows);
    }

    public function toArray(): array
    {
        if (!isset($this->records)) {
            $this->records = $this->initArray();
            $this->rows = null;
        }
        return $this->records;
    }

    public function getPaginator(): ?AbstractPaginator
    {
        $items = $this->toArray();
        $total = $this->countAll();
        $perPage = $this->getTable()->getLimit();
        $currentPage = $this->getTable()->getPage();
        $options = [
            'path' => request()->path(),
            'pageName' => $this->getTable()->makeIdKey(\Tk\Table\Table::QUERY_PAGE),
        ];
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }

    /**
     * Set the table rows and apply pagination and sorting with PHP
     * Use this method when all the results are in the $rows array
     * Set $sort to null to disable sorting
     *
     * @param array<int|string, mixed> $rows
     * @return array<int|string, mixed>
     */
    protected function paginateRows(array $rows): array
    {
        $offset = ($this->getTable()->getPage()-1)*$this->getTable()->getLimit();
        if ($this->getTable()->getLimit() > 0 && $this->getTable()->getLimit() < $this->countAll()) {
            return array_slice($rows, $offset, $this->getTable()->getLimit());
        }
        return $rows;
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
        } elseif (isset($row->{$col})) {
            return $row->{$col} ?? null;
        }
        return null;
    }
}
