<?php

namespace Tk\Table;

use Illuminate\View\ComponentAttributeBag;
use Tk\Utils\Str;

class Cell
{
    const string TYPE_ROW_SELECT = 'rowselect';

    protected string     $name     = '';
    protected string     $header   = '';
    protected string     $orderBy  = '';
    protected string     $type     = '';
    protected bool       $sortable = false;
    protected mixed      $value    = null;  // null|string|callable
    protected mixed      $html     = null;  // null|string|callable
    protected ?Table     $table    = null;
    protected ComponentAttributeBag $attributes;
    protected ComponentAttributeBag $headerAttrs;

    // the current row being rendered, null if not rendering
    private object|array|null $row = null;


    public function __construct(string $name)
    {
        $this->name = $name;
        $this->orderBy = $name;
        $this->attributes = new ComponentAttributeBag();
        $this->headerAttrs = new ComponentAttributeBag();

        $this->addClass('m'.ucfirst($name));
        $this->addHeaderClass('mh'.ucfirst($name));

        // set the default header
        $header = strval(preg_replace('/(Id|_id)$/', '', $name));
        $header = str_replace(['_', '-'], ' ', $header);
        $header = ucwords(strval(preg_replace('/[A-Z]/', ' $0', $header)));
        $this->setHeader($header);
    }

    /**
     * Return the currently rendered row,
     * getValue($row) must be called at least once to get access to the row
     */
    public function getRow(): object|array|null
    {
        return $this->row;
    }

    public function setRow(object|array|null $row): Cell
    {
        $this->row = $row;
        return $this;
    }

    /**
     * Return the raw value of a cell, without display markup
     * If value is null then get value from row using the cell name
     * value should be exportable for csv|text formats
     */
    public function getValue(object|array $row): mixed
    {
        if (is_null($this->row)) $this->row = $row;

        $value = $this->value;
        if (is_callable($value)) {
            $value = $value($row, $this);
        }
        if (is_null($value)) {
            if (is_array($row)) $value = $row[$this->name] ?? '';
            if (is_object($row)) $value = $row->{$this->name} ?? '';
        }
        return $value;
    }

    /**
     * @callable function (mixed $row, Cell $cell):string { return ''; }
     */
    public function setValue(string|callable $value): Cell
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Return the value with any display markup if required
     */
    public function getHtml(object|array $row): string
    {
        $value = $this->getValue($row);
        $html = $this->html ?? $value;
        if (is_callable($html)) {
            $html = strval($html($row, $this));
        }
        return $html;
    }

    /**
     * @callable function (mixed $row, Cell $cell):string { return ''; }
     */
    public function setHtml(mixed $html): Cell
    {
        $this->html = $html;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttributes(): ComponentAttributeBag
    {
        return $this->attributes;
    }

    /**
     * Add a CSS class to the cell
     */
    public function addClass(string $class): static
    {
        $this->attributes = $this->attributes->class($class);
        return $this;
    }

    public function addAttr(array $attrs): static
    {
        $this->attributes = $this->attributes->merge($attrs);
        return $this;
    }

    public function setHeader(string $header): static
    {
        $this->header = $header;
        return $this;
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function getHeaderAttrs(): ComponentAttributeBag
    {
        return $this->headerAttrs;
    }

    public function setHeaderAttrs(ComponentAttributeBag $headerAttrs): static
    {
        $this->headerAttrs = $headerAttrs;
        return $this;
    }

    public function addHeaderClass(string $class): static
    {
        $this->headerAttrs = $this->headerAttrs->class($class);
        return $this;
    }

    public function addHeaderAttr(array $attrs): static
    {
        $this->headerAttrs = $this->headerAttrs->merge($attrs);
        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function setSortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;
        return $this;
    }

    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set the orderBy column
     */
    public function setOrderBy(string $orderBy): static
    {
        $this->orderBy = str_starts_with($orderBy, '-') ? substr($orderBy, 1) : $orderBy;
        return $this;
    }

    /**
     * The $table param is set when a cell is added to a table
     */
    public function getTable(): ?Table
    {
        return $this->table;
    }

    public function setTable(?Table $table): static
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Get the next order by url for this cell.
     * Create an orderBy url for the next order
     *  - asc
     *  - desc
     *  - {remove}
     *
     * @todo think about how to manage multiple orderBy values
     */
    public function getOrderByUrl(): string
    {
        if (!$this->getTable()) return '';
        if (!$this->isSortable()) return '';

        $key = $this->getTable()->makeIdKey(Table::QUERY_ORDER);
        $url = request()->url();
        $url = url()->query($url, [$key => null]);

        $orderBy = $this->getOrderBy();
        $tableOrderBy = $this->getTable()->getOrderBy();

        $dir = str_starts_with($tableOrderBy, '-') ? '' : '-';

        if (str_replace('-', '', $tableOrderBy) == $orderBy) {     // if ordered by current cell
            // set to DESC
            if ($dir == '-') {
                $url = url()->query($url, [$key => $dir.$orderBy]);
            } else {
                // remove cell order
                // TODO: When default desc order by set to this col we cannot
                //       toggle the order by when setting it to null, as the default then becomes set.
                //       Using an empty string may be fine, just means we have an empty query param in the url?
                //$url = url()->query($url, [$key => null]);
                $url = url()->query($url, [$key => '']);
            }
        } else {
            // set to ASC
            $url = url()->query($url, [$key => $orderBy]);
        }

        return $url;
    }

    public function getOrderByDir():string
    {
        $orderBy = $this->getOrderBy();
        $tableOrderBy = $this->getTable()->getOrderBy();

        if (str_replace('-', '', $tableOrderBy) == $orderBy) {    // if ordered by current cell
            if (str_starts_with($tableOrderBy, '-')) {
                return 'desc';
            } elseif (!empty($this->getTable()->getOrderBy())) {
                return 'asc';
            }
        }
        return '';
    }

}
