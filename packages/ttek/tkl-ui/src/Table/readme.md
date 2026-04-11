
# `Tk\Table` — Developer Guide

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
   - [The `rows()` method](#17-the-rows-method)
   - [Rendering the table](#18-rendering-the-table)
   - [Full Livewire example](#19-full-livewire-example)
2. [Controller table](#2-controller-table)
3. [Cell & Filter API reference](#3-cell--filter-api-reference)
4. [Common patterns](#4-common-patterns)

---

## 1. Livewire Table

### 1.1 Traits

Add these traits to your Livewire component:

```php
use Livewire\WithPagination;
use Tk\Table\IsLivewireTable;
use Tk\Table\IsSearchable;

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
use Tk\Table\Builder;

public function boot(): void
{
    Builder::build($this, [
        'defaultSort' => 'family_name',        // default sort column (DB key)
        'defaultDir'  => 'asc',                // 'asc' (default) or 'desc'
        'tableId'     => 'tbl',                // optional; used to prefix URL params
        'rowAttrs'    => fn($row, $table) => [
            'data-url' => route('staff.show', $row->id),
        ],
        'cells'   => [ ... ],
        'actions' => [ ... ],
        'filters' => [ ... ],
        'search'  => [ ... ],
    ]);
}
```

`rowAttrs` is an optional callable that returns an array of HTML attributes applied to each `<tr>`. Clicking a row with a `data-url` attribute navigates to that URL (handled by Alpine.js in the table component).

---

### 1.3 Defining cells

Each entry in `'cells'` is keyed by the column name (used to read the value from the row by default).

```php
'cells' => [
    'name_last_first' => [
        'header'      => 'Name',           // column heading (auto-generated from key if omitted)
        'sortable'    => true,             // enables sort click on header
        'sort'        => 'family_name',    // DB column to ORDER BY (defaults to key name)
        'value'       => fn($row, $cell) => $row->family_name . ', ' . $row->given_name,
        'view'        => fn($row, $cell) => view('core::components.table.cells.a', [
                             'href' => route('staff.show', $row->id),
                             'text' => $cell->value($row),
                         ]),
        'class'       => 'fw-bold',        // CSS class on <td>
        'headerClass' => 'text-nowrap',    // CSS class on <th>
        'attrs'       => ['data-foo' => 'bar'],        // extra <td> attributes
        'headerAttrs' => ['title' => 'Sort by name'], // extra <th> attributes
        'visible'     => true,             // hide the column when false
    ],

    // Minimal cell — header and value derived from the key name automatically
    'email' => [],

    // Sortable cell using a built-in formatter
    'created_at' => [
        'header'   => 'Created',
        'sortable' => true,
        'value'    => '\Tk\Table\Formats::date',
    ],
],
```

**`value` vs `view`**

- `value($row, $cell)` — plain text value, used in CSV exports and as the fallback for `view`.
- `view($row, $cell)` — HTML markup rendered in the table cell. Falls back to `value()` when not set.

Both accept a callable with signature `fn(mixed $row, Cell $cell): string`, or a string callable like `'ClassName::method'`.

**Auto-generated headers**

If `header` is omitted, the column name is converted automatically:
`family_name` → `Family Name`, `created_at_id` → `Created At`.

**Cell view helpers**

Two small Blade components are available for common cell outputs:

```php
// Renders: <a href="..."><i class="..."></i> Text</a>
view('core::components.table.cells.a', [
    'href'  => route('staff.show', $row->id),
    'text'  => $cell->value($row),   // optional
    'icon'  => 'fa fa-eye',          // optional
    'title' => 'View staff',         // optional
]);

// Renders: <span title="..."><i class="..."></i> Text</span>
view('core::components.table.cells.icon', [
    'icon'  => 'fa fa-check text-success',
    'text'  => 'Active',   // optional
    'title' => 'Active',   // optional
]);
```

**Accessing raw row values inside a `view` callback**

Inside a `view` callback, use `$cell->getRowValue($row)` to get the unformatted row value (bypassing any `value` callable). Use `$cell->value($row)` to get the formatted/escaped text value.

```php
'view' => function ($row, $cell) {
    if (!auth()->user()->can(Ability::ChangeStaff->value)) {
        return $cell->value($row);  // plain text fallback
    }
    return view('core::components.table.cells.a', [
        'href' => route('staff.show', $row->id),
        'text' => $cell->value($row),
    ]);
},
```

---

### 1.4 Defining action cells

Action columns render icon links. They use `ActionCell` internally.

```php
'actions' => [
    'view' => [
        'icon'    => 'fa fa-fw fa-eye',
        'route'   => fn($row) => route('staff.show', $row),  // href for the default icon link
        'attrs'   => ['class' => 'text-center', 'title' => 'View'],
        'visible' => true,                                    // optional
    ],

    'edit' => [
        'icon'    => 'fa fa-fw fa-pen-to-square',
        'route'   => fn($row) => route('staff.edit', $row),
        'attrs'   => ['class' => 'text-center'],
        'visible' => auth()->user()->can('change-staff'),     // hides entire column when false
    ],

    // Custom renderer — overrides the default icon link entirely
    'delete' => [
        'icon'  => 'fa fa-fw fa-trash',
        'route' => fn($row) => route('staff.destroy', $row),
        'view'  => function ($row, $cell) {
            $icon = $row->can_delete ? 'fa fa-fw fa-trash' : 'fa fa-fw';
            return view('core::components.table.cells.a', [
                'href' => route('staff.destroy', $row),
                'icon' => $icon,
            ]);
        },
    ],
],
```

`route` is a callable that returns the URL for the default icon link.
`view` is an optional callable for complete control over the cell output.

---

### 1.5 Defining filters

Filters are rendered as form controls above the table. Their values are stored in `$this->filterVals[key]` and can be read inside `rows()`.

```php
'filters' => [
    'country' => [
        'label'   => 'Country',               // shown as the default/empty option label
        'type'    => 'select',                // 'select' (default) | 'text' | 'checkbox' | 'date'
        'options' => fn() => Staff::distinct()->orderBy('country')->pluck('country', 'country')->toArray(),
        'visible' => true,
    ],

    'given_name' => [
        'options' => fn() => Staff::distinct()->orderBy('given_name')->pluck('given_name', 'given_name')->toArray(),
    ],

    // Dependent filter — value is cleared when the parent filter changes
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

    // Filter with a default value
    'status' => [
        'type'         => 'select',
        'options'      => ['active' => 'Active', 'inactive' => 'Inactive'],
        'defaultValue' => 'active',
    ],
],
```

**Filter types**

| Type | Rendered as |
|---|---|
| `select` (default) | `<select>` dropdown |
| `text` | `<input type="text">` |
| `checkbox` | Toggle switch |
| `date` | `<input type="date">` |

**Dependent filters**

Set `dependsOn` to the key of another filter. When the parent changes, the child value is cleared automatically via `updateFilters()`. The `options` callable receives `$parentKey` (the string value of `dependsOn`); use `$this->filterVals[$parentKey]` to get the parent's current value.

**Reading filter values in `rows()`**

```php
$query->when($this->filterVals['country'] ?? null, fn($q, $v) => $q->where('country', $v));
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

The search value is available as `$this->search` inside `rows()`.

---

### 1.7 The `rows()` method

Implement `rows()` as a `#[Computed]` method. It should return a filtered (but **not** sorted or paginated) Eloquent `Builder` or plain `array`. The table component calls `paginatedRows()` internally, which handles sorting and pagination.

```php
use Livewire\Attributes\Computed;
use Illuminate\Database\Eloquent\Builder;

#[Computed]
public function rows(): array|Builder
{
    $query = Staff::with('country');

    // Set total before filtering so the count reflects the full dataset
    $this->totalRows = $query->count();

    if ($this->isSearchable()) {
        $query->when($this->search, function (Builder $q) {
            $str   = preg_replace("/[^a-zA-Z0-9' -]/", ' ', $this->search);
            $email = preg_replace("/[^a-zA-Z0-9@._-]/", '', $this->search);
            return $q->where('name', 'like', "%{$str}%")
                     ->orWhere('email', 'like', "%{$email}%");
        });
    }

    $query->when($this->filterVals['country'] ?? null,     fn($q, $v) => $q->where('country', $v))
          ->when($this->filterVals['given_name'] ?? null,  fn($q, $v) => $q->where('given_name', $v))
          ->when($this->filterVals['family_name'] ?? null, fn($q, $v) => $q->where('family_name', $v));

    return $query;
}
```

Do **not** call `orderBy()` or `paginate()` inside `rows()` — those are handled by the table system.

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

use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Auth\Enums\Ability;
use Modules\Core\Support\Facades\Breadcrumbs;
use Tk\Table\Builder as TableBuilder;
use Tk\Table\IsLivewireTable;
use Tk\Table\IsSearchable;
use Modules\Staff\Models\Staff;

new #[Layout('layouts.main')]
class extends Component {

    use WithPagination, IsLivewireTable, IsSearchable;

    public function boot(): void
    {
        Breadcrumbs::push('Manage Staff');

        TableBuilder::build($this, [
            'defaultSort' => 'family_name',
            'rowAttrs'    => fn($row) => ['data-url' => route('staff.show', $row->id)],

            'cells' => [
                'name_last_first' => [
                    'header'   => 'Name',
                    'sortable' => true,
                    'sort'     => 'family_name',
                    'class'    => 'fw-bold',
                    'view'     => function ($row, $cell) {
                        if (!auth()->user()->can(Ability::ChangeStaff->value)) {
                            return $cell->value($row);
                        }
                        return view('core::components.table.cells.a', [
                            'href' => route('staff.show', $row->id),
                            'text' => $cell->value($row),
                        ]);
                    },
                ],
                'email'      => [],
                'city'       => ['sortable' => true],
                'country'    => ['sortable' => true],
                'phone'      => ['value' => '\Tk\Table\Formats::phone'],
                'created_at' => [
                    'header'   => 'Created',
                    'sortable' => true,
                    'value'    => '\Tk\Table\Formats::date',
                ],
            ],

            'actions' => [
                'view' => [
                    'icon'  => 'fa fa-fw fa-eye',
                    'route' => fn($row) => route('staff.show', $row),
                    'attrs' => ['class' => 'text-center'],
                ],
                'edit' => [
                    'icon'    => 'fa fa-fw fa-pen-to-square',
                    'route'   => fn($row) => route('staff.edit', $row),
                    'attrs'   => ['class' => 'text-center'],
                    'visible' => auth()->user()->can(Ability::ChangeStaff->value),
                ],
            ],

            'filters' => [
                'country' => [
                    'options' => fn() => Staff::query()
                        ->select('country')->distinct()->orderBy('country')
                        ->pluck('country', 'country')->toArray(),
                ],
                'given_name' => [
                    'options' => fn() => Staff::query()
                        ->select('given_name')->distinct()->orderBy('given_name')
                        ->pluck('given_name', 'given_name')->toArray(),
                ],
                'family_name' => [
                    'dependsOn' => 'given_name',
                    'options'   => fn($parentKey) => Staff::query()
                        ->when($this->filterVals[$parentKey] ?? null,
                            fn($q, $v) => $q->where($parentKey, $v))
                        ->select('family_name')->distinct()->orderBy('family_name')
                        ->pluck('family_name', 'family_name')->toArray(),
                ],
            ],

            'search' => [
                'placeholder'  => 'Search Name, Email...',
                'clearFilters' => ['given_name', 'family_name'],
            ],
        ]);
    }

    #[Computed]
    public function rows(): array|Builder
    {
        $query = Staff::with('country');

        $this->totalRows = $query->count();

        if ($this->isSearchable()) {
            $query->when($this->search, function (Builder $q) {
                $str   = preg_replace("/[^a-zA-Z0-9' -]/", ' ', $this->search);
                $email = preg_replace("/[^a-zA-Z0-9@._-]/", '', $this->search);
                return $q->where('name', 'like', "%{$str}%")
                         ->orWhere('email', 'like', "%{$email}%");
            });
        }

        $query->when($this->filterVals['country'] ?? null,     fn($q, $v) => $q->where('country', $v))
              ->when($this->filterVals['given_name'] ?? null,  fn($q, $v) => $q->where('given_name', $v))
              ->when($this->filterVals['family_name'] ?? null, fn($q, $v) => $q->where('family_name', $v));

        return $query;
    }
};
?>
<div>
    <h1>{{ $pageName }}</h1>

    <x-core::table.filters :table="$this">
        <x-slot name="actions">
            @can(Ability::AddStaff->value)
                <a href="{{ route('staff.create') }}" class="btn btn-sm btn-primary">+ New Staff</a>
            @endcan
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
use Illuminate\Database\Eloquent\Builder;
use Tk\Table\Builder as TableBuilder;
use Tk\Table\IsTable;
use Modules\Staff\Models\Staff;

class StaffController extends Controller
{
    use IsTable;

    public function index(Request $request)
    {
        // Clear — redirect to strip all table query params from the URL
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

    public function rows(): array|Builder
    {
        return Staff::query();
    }
}
```

In the Blade view, pass `$table` explicitly:

```blade
<x-core::table.filters :table="$table"/>
<x-core::table :table="$table"/>
```

**Note:** Controller-based tables do not yet support reactive filter dropdowns. Filter values must be read from the request manually and applied inside `rows()`. Dependent filter option reloading requires a JavaScript/Alpine approach.

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
            'value'       => callable,  // fn($row, $cell): string  — plain text / CSV value
            'view'        => callable,  // fn($row, $cell): string  — HTML value; falls back to value()
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
            'view'        => callable,  // fn($row, $cell): string — full override
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
            'options'      => array | callable,  // fn(string $parentKey): array
            'dependsOn'    => string,            // key of the parent filter
            'defaultValue' => string,
            'visible'      => bool,
        ],
    ],

    'search' => [
        'placeholder'  => string,
        'clearFilters' => array,   // filter keys to clear when search changes
    ],
]);
```

---

### Fluent cell/filter API (without Builder)

Cells and filters can also be built individually using the fluent API:

```php
use Tk\Table\Cell;
use Tk\Table\Filter;

// Append a cell to the end
$cell = $this->appendCell(new Cell('email'));
$cell->setSortable()
     ->setHeader('Email Address')
     ->addClass('text-lowercase')
     ->setView(fn($row, $cell) => view('core::components.table.cells.a', [
         'href' => 'mailto:' . $row->email,
         'text' => $row->email,
     ]));

// Insert a cell after another
$this->appendCell(new Cell('phone'), after: 'email');

// Prepend a cell before another
$this->prependCell(new Cell('id'), before: 'email');

// Remove a cell
$this->removeCell('phone');

// Modify a cell after Builder::build()
$this->getCell('email')->addClass('text-danger');

// Append a filter
$this->appendFilter(new Filter('status', type: Filter::TYPE_SELECT, options: [
    'active'   => 'Active',
    'inactive' => 'Inactive',
]));
```

**`Cell` fluent methods**

| Method | Description |
|---|---|
| `setHeader(string)` | Set the column heading (may contain HTML) |
| `setSortable(bool)` | Enable/disable sort click |
| `setSort(string)` | Set the DB column for ORDER BY |
| `setVisible(bool)` | Show/hide the column |
| `setValue(callable\|string)` | Set the plain text / CSV value callable |
| `setView(callable)` | Set the HTML view callable |
| `addClass(string)` | Add a CSS class to `<td>` |
| `addAttr(array)` | Merge attributes onto `<td>` |
| `addHeaderClass(string)` | Add a CSS class to `<th>` |
| `addHeaderAttr(array)` | Merge attributes onto `<th>` |
| `value(mixed $row)` | Get the formatted/escaped text value for a row |
| `view(mixed $row)` | Get the HTML value for a row (falls back to `value()`) |
| `getRowValue(mixed $row)` | Get the unformatted raw value from the row (bypasses any `value` callable) |

---

### `Formats` — built-in value formatters

`Tk\Table\Formats` provides static formatters for common cell value types. Pass them as a string callable to `value`:

```php
'phone'      => ['value' => '\Tk\Table\Formats::phone'],
'created_at' => ['value' => '\Tk\Table\Formats::date'],
'is_active'  => ['value' => '\Tk\Table\Formats::yesNo'],
```

| Formatter | Output |
|---|---|
| `Formats::date` | `d M Y` (e.g. `12 Apr 2026`) |
| `Formats::phone` | International phone format via `libphonenumber` |
| `Formats::yesNo` | `'Yes'` or `'No'` for truthy/falsy values |

---

### Useful table helpers

| Method | Description |
|---|---|
| `safeSort()` | Validated sort column (falls back to `defaultSort`) |
| `getDir()` | Returns `'asc'` or `'desc'` |
| `getLimit()` | Current per-page limit |
| `paginatedRows()` | Sorts and paginates the result of `rows()`; called by the Blade component |
| `buildCsv($rows, $fileName)` | Returns a CSV stream download response |
| `isSearchable()` | `true` if `IsSearchable` trait is in use |
| `isLivewire()` | `true` when using `IsLivewireTable` |
| `tableKey(string $key)` | Prefixes a URL param key with the `tableId` |
| `setDefaultLimit(int)` | Override the default per-page limit |
| `setDefaultSort(string, string)` | Set the default sort column and direction |

---

## 4. Common Patterns

### Conditional column visibility

```php
'visible' => auth()->user()->can('view-salary'),
```

### Custom sort key

When the column key and the DB sort column differ:

```php
'name_last_first' => [
    'sortable' => true,
    'sort'     => 'family_name',  // ORDER BY family_name
],
```

### Modifying a cell after `Builder::build()`

```php
Builder::build($this, [...]);

$this->getCell('email')->addClass('text-danger');
$this->removeCell('phone');
```

### Row click navigation

Return `data-url` from `rowAttrs` to make the whole row clickable:

```php
'rowAttrs' => fn($row) => ['data-url' => route('staff.show', $row->id)],
```

Clicking a link or button inside the row does not trigger navigation.

### Multiple tables on one page

Set a unique `tableId` on each table to avoid URL key conflicts:

```php
Builder::build($this->staffTable, ['tableId' => 'staff', ...]);
Builder::build($this->leaveTable, ['tableId' => 'leave', ...]);
```

URL params become `staff_s`, `staff_d`, `staff_f`, `leave_s`, `leave_d`, `leave_f`, etc.

### Setting a custom per-page limit

```php
public function boot(): void
{
    $this->setDefaultLimit(50);
    Builder::build($this, [...]);
}
```
