<?php

namespace Tk\Support;

use Illuminate\Support\Collection;

class ItemCollection extends Collection
{

    public function appendItem(string $key, mixed $item, ?string $after = null): mixed
    {
        if ($this->has($key)) {
            throw new \Exception("Item key '{$key}' already exists.");
        }

        if (is_null($after)) {
            $this->put($key, $item);
            return $item;
        }

        $index = $this->keys()->search($after);
        if ($index === false) {
            $this->put($key, $item);
            return $item;
        }

        $this->insertAt($index+1, $key, $item);

        return $item;
    }

    public function prependItem(string $key, mixed $item, ?string $before = null): mixed
    {
        if ($this->has($key)) {
            throw new \Exception("Item key '{$key}' already exists.");
        }

        if (is_null($before)) {
            $this->prepend($item, $key);
            return $item;
        }

        $index = $this->keys()->search($before);
        if ($index === false) {
            $this->prepend($item, $key);
            return $item;
        }

        $this->insertAt($index, $key, $item);

        return $item;
    }

    /**
     * helper function to insert items at a specific index
     */
    private function insertAt(int $index, string $key, mixed $item): Collection
    {
        $before = $this->slice(0, $index);
        $after = $this->slice($index);

        $this->items = $before
            ->put($key, $item)
            ->merge($after)->all();

        return $this;
    }
}
