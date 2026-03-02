<?php

namespace Tk\Table;

use Illuminate\View\ComponentAttributeBag;
use Tk\Table\Traits\HasAttributes;
use Tk\Utils\Str;

class Cell
{
    use HasAttributes;

    // Custom table components
    const string COMP_ROW_SELECT = 'tk-base::table.cell.rowselect';

    protected string     $name      = '';
    protected string     $header    = '';
    protected string     $orderBy   = '';
    protected string     $component = '';
    protected bool       $sortable  = false;
    protected mixed      $value     = null;  // null|string|callable
    protected mixed      $html      = null;  // null|string|callable
    protected ?Table     $table     = null;
    protected ComponentAttributeBag $headerAttrs;

    // the current row being rendered, null if not rendering
    private object|array|null $row = null;


    public function __construct(string $name)
    {
        $this->name = $name;
        $this->orderBy = $name;
        $this->_attributes = new ComponentAttributeBag();
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
     * Return the raw value without display markup
     * If the cell value is a string, return the static string
     * If the cell value is a callable, return the result of the callable
     * If the cell value is null, return the value from the row array or object.
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
    public function setValue(null|string|callable $value): Cell
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Return the value with any display markup if required.
     * If the cell html is a string, return the static string
     * If the cell html is a callable, return the result of the callable
     * If the cell html is null, return the raw cell value
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
    public function setHtml(null|string|callable $html): Cell
    {
        $this->html = $html;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * return the head component name renderer
     */
    public function getComponentHead(): string
    {
        if (empty($this->component)) return '';
        return $this->component . '-head';
    }

    /**
     * Set the view component name to use for this cell
     */
    public function setComponent(string $component): static
    {
        if (str_starts_with($component, 'x-')) {
            $component = substr($component, 2);
        }
        $this->component = $component;
        return $this;
    }

    /**
     * Check if a component exists
     *
     * @note View::exists() function requires a view namespace,
     *       use this method to prepend the `packages.`
     *       namespace to the component path.
     */
    public function componentExists(string $component): bool
    {
        if (!$component) return false;
        if (str_contains($component, '::')) {
            [$pkg, $comp] = explode('::', $component);
            $component = $pkg.'::components.'.$comp;
        } else {
            $component = 'components.'.$component;
        }
        return view()->exists($component);
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

//        $key = $this->getTable()->key(Table::QUERY_ORDER);
//        $url = request()->url();
        //$url = url()->query($url, [$key => null]);

        $orderBy = $this->getOrderBy();
        $tableOrderBy = $this->getTable()->getOrderBy();

        $dir = str_starts_with($tableOrderBy, '-') ? '' : '-';

        if (str_replace('-', '', $tableOrderBy) == $orderBy) {     // if ordered by current cell
            // set to DESC
            if ($dir == '-') {
                $url = $this->table->url([Table::QUERY_ORDER => $dir.$orderBy]);
                //$url = url()->query($url, [$key => $dir.$orderBy]);
            } else {
                // remove cell order
                // TODO: When default desc order by set to this col we cannot
                //       toggle the order by when setting it to null, as the default then becomes set.
                //       Using an empty string may be fine, just means we have an empty query param in the url?
                //$url = url()->query($url, [$key => null]);
                //$url = url()->query($url, [$key => '']);
                $url = $this->table->url([Table::QUERY_ORDER => '']);
            }
        } else {
            // set to ASC
            //$url = url()->query($url, [$key => $orderBy]);
            $url = $this->table->url([Table::QUERY_ORDER => $orderBy]);
        }

        return $url;
    }

    public function getOrderByDir():string
    {
        $orderBy = $this->getOrderBy();
        $tableOrderBy = $this->getTable()->getOrderBy();

        // if ordered by current cell
        if (str_replace('-', '', $tableOrderBy) == $orderBy) {
            if (str_starts_with($tableOrderBy, '-')) {
                return 'desc';
            } elseif (!empty($this->getTable()->getOrderBy())) {
                return 'asc';
            }
        }
        return '';
    }

}
