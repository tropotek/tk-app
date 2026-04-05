<?php

namespace Tk\Tbl;

use Illuminate\View\ComponentAttributeBag;

class Cell
{
    public string $name = '';
    public string $header = '';
    public bool $sortable = false;
    public string $sort = '';
    public bool $visible = true;

    protected mixed $text = null;   // null|callable
    protected mixed $html = null;   // null|callable
    protected mixed $table = null;  // HasTable trait

    protected ComponentAttributeBag $attrs;


    public function __construct(
        string $name,
        string $header = '',
        bool $sortable = false,
        null|callable $text = null,
        null|callable $html = null,
        string $sort = '',
        bool $visible = true
    )
    {
        $this->attrs = new ComponentAttributeBag();
        $this->name = $name;

        if (empty($header)) {
            $header = strval(preg_replace('/(Id|_id)$/', '', $name));
            $header = str_replace(['_', '-'], ' ', $header);
            $header = ucwords(strval(preg_replace('/[A-Z]/', ' $0', $header)));
        }
        $this->header = $header;

        if (empty($sort)) $sort = $name;
        $this->sort = $sort;

        $this->sortable = $sortable;

        if (is_callable($text)) {
            $this->text = $text;
        }

        if (is_callable($html)) {
            $this->html = $html;
        }

        $this->visible = $visible;
    }

    /**
     * Get the plain text value of the cell (for .txt or .csv)
     */
    public function text(mixed $row): string
    {
        if (is_callable($this->text)) {
            return call_user_func($this->text, $row, $this);
        }
        $val = '';
        if (is_array($row)) {
            $val = $row[$this->name] ?? '';
        }
        if (is_object($row)) {
            $val = $row->{$this->name} ?? '';
        }
        if (is_string($val) || $val instanceof \Stringable) {
            return strval($val);
        }
        return '';
    }

    /**
     * Get the HTML value of the cell
     */
    public function html(mixed $row): string
    {
        if (is_callable($this->html)) {
            return call_user_func($this->html, $row, $this);
        }
        return $this->text($row);
    }

    public function getTable(): mixed
    {
        return $this->table;
    }

    public function setTable(mixed $table): static
    {
        if (is_null($table)) {
            throw new \InvalidArgumentException("cannot set a null table object");
        }
        if (!method_exists($table, 'rows')) {
            throw new \InvalidArgumentException('expected table object using the isTable trait');
        }
        $this->table = $table;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function setHeader(string $header): Cell
    {
        $this->header = $header;
        return $this;
    }

    public function setSortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;
        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function getSort(): string
    {
        return $this->sort;
    }

    public function setSort(string $sort): Cell
    {
        $this->sort = $sort;
        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible = true): Cell
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * Set the callable that returns the text value of the cell
     */
    public function setText(callable $text): Cell
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Set the callable that returns the HTML value of the cell
     */
    public function setHtml(callable $html): Cell
    {
        $this->html = $html;
        return $this;
    }

    /**
     * Add a CSS class to the cell
     */
    public function addClass(string $class): static
    {
        $this->attrs = $this->attrs->class($class);
        return $this;
    }

    public function addAttr(array $attrs): static
    {
        $this->attrs = $this->attrs->merge($attrs);
        return $this;
    }

    public function getAttrs(): ComponentAttributeBag
    {
        return $this->attrs;
    }

    public function getNextSortUrl(string $currentSort = '', string $currentDir = '', array $query = []): string
    {
        $url = request()->fullUrl();
        if ($this->isSortable()) {
            if ($currentSort == $this->getSort() && $currentDir == 'desc') {
                $query[$this->getTable()->tableKey('sort')] = null;
                $query[$this->getTable()->tableKey('dir')] = null;
            } else {
                $query[$this->getTable()->tableKey('sort')] = $this->getSort();
                $query[$this->getTable()->tableKey('dir')] = $this->getNextDir($currentSort, $currentDir);
            }
        }
        return url()->query($url, $query);
    }

    /**
     * get the next sort direction
     */
    protected function getNextDir(string $currentSort = '', string $currentDir = ''): string
    {
        if ($currentSort == $this->getSort()) {
            if ($currentDir == 'desc') {
                return '';
            }
            return empty($currentDir) ? 'asc' : 'desc';
        }
        return 'asc';
    }

}
