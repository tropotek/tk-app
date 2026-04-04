<?php

namespace Tk\Tbl;

use Illuminate\Container\Container;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Tk\Livewire\Table\Records\RecordsInterface;
use Tk\Livewire\Table\Table;

/**
 * Populate a table with rows from an array.
 */
class ArrayRows extends RecordsInterface
{
    protected ?array $rows = null;

    public function __construct(?array $rows = null)
    {
        $this->rows = $rows;
    }

    /**
     * @interface RecordsInterface
     */
    public function toArray(): array
    {
        if (isset($this->records)) {
            return $this->records;
        }

        if (!$this->getTable() || is_null($this->rows)) {
            throw new \Exception('Table or rows not set');
        }

        // filter results using callable
        if ($this->filter) {
            $this->rows = call_user_func($this->filter, $this->rows); // $this->getTable()->getParams());
        }

        // Set total rows found
        $this->total = count($this->rows);

        // sort results
        $columns = array_filter(array_map('trim', explode(',', $this->getTable()->sort)));
        $this->rows = $this->sortRows($this->rows, ...$columns);

        // paginate results
        $limit = $this->getTable()->limit;
        if ($limit > 0) {
            $page = $this->page;
            $offset = ($page - 1) * $limit;
            $items = array_slice($this->rows, $offset, $limit);
            $this->records = $items;

            // setup paginator
            $this->paginator = new LengthAwarePaginator(
                $items,
                $this->total,
                $limit,
                $page,
                [
                    'path' => request()->url(),
                    'pageName' => $this->getTable()->key(Table::QUERY_PAGE),
                ]
            );
        } else {
            $this->records = $this->rows;
        }

        $this->rows = null;
        return $this->records;
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
