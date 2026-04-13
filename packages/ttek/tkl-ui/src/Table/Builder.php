<?php

namespace Tk\Table;


class Builder
{
    /**
     * Build a table from a metadata array.
     *
     * usage: Builder::build($table, $tableMeta);
     *
     * Look into the Table objects for more detailed info on each property.
     *
     * tableMeta structure:
     *
     * [
     *     'defaultSort'  => string,    // (optional) default sort column
     *     'defaultDir'   => string,    // (optional) default sort direction ('asc'|'desc')
     *     'attrs'        => array,     // (optional) table attrs
     *     'rowAttrs'     => callable,  // (optional) fn($row, $table): array|ComponentAttributeBag
     *     'cells'        => [
     *         'cell_name' => [
     *             'header'      => string,    // (optional) column label
     *             'sortable'    => bool,      // (optional) is cell sortable
     *             'sort'        => string,    // (optional) DB/sort key
     *             'value'       => string|callable,  // (optional) fn($row, $cell): string
     *             'view'        => string|callable,  // (optional) fn($row, $cell): string
     *             'class'       => string,    // (optional) td CSS class
     *             'headerClass' => string,    // (optional) th CSS class
     *             'visible'     => bool,      // (optional) visibility flag
     *             'attrs'       => array,     // (optional) td attrs
     *             'headerAttrs' => array,     // (optional) th attrs
     *         ],
     *     ],
     *     'actions'      => [
     *         'action_name' => [
     *             'icon'        => string,    // (optional) font based icon class
     *             'route'       => string|callable,  // (optional) fn($row, $cell): string (alias for value callback)
     *             'view'        => string|callable,  // (optional) fn($row, $cell): string
     *             'class'       => string,    // (optional) td CSS class
     *             'headerClass' => string,    // (optional) th CSS class
     *             'visible'     => bool,      // (optional) visibility flag
     *             'attrs'       => array,     // (optional) td attrs
     *             'headerAttrs' => array,     // (optional) th attrs
     *         ],
     *     ],
     *     'filters'      => [
     *         'filter_key' => [
     *             'label'     => string,         // (optional) filter label
     *             'type'      => string,         // (optional) select|text|checkbox|date
     *             'options'   => array|callable, // (optional) fn(?string $parentValue): array
     *             'dependsOn' => string,         // (optional) parent filter key
     *             'defaultValue' => mixed,       // (optional) default value
     *             'visible'   => bool,           // (optional) visibility flag
     *         ],
     *     ],
     *     'search' => [
     *         'enabled' => true,                 // (optional) enable search
     *         'placeholder' => 'Search...',      // (optional) placeholder text
     *         'clearFilters' => [],              // (optional) array of filter keys to clear on search
     *     ],
     *     'export'       => [
     *         // export configuration, if supported
     *     ],
     * ]
     *
     * Notes:
     * - `defaultSort` and `defaultDir` are applied together.
     * - `cells`, `actions`, and `filters` are keyed collections.
     * - The builder ignores unknown keys.
     */
    public static function build(mixed $table, array $tableMeta): void
    {
        // defaultSort and defaultDir are set together via setDefaultSort()
        $defaultSort = $tableMeta['defaultSort'] ?? '';
        $defaultDir  = $tableMeta['defaultDir'] ?? '';
        if ($defaultSort !== '' || $defaultDir !== '') {
            $table->setDefaultSort($defaultSort, $defaultDir);
        }

        foreach ($tableMeta as $key => $meta) {
            match ($key) {
                'attrs'    => $table->setAttrs($meta),
                'rowAttrs' => $table->setRowAttrs($meta),
                'cells'    => self::buildCells($table, $meta),
                'actions'  => self::buildActions($table, $meta),
                'filters'  => self::buildFilters($table, $meta),
                'search'   => self::buildSearch($table, $meta),
                'export'   => self::buildExport($meta),
                default    => null,
            };
        }
    }

    /**
     * Build and append one or more Cell objects to the table.
     * Accepts a single cell definition array or an array of definitions.
     */
    protected static function buildCells(mixed $table, array $meta): void
    {
        foreach ($meta as $name => $cellMeta) {
            $table->appendCell(self::buildCell($name, $cellMeta));
        }
    }

    protected static function buildCell(string $name, array $meta): Cell
    {
        $cell = new Cell(
            name:     $name,
            header:   $meta['header'] ?? '',
            sortable: $meta['sortable'] ?? false,
            value:    is_callable($meta['value'] ?? null) ? $meta['value'] : null,
            view:     is_callable($meta['view'] ?? null) ? $meta['view'] : null,
            sort:     $meta['sort'] ?? '',
            visible:  $meta['visible'] ?? true,
        );

        $cell->addClass($meta['class'] ?? '');
        $cell->mergeAttrs($meta['attrs'] ?? []);

        $cell->addHeaderClass($meta['headerClass'] ?? '');
        $cell->mergeHeaderAttrs($meta['headerAttrs'] ?? []);

        return $cell;
    }

    /**
     * Build and append one or more ActionCell objects to the table.
     * Accepts a single action definition array or an array of definitions.
     */
    protected static function buildActions(mixed $table, array $meta): void
    {
        foreach ($meta as $name => $actionMeta) {
            $table->appendCell(self::buildAction($name, $actionMeta));
        }
    }

    protected static function buildAction(string $name, array $meta): ActionCell
    {
        $cell = new ActionCell(
            name:    $name,
            icon:    $meta['icon'] ?? '',
            route:   is_callable($meta['route'] ?? null) ? $meta['route'] : null,
            view:    is_callable($meta['view'] ?? null) ? $meta['view'] : null,
            visible: $meta['visible'] ?? true,
        );

        if (!empty($meta['header'])) {
            $cell->setHeader($meta['header']);
        }

        $cell->addClass($meta['class'] ?? '');
        $cell->mergeAttrs($meta['attrs'] ?? []);

        $cell->addHeaderClass($meta['headerClass'] ?? '');
        $cell->mergeHeaderAttrs($meta['headerAttrs'] ?? []);

        return $cell;
    }

    /**
     * Build and append one or more Filter objects to the table.
     * Accepts a single filter definition array or an array of definitions.
     */
    protected static function buildFilters(mixed $table, array $meta): void
    {
        foreach ($meta as $key => $filterMeta) {
            $table->appendFilter(self::buildFilter($key, $filterMeta));
        }
    }

    protected static function buildFilter(string $key, array $meta): Filter
    {
        $filter = new Filter(
            key:          $key,
            label:        $meta['label'] ?? '',
            type:         $meta['type'] ?? Filter::TYPE_SELECT,
            options:      $meta['options'] ?? [],
            visible:      $meta['visible'] ?? true,
            dependsOn:    $meta['dependsOn'] ?? '',
            defaultValue: $meta['defaultValue'] ?? '',
        );

        $filter->mergeAttrs($meta['attrs'] ?? []);

        return $filter;
    }

    protected static function buildSearch(mixed $table, array $meta): void
    {
        if (!$table->isSearchable()) {
            throw new \InvalidArgumentException("Use the 'IsSearchable' trait to enable search.");
        }

        $table->searchPlaceholder = $meta['placeholder'] ?? '';
        $table->searchClear = $meta['clearFilters'] ?? [];
    }

    protected static function buildExport(array $meta): void
    {
        // todo mm To implement later
    }

}
