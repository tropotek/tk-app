
# `Modules\Core\Table` — Developer Guide

This guide walks through building table pages using the Core Table system.

Two implementations are supported:

1. **Livewire** — reactive, URL-synced, single-file page component using `IsLivewireTable`
2. **Controller** — classic request-driven page using `IsTable`

---

## Table of Contents

1. [Livewire table](#1-livewire-table)
    - [Traits](#11-traits)
    - [Configuring the table with `Builder::build()`](#12-configuring-the-table-with-builderbuild)
    - [Defining cells](#13-defining-cells)
    - [Defining action cells](#14-defining-action-cells)
    - [Defining filters](#15-defining-filters)
    - [Enabling search](#16-enabling-search)
    - [The `query()` and `rows()` methods](#17-the-query-and-rows-methods)
    - [Rendering the table](#18-rendering-the-table)
    - [Full Livewire example](#19-full-livewire-example)
2. [Controller table](#2-controller-table)
3. [Cell & Filter API reference](#3-cell--filter-api-reference)
4. [Common patterns](#4-common-patterns)

---

## 1. Livewire Table

### 1.1 Traits

Add these three traits to your Livewire component:

```php
use Livewire\WithPagination;
use Modules\Core\Table\IsLivewireTable;
use Modules\Core\Table\IsSearchable;

class extends Component {
    use WithPagination, IsLivewireTable, IsSearchable;
}
```

| Trait | Purpose |
|---|---|
| `WithPagination` | Laravel/Livewire pagination |
| `IsLivewireTable` | Table state (sort, dir, limit, filterVals), URL sync, helpers |
| `IsSearchable` | Adds `$search`, `$searchPlaceholder`, `$searchClear` properties |

`IsSearchable` is optional — omit it if the table does not need a search input.

---

### 1.2 Configuring the table with `Builder::build()`

Call `Builder::build($this, [...])` inside `boot()`. This is the primary way to define the entire table configuration in one place.

```php
use Modules\Core\Table\Builder;

public function boot(): void
{
    Builder::build($this, [
        'defaultSort'  => 'family_name',       // default sort column (DB key)
        'defaultDir'   => 'asc',               // 'asc' (default) or 'desc'
        'tableId'      => 'tbl',               // optional; used to prefix URL params
        'rowAttrs'     => fn($row, $table) => [
            'data-url' => route('staff.show', $row->id),
        ],
        'cells'        => [ ... ],
        'actions'      => [ ... ],
        'filters'      => [ ... ],
        'search'       => [ ... ],
        'export'       => [ ... ],
    ]);
}
```

`rowAttrs` is an optional callable that returns an array of HTML attributes applied to each `<tr>`. Clicking a row with a `data-url` attribute navigates to that URL (handled by Alpine.js in the table component).

---

### 1.3 Defining cells

Each entry in `'cells'` is keyed by the column name (used to read the value from the row object by default).

```php
'cells' => [
    'name_last_first' => [
        'header'      => 'Name',           // column heading (auto-generated from key if omitted)
        'sortable'    => true,             // enables sort click on header
        'sort'        => 'family_name',    // DB column to ORDER BY (defaults to key name)
        'text'        => fn($row, $cell) => $row->family_name . ', ' . $row->given_name,
        'html'        => fn($row, $cell) => Cell::makeLinkView(
                             route('staff.show', $row->id),
                             $cell->text($row)
                         ),
        'class'       => 'fw-bold',        // CSS class on <td>
        'headerClass' => 'text-nowrap',    // CSS class on <th>
        'attrs'       => ['data-foo' => 'bar'],        // extra <td> attributes
        'headerAttrs' => ['title' => 'Sort by name'], // extra <th> attributes
        'visible'     => true,             // hide the column when false
    ],

    // Minimal cell — header and value derived from the key name automatically
    'email' => [],

    // Sortable with a custom text renderer
    'created_at' => [
        'header'   => 'Created',
        'sortable' => true,
        'text'     => fn($row) => Carbon::parse($row->created_at)->format('d M Y'),
    ],
],
```

**`text` vs `html`**

- `text($row, $cell)` — plain text value, used in CSV exports and as the fallback for `html`.
- `html($row, $cell)` — HTML markup rendered in the table cell. Falls back to `text()` when not set.

Both are callables with signature `fn(mixed $row, Cell $cell): string`.

**Auto-generated headers**

If `header` is omitted, the column name is converted automatically:
`family_name` → `Family Name`, `created_at_id` → `Created At`.

**`Cell::makeLinkView()`**

A static helper to render an anchor tag:

```php
Cell::makeLinkView(string $route, string $text, array $attrs = []): string
```

---

### 1.4 Defining action cells

Action columns render icon links. They use `ActionCell` internally.

```php
'actions' => [
    'view' => [
        'icon'    => 'fa fa-fw fa-eye',
        'route'   => fn($row, $cell) => route('staff.show', $row),  // href for the link
        'visible' => true,                                           // optional
        'attrs'   => ['class' => 'text-primary'],                    // extra <td> attributes
    ],

    'edit' => [
        'icon'    => 'fa fa-fw fa-pen-to-square',
        'route'   => fn($row, $cell) => route('staff.edit', $row),
        'visible' => auth()->user()->can('change-staff'),            // hides column when false
    ],

    // Custom HTML renderer — returning null falls back to the default icon link
    'delete' => [
        'icon'  => 'fa fa-fw fa-trash',
        'route' => fn($row) => route('staff.destroy', $row),
        'html'  => function ($row, $cell) {
            if (!$row->can_delete) {
                return '<span class="text-muted"><i class="fa fa-fw fa-trash"></i></span>';
            }
            return null; // falls back to default icon link using 'route'
        },
    ],
],
```

`route` is a callable that returns the URL for the default icon link.
`html` is an optional callable for complete control over the cell output. Return `null` or a non-string to fall back to the default icon link.

---

### 1.5 Defining filters

Filters are rendered as form controls above the table. Their values are stored in `$this->filterVals[key]`.

```php
'filters' => [
    'country' => [
        'label'   => 'Country',               // shown as the default/empty option label
        'type'    => 'select',                // 'select' (default) | 'text' | 'checkbox' | 'date'
        'options' => fn() => Country::orderBy('name')->pluck('name', 'code')->toArray(),
        'visible' => true,
    ],

    'given_name' => [
        'options' => fn() => Staff::distinct()->orderBy('given_name')->pluck('given_name', 'given_name')->toArray(),
    ],

    // Dependent filter — options reload when the parent changes
    'family_name' => [
        'dependsOn' => 'given_name',          // key of the parent filter
        'options'   => fn($parentKey) => Staff::query()
            ->when($this->filterVals[$parentKey] ?? null,
                fn($q, $v) => $q->where($parentKey, $v))
            ->distinct()
            ->orderBy('family_name')
            ->pluck('family_name', 'family_name')
            ->toArray(),
    ],
],
```

**Filter types**

| Type | Component rendered |
|---|---|
| `select` (default) | `<select>` dropdown |
| `text` | `<input type="text">` |
| `checkbox` | Toggle switch |
| `date` | `<input type="date">` |

**Dependent filters**

Set `dependsOn` to the key of another filter. When the parent filter changes, the child filter value is cleared automatically via `updateFilters()`.

The `options` callable receives the `$parentKey` string (the value of `dependsOn`). Access the parent's current value via `$this->filterVals[$parentKey]`.

**Reading filter values in `query()`**

```php
$this->filterVals['country'] ?? null
```

**Default values**

```php
'status' => [
    'type'         => 'select',
    'options'      => ['active' => 'Active', 'inactive' => 'Inactive'],
    'defaultValue' => 'active',
],
```

---

### 1.6 Enabling search

Add `IsSearchable` to your traits, then configure search in the builder:

```php
'search' => [
    'placeholder'  => 'Search name, email...',
    'clearFilters' => ['given_name', 'family_name'], // filter keys to clear when search changes
],
```

The search input value is available as `$this->search`.

---

### 1.7 The `query()` and `rows()` methods

Split data retrieval into two methods:

**`query(): Builder`** — builds and returns the filtered (unpaginated) Eloquent query.

```php
protected function query(): Builder
{
    $query = Staff::with('country');

    if ($this->isSearchable()) {
        $query->when($this->search, function (Builder $q) {
            $str = preg_replace("/[^a-zA-Z0-9' -]/", ' ', $this->search);
            return $q->where('name', 'like', "%{$str}%")
                     ->orWhere('email', 'like', "%{$str}%");
        });
    }

    $query->when($this->filterVals['country'] ?? null,     fn($q, $v) => $q->where('country', $v))
          ->when($this->filterVals['given_name'] ?? null,  fn($q, $v) => $q->where('given_name', $v))
          ->when($this->filterVals['family_name'] ?? null, fn($q, $v) => $q->where('family_name', $v));

    return $query;
}
```

**`rows(): LengthAwarePaginator`** — paginates and sorts using the table helpers. Mark it `#[Computed]` so Livewire caches the result within a single render cycle.

```php
use Livewire\Attributes\Computed;
use Illuminate\Pagination\LengthAwarePaginator;

#[Computed]
public function rows(): LengthAwarePaginator
{
    return $this->paginateQuery($this->query());
}
```

`paginateQuery()` applies `sortQuery()` internally (using `safeSort()` and `getDir()`), then paginates with the correct page name.

---

### 1.8 Rendering the table

Use two Blade components in your view:

```blade
<div>
    {{-- Filter bar: search input, filter controls, row count, and optional actions slot --}}
    <x-core::table.filters :table="$this">
        <x-slot name="actions">
            @can('add-staff')
                <a href="{{ route('staff.create') }}" class="btn btn-sm btn-primary">+ New Staff</a>
            @endcan
        </x-slot>
    </x-core::table.filters>

    {{-- Table: headers, rows, pagination --}}
    <x-core::table :table="$this"/>
</div>
```

The `actions` slot is optional. Use it for buttons placed in the filter bar (e.g. "New" / "Export").

**Row click navigation**

If `rowAttrs` returns a `data-url` attribute, clicking anywhere on the row (except links and buttons) navigates to that URL. This is handled by Alpine.js in the table component.

---

### 1.9 Full Livewire example

```php
<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Table\Builder as TableBuilder;
use Modules\Core\Table\Cell;
use Modules\Core\Table\IsLivewireTable;
use Modules\Core\Table\IsSearchable;
use Modules\Staff\Models\Staff;

new #[Layout('layouts.main')]
class extends Component {

    use WithPagination, IsLivewireTable, IsSearchable;

    public function boot(): void
    {
        TableBuilder::build($this, [
            'defaultSort' => 'family_name',
            'rowAttrs'    => fn($row) => ['data-url' => route('staff.show', $row->id)],
            'cells' => [
                'name_last_first' => [
                    'header'   => 'Name',
                    'sortable' => true,
                    'sort'     => 'family_name',
                    'text'     => fn($row) => $row->family_name . ', ' . $row->given_name,
                    'html'     => fn($row, $cell) => Cell::makeLinkView(
                                      route('staff.show', $row->id), $cell->text($row)
                                  ),
                ],
                'email'      => [],
                'created_at' => [
                    'header'   => 'Created',
                    'sortable' => true,
                    'text'     => fn($row) => Carbon::parse($row->created_at)->format('d M Y'),
                ],
            ],
            'actions' => [
                'view' => [
                    'icon'  => 'fa fa-fw fa-eye',
                    'route' => fn($row) => route('staff.show', $row),
                ],
                'edit' => [
                    'icon'    => 'fa fa-fw fa-pen-to-square',
                    'route'   => fn($row) => route('staff.edit', $row),
                    'visible' => auth()->user()->can('change-staff'),
                ],
            ],
            'filters' => [
                'country' => [
                    'options' => fn() => Staff::distinct()->orderBy('country')->pluck('country', 'country')->toArray(),
                ],
                'given_name' => [
                    'options' => fn() => Staff::distinct()->orderBy('given_name')->pluck('given_name', 'given_name')->toArray(),
                ],
                'family_name' => [
                    'dependsOn' => 'given_name',
                    'options'   => fn($k) => Staff::query()
                        ->when($this->filterVals[$k] ?? null, fn($q, $v) => $q->where($k, $v))
                        ->distinct()->orderBy('family_name')
                        ->pluck('family_name', 'family_name')->toArray(),
                ],
            ],
            'search' => [
                'placeholder'  => 'Search name, email...',
                'clearFilters' => ['given_name', 'family_name'],
            ],
        ]);
    }

    protected function query(): Builder
    {
        $query = Staff::query();

        $query->when($this->search, function (Builder $q) {
            $str = preg_replace("/[^a-zA-Z0-9' -]/", ' ', $this->search);
            return $q->where('name', 'like', "%{$str}%")
                     ->orWhere('email', 'like', "%{$str}%");
        });

        $query->when($this->filterVals['country'] ?? null,     fn($q, $v) => $q->where('country', $v))
              ->when($this->filterVals['given_name'] ?? null,  fn($q, $v) => $q->where('given_name', $v))
              ->when($this->filterVals['family_name'] ?? null, fn($q, $v) => $q->where('family_name', $v));

        return $query;
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return $this->paginateQuery($this->query());
    }
};
?>
<div>
    <x-core::table.filters :table="$this">
        <x-slot name="actions">
            <a href="{{ route('staff.create') }}" class="btn btn-sm btn-primary">+ New Staff</a>
        </x-slot>
    </x-core::table.filters>

    <x-core::table :table="$this"/>
</div>
```

---

## 2. Controller Table

Use `IsTable` when the page is request-driven (no Livewire).

```php
<?php

namespace Modules\Staff\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\Table\Builder as TableBuilder;
use Modules\Core\Table\IsTable;
use Modules\Staff\Models\Staff;

class StaffController extends Controller
{
    use IsTable;

    public function index(Request $request)
    {
        // Handle clear/reset — redirect to strip all table query params
        if ($request->has($this->tableKey('reset'))) {
            return redirect(url()->current());
        }

        TableBuilder::build($this, [
            'defaultSort' => 'family_name',
            'cells' => [
                'name_last_first' => [
                    'header'   => 'Name',
                    'sortable' => true,
                    'sort'     => 'family_name',
                ],
                'email'      => ['sortable' => true],
                'created_at' => ['header' => 'Created', 'sortable' => true],
            ],
        ]);

        // Hydrate sort/limit/dir from the request query string
        $this->hydrateTableFromRequest();

        return view('staff.index', ['table' => $this]);
    }

    public function rows(): LengthAwarePaginator
    {
        return $this->paginateQuery(Staff::query());
    }
}
```

In the Blade view:

```blade
<x-core::table.filters :table="$table"/>
<x-core::table :table="$table"/>
```

**Note:** Controller-based tables do not yet support reactive filters. Filter values must be read from the request manually and applied inside `rows()`. Dependent filter dropdowns require a JavaScript/Alpine approach for dynamic option reloading.

---

## 3. Cell & Filter API Reference

### `Builder::build()` — full schema

```php
Builder::build($this, [
    'tableId'     => 'tbl',           // string; 'tbl' = no URL prefix (default)
    'defaultSort' => 'column_name',   // string
    'defaultDir'  => 'asc',           // 'asc' (default) | 'desc'
    'rowAttrs'    => fn($row, $table): array => [],

    'cells' => [
        'column_key' => [
            'header'      => string,
            'sortable'    => bool,
            'sort'        => string,    // DB column for ORDER BY; defaults to key
            'text'        => callable,  // fn($row, $cell): string
            'html'        => callable,  // fn($row, $cell): string
            'class'       => string,    // CSS class on <td>
            'headerClass' => string,    // CSS class on <th>
            'attrs'       => array,     // extra <td> attributes
            'headerAttrs' => array,     // extra <th> attributes
            'visible'     => bool,
        ],
    ],

    'actions' => [
        'action_key' => [
            'icon'        => string,    // font icon class e.g. 'fa fa-fw fa-eye'
            'route'       => callable,  // fn($row, $cell): string — href for default icon link
            'html'        => callable,  // fn($row, $cell): string — full override; return null to use default
            'header'      => string,    // optional label (shown as icon title tooltip by default)
            'class'       => string,
            'headerClass' => string,
            'attrs'       => array,
            'headerAttrs' => array,
            'visible'     => bool,
        ],
    ],

    'filters' => [
        'filter_key' => [
            'label'        => string,
            'type'         => 'select' | 'text' | 'checkbox' | 'date',
            'options'      => array | callable, // fn(string $parentKey): array
            'dependsOn'    => string,   // key of the parent filter
            'defaultValue' => string,
            'visible'      => bool,
        ],
    ],

    'search' => [
        'placeholder'  => string,
        'clearFilters' => array,  // filter keys to clear when search changes
    ],
]);
```

---

### Fluent cell API (without Builder)

Cells and filters can also be added individually using the fluent API:

```php
// Append a cell
$cell = $this->appendCell(new Cell('email'));
$cell->setSortable()
     ->setHeader('Email Address')
     ->addClass('text-lowercase')
     ->setHtml(fn($row, $cell) => Cell::makeLinkView('mailto:'.$row->email, $row->email));

// Insert a cell after another
$this->appendCell(new Cell('phone'), after: 'email');

// Prepend a cell before another
$this->prependCell(new Cell('id'), before: 'email');

// Remove a cell
$this->removeCell('phone');

// Append a filter
$this->appendFilter(new Filter('status', type: Filter::TYPE_SELECT, options: [
    'active'   => 'Active',
    'inactive' => 'Inactive',
]));
```

---

### Useful table helpers

| Method | Description |
|---|---|
| `safeSort()` | Returns the validated sort column (falls back to `defaultSort`) |
| `getDir()` | Returns `'asc'` or `'desc'` |
| `getLimit()` | Returns the current per-page limit |
| `paginateQuery(Builder $query)` | Sorts and paginates a query; returns `LengthAwarePaginator` |
| `paginateArray(array $rows)` | Paginates a plain array |
| `buildCsv($rows, $fileName)` | Returns a CSV stream download response |
| `isSearchable()` | Returns true if `IsSearchable` trait is in use |
| `isLivewire()` | Returns true when using `IsLivewireTable` |
| `tableKey(string $key)` | Prefixes a URL param key with the `tableId` |

---

## 4. Common Patterns

### Conditional cell visibility

```php
'visible' => auth()->user()->can('view-salary'),
```

### Modifying a cell after build

```php
Builder::build($this, [...]);

$this->getCell('email')->addClass('text-danger');
$this->removeCell('phone');
```

### Custom sort key

When the column key and the DB sort column differ:

```php
'name_last_first' => [
    'sortable' => true,
    'sort'     => 'family_name',  // ORDER BY family_name
],
```

### Multiple tables on one page

Set a unique `tableId` on each table to avoid URL key conflicts:

```php
// Table 1
Builder::build($this->staffTable, ['tableId' => 'staff', ...]);

// Table 2
Builder::build($this->leaveTable, ['tableId' => 'leave', ...]);
```

URL params become `staff_s`, `staff_f`, `leave_s`, `leave_f`, etc.

### Row click navigation

Return `data-url` from `rowAttrs` to make the whole row clickable:

```php
'rowAttrs' => fn($row) => ['data-url' => route('staff.show', $row->id)],
```

Clicking a link or button inside the row does not trigger navigation.
