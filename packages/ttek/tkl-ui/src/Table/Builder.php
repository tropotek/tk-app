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
     *     'columns'        => [
     *         'column_name' => [
     *             'header'      => string,    // (optional) column label
     *             'sortable'    => bool,      // (optional) is column sortable
     *             'sort'        => string,    // (optional) DB/sort key
     *             'value'       => string|callable,  // (optional) fn($row, $column): string
     *             'view'        => string|callable,  // (optional) fn($row, $column): string
     *             'class'       => string,    // (optional) td CSS class
     *             'attrs'       => array,     // (optional) td attrs
     *             'headerClass' => string,    // (optional) th CSS class
     *             'headerAttrs' => array,     // (optional) th attrs
     *             'visible'     => bool,      // (optional) visibility flag
     *         ],
     *     ],
     *     'actions'      => [
     *         'action_name' => [
     *             'icon'        => string,    // (optional) font based icon class
     *             'route'       => string|callable,  // (optional) fn($row, $column): string (alias for value callback)
     *             'view'        => string|callable,  // (optional) fn($row, $column): string
     *             'class'       => string,    // (optional) td CSS class
     *             'attrs'       => array,     // (optional) td attrs
     *             'headerClass' => string,    // (optional) th CSS class
     *             'visible'     => bool,      // (optional) visibility flag
     *             'headerAttrs' => array,     // (optional) th attrs
     *         ],
     *     ],
     *     'filters'      => [
     *         'filter_key' => [
     *             'label'        => string,         // (optional) filter label
     *             'type'         => string,         // (optional) select|text|checkbox|date
     *             'options'      => array|callable, // (optional) fn(?string $parentValue): array
     *             'dependsOn'    => string,         // (optional) parent filter key
     *             'defaultValue' => mixed,          // (optional) default value
     *             'class'        => string,         // (optional) td CSS class
     *             'attrs'        => array,          // (optional) td attrs
     *             'visible'      => bool,           // (optional) visibility flag
     *         ],
     *     ],
     *     'search' => [
     *         'enabled' => true,                 // (optional) enable/disable search
     *         'placeholder' => 'Search...',      // (optional) placeholder text
     *         'clearFilters' => [],              // (optional) array of filter keys to clear on search
     *     ],
     * ]
     *
     * Notes:
     * - `defaultSort` and `defaultDir` are applied together.
     * - `columns`, `actions`, and `filters` are keyed collections.
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
                'class'    => $table->addClass($meta),
                'attrs'    => $table->addAttrs($meta),
                'rowAttrs' => $table->setRowAttrs($meta),
                'columns'    => self::buildColumns($table, $meta),
                'actions'  => self::buildActions($table, $meta),
                'filters'  => self::buildFilters($table, $meta),
                'search'   => self::buildSearch($table, $meta),
                default    => null,
            };
        }
    }

    /**
     * Build and append one or more Column objects to the table.
     * Accepts a single column definition array or an array of definitions.
     */
    protected static function buildColumns(mixed $table, array $meta): void
    {
        foreach ($meta as $name => $columnMeta) {
            $table->appendColumn(self::buildColumn($name, $columnMeta));
        }
    }

    protected static function buildColumn(string $name, array $meta): Column
    {
        $column = new Column(
            name:     $name,
            header:   $meta['header'] ?? '',
            sortable: $meta['sortable'] ?? false,
            value:    is_callable($meta['value'] ?? null) ? $meta['value'] : null,
            view:     is_callable($meta['view'] ?? null) ? $meta['view'] : null,
            sort:     $meta['sort'] ?? '',
            visible:  $meta['visible'] ?? true,
        );

        $column->addClass($meta['class'] ?? '');
        $column->addAttrs($meta['attrs'] ?? []);

        $column->addHeaderClass($meta['headerClass'] ?? '');
        $column->addHeaderAttrs($meta['headerAttrs'] ?? []);

        return $column;
    }

    /**
     * Build and append one or more ActionColumn objects to the table.
     * Accepts a single action definition array or an array of definitions.
     */
    protected static function buildActions(mixed $table, array $meta): void
    {
        foreach ($meta as $name => $actionMeta) {
            $table->appendColumn(self::buildAction($name, $actionMeta));
        }
    }

    protected static function buildAction(string $name, array $meta): ActionColumn
    {
        $column = new ActionColumn(
            name:    $name,
            icon:    $meta['icon'] ?? '',
            route:   is_callable($meta['route'] ?? null) ? $meta['route'] : null,
            view:    is_callable($meta['view'] ?? null) ? $meta['view'] : null,
            visible: $meta['visible'] ?? true,
        );

        if (!empty($meta['header'])) {
            $column->setHeader($meta['header']);
        }

        $column->addClass($meta['class'] ?? '');
        $column->addAttrs($meta['attrs'] ?? []);

        $column->addHeaderClass($meta['headerClass'] ?? '');
        $column->addHeaderAttrs($meta['headerAttrs'] ?? []);

        return $column;
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

        $filter->addClass($meta['class'] ?? '');
        $filter->addAttrs($meta['attrs'] ?? []);

        return $filter;
    }

    protected static function buildSearch(mixed $table, array $meta): void
    {
        $table->searchPlaceholder = (string)($meta['placeholder'] ?? '');
        $table->searchClear = $meta['clearFilters'] ?? [];
        $table->searchEnabled = (bool)($meta['enabled'] ?? true);
    }

}
