<?php

namespace Tk\Tbl;

use Illuminate\Support\Collection;

class Table
{
    public string $id = 'tbl';
    protected Collection $cells;

    public function getCell(string $name): ?Cell
    {
        /** @var Cell $cell */
        $cell = $this->cells->get($name);
        return $cell;
    }

    public function removeCell(string $name): static
    {
        $this->cells->forget([$name]);
        return $this;
    }

    /**
     * @return Collection<int,Cell>
     */
    public function getCells(object|array|null $row = null): Collection
    {
        if (!is_null($row)) {
            foreach ($this->cells as $cell) {
                $cell->setRow($row);
            }
        }
        return $this->cells;
    }

    public function appendCell(string|Cell $cell, ?string $after = null): Cell
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
            $idx = $this->cells->keys()->search($after);
            // If the key is not found, add at the end
            $idx = ($idx === false) ? $this->cells->count() - 1 : $idx;

            $this->cells = $this->cells->slice(0, $idx + 1)
                ->merge([$cell->name => $cell])
                ->merge($this->cells->slice($idx + 1));
        } else {
            $this->cells->put($cell->name, $cell);
        }

        return $cell;
    }

    public function prependCell(string|Cell $cell, ?string $before = null): Cell
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

        if ($before) {
            $idx = $this->cells->keys()->search($before);
            // If the key is not found, add at the beginning
            $idx = ($idx === false) ? 0 : $idx;

            $this->cells = $this->cells->splice($idx, 0, [$cell->name => $cell]);
        } else {
            $this->cells->prepend($cell, $cell->name);
        }

        return $cell;
    }

    /**
     * Create a key from the table id
     * If the table is using the default id, return the key unchanged
     */
    public function key(string $key): string
    {
        return self::makeKey($key, $this->id);
    }

    /**
     * Create a table-specific key using the table id
     * returns: `{$tableId}_{$key}`
     */
    public static function makeKey(string $key, string $tableId = ''): string
    {
        if (str_starts_with($key, $tableId.'_')) {
            return $key;
        }
        return $tableId.'_'.$key;
    }
}
