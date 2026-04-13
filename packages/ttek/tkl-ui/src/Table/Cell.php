<?php

namespace Tk\Table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

class Cell
{
    public string $name = '';
    public string $header = '';
    public bool $sortable = false;
    public string $sort = '';
    public bool $visible = true;

    protected mixed $value = null;   // null|string|callable
    protected mixed $view = null;   // null|string|callable
    protected mixed $table = null;  // HasTable trait
    protected ComponentAttributeBag $attrs;
    protected ComponentAttributeBag $headerAttrs;


    public function __construct(
        string $name,
        string $header = '',
        bool $sortable = false,
        null|callable $value = null,
        null|callable $view = null,
        string $sort = '',
        bool $visible = true
    )
    {
        $this->name = $name;
        if (empty($header)) {
            $header = strval(preg_replace('/(Id|_id)$/', '', $name));
            $header = str_replace(['_', '-'], ' ', $header);
            $header = ucwords(strval(preg_replace('/[A-Z]/', ' $0', $header)));
            $header = e($header);
        }
        $this->setHeader($header);
        $this->setSort($sort ?: $name);
        $this->setSortable($sortable);
        $this->setVisible($visible);
        if (is_callable($value)) $this->value = $value;
        if (is_callable($view)) $this->view = $view;
    }

    /**
     * get the row primary key value
     */
    public static function getKey(mixed $row, string $key = 'id'): string
    {
        if (is_null($row)) return '';

        if ($row instanceof Model) {
            return $row->getKey();
        }

        if (is_array($row)) {
            return $row[$key] ?? '';
        }

        if (is_object($row)) {
            return $row->{$key} ?? '';
        }
        return '';
    }

    /**
     * @return IsTable
     */
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

    /**
     * Headers can contain markup and text should be escaped.
     */
    public function setHeader(string $header): static
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

    public function setSort(string $sort): static
    {
        $this->sort = $sort;
        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible = true): static
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * return the raw value from a row using the cell key name
     */
    public function getRowValue(mixed $row): mixed
    {
        if (is_array($row)) return $row[$this->name] ?? '';
        if (is_object($row)) return $row->{$this->name} ?? '';
        return '';
    }

    /**
     * Get the plain text value of the cell (useful for .txt or .csv)
     */
    public function value(mixed $row): mixed
    {
        if (!$this->isVisible()) return '';
        if (is_callable($this->value)) {
            $ret = call_user_func($this->value, $row, $this);
            if (is_string($ret) || $ret instanceof \Stringable) {
                return e($ret);
            }
        }

        $value = $this->getRowValue($row);

        if (is_string($value) || $value instanceof \Stringable) {
            return e($value);
        }

        return '';
    }

    /**
     * Set a callable or string that returns the text value of the cell
     * Used for .txt, .csv exports and should not contain any HTML markup.
     * Note: value will be escaped automatically.
     *
     * @callable function (mixed $row, Cell $cell): string { }
     */
    public function setValue(string|callable $value): static
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get the HTML value of the cell
     */
    public function view(mixed $row): mixed
    {
        if (!$this->isVisible()) return '';
        if (is_callable($this->view)) {
            $ret = call_user_func($this->view, $row, $this);
            if (is_string($ret) || $ret instanceof \Stringable) {
                return $ret;
            }
        }
        return $this->value($row);
    }

    /**
     * Set the callable that returns the HTML value of the cell.
     * By default, `view()` returns `value()`.
     *
     * @callable function (mixed $row, Cell $cell): string { }
     */
    public function setView(callable $view): static
    {
        $this->view = $view;
        return $this;
    }

    public function addClass(string $class): static
    {
        $this->attrs = $this->getAttrs()->class($class);
        return $this;
    }

    public function addAttr(array $attrs): static
    {
        $this->attrs = $this->getAttrs()->merge($attrs);
        return $this;
    }

    public function getAttrs(): ComponentAttributeBag
    {
        $this->attrs ??= new ComponentAttributeBag();
        return $this->attrs;
    }

    public function mergeAttrs(array $attrs): static
    {
        $this->attrs = $this->getAttrs()->merge($attrs);
        return $this;
    }

    /**
     * Add a CSS class to the cell
     */
    public function addHeaderClass(string $class): static
    {
        $this->headerAttrs = $this->getHeaderAttrs()->class($class);
        return $this;
    }

    public function addHeaderAttr(array $attrs): static
    {
        $this->headerAttrs = $this->getHeaderAttrs()->merge($attrs);
        return $this;
    }

    public function getHeaderAttrs(): ComponentAttributeBag
    {
        $this->headerAttrs ??= new ComponentAttributeBag();
        return $this->headerAttrs;
    }

    public function mergeHeaderAttrs(array $attrs): static
    {
        $this->headerAttrs = $this->getHeaderAttrs()->merge($attrs);
        return $this;
    }

    public function getNextSortUrl(array $query = []): string
    {
        $url = request()->fullUrl();

        if ($this->isSortable()) {

            if ($this->getSort() === $this->getTable()->getSort()) {
                $dir = $this->getTable()->getDir() === $this->getTable()::SORT_DESC
                    ? ''
                    : $this->getTable()::SORT_DESC;
            } else {
                $dir  = '';
            }

            $query[$this->getTable()->tableKey($this->getTable()::QUERY_SORT)] = $this->getSort();
            $query[$this->getTable()->tableKey($this->getTable()::QUERY_DIR)] = $dir ?: null;
        }

        return url()->query($url, $query);
    }

    /**
     * get the next sort direction
     */
    protected function getNextDir(): string
    {
        return (empty($this->getTable()->getDir()) || $this->getTable()::SORT_DESC)
            ? $this->getTable()::SORT_ASC
            : $this->getTable()::SORT_DESC;
    }

}
