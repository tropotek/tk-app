<?php

namespace Tk\Tbl;

class Cell
{
    public string $name = '';
    public string $header = '';
    public bool $sortable = false;
    public string $sort = '';
    public bool $visible = true;

    protected mixed $text = null;  // null|callable
    protected mixed $html = null;  // null|callable
    protected ?Table $table = null;


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
            return call_user_func($this->text, $row);
        }
        if (is_array($row)) {
            return $row[$this->name] ?? '';
        }
        if (is_object($row)) {
            return $row->{$this->name} ?? '';
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

    public function getTable(): ?Table
    {
        return $this->table;
    }

    public function setTable(?Table $table): static
    {
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
     * @todo: we should use the table dir
     */
//    public function getNextSortDir(): string
//    {
//        if (!$this->getTable()) {
//            return '';
//        }
//        if (!$this->isSortable()) {
//            return '';
//        }
//        return match ($this->getTable()->dir) {
//            'asc' => 'desc',
//            'desc' => 'asc',
//            default => '',
//        };
//    }

}
