

# TODO: get the AI to write a a tutorial.

Write me a tutorial readme.md showing developers to implement the `\Tk\Table` system.
Start with how to use the `\Tk\Table\IsLivewireTable` within a Livewire single file page component.
You can use reference the implementation in the resources folder `/resources/views/pages/ideas/index.blade.php`.
Then move on to the `\Tk\Table\IsTable` and show how to create a table within a Controller. 
You can use `\App\Http\Controllers\Examples\ArrayTableController` for a reference implementation.
It needs to include how to use the table components (search, filters, etc.).
I want the document to walk a developer step by step through both the Livewire and Controller implementations.
It needs to include how to use the table components (search, filters, etc.).
I want the document to walk a developer step by step through both the Livewire and Controller based implementations.


------------------------------------------------------------

# `\Tk\Table` Tutorial

This guide shows how to implement the `\Tk\Table` system in two ways:

1. **Livewire single-file page component** using `\Tk\Table\IsLivewireTable`
2. **Controller-based table page** using `\Tk\Table\IsTable`

It walks through:

- defining columns
- enabling sorting
- adding search and filters
- rendering the table UI
- handling pagination
- resetting table state

---

## Table of contents

- [1. Livewire table with `\Tk\Table\IsLivewireTable`](#1-livewire-table-with-tktableislivewiretable)
- [2. Controller table with `\Tk\Table\IsTable`](#2-controller-table-with-tktableistable)
- [3. Search, filters, sorting, and pagination](#3-search-filters-sorting-and-pagination)
- [4. Common patterns](#4-common-patterns)
- [5. Recommended implementation order](#5-recommended-implementation-order)

---

## 1. Livewire table with `\Tk\Table\IsLivewireTable`

Use `\Tk\Table\IsLivewireTable` when the table lives inside a **Livewire single-file page component** and you want:

- reactive filters
- URL-synced table state
- built-in pagination behavior
- easy sorting support

A useful reference is the Livewire page implementation in:

- `/resources/views/pages/ideas/index.blade.php`

### Step 1: Create a Livewire single-file page component

A Livewire single-file page component puts the PHP class and Blade markup in the same file.

A typical structure looks like this:

```php
<?php

use Livewire\Component;
use Livewire\WithPagination;
use Tk\Table\Cell;
use Tk\Table\IsLivewireTable;

new class extends Component
{
    use WithPagination, IsLivewireTable;

    public $search = '';
    public $status = '';

    public function boot()
    {
        // Define columns here.
    }

    public function rows()
    {
        // Return paginated data here.
    }
};
?>

<div>
    <!-- Page content -->
</div>
```


---

### Step 2: Add the table trait

The trait gives you table behavior such as:

- `tableId`
- `limit`
- `sort`
- `dir`
- `clearFilters()`
- `setLimit()`
- `toggleDir()`

Because `IsLivewireTable` builds on top of `IsTable`, you get the same table configuration patterns in both Livewire and controller-driven pages.

---

### Step 3: Define table cells in `boot()`

Register your columns in the `boot()` method using `appendCell()`.

Example:

```php
<?php

public function boot()
{
    $this->appendCell(new Cell('title'))
        ->setSortable()
        ->addClass('fw-bold');

    $this->appendCell(new Cell('status'))
        ->setSortable();

    $this->appendCell(new Cell('created_at'))
        ->setHeader('Created')
        ->setSortable();

    $this->appendCell(new Cell('updated_at'))
        ->setHeader('Updated')
        ->setSortable();
}
```


### What this does

Each `Cell` describes a column in the table. You can configure it to:

- be sortable
- have a custom header
- have extra CSS classes
- render custom text or HTML

---

### Step 4: Add search and filter properties

In Livewire, table filters are usually public properties.

Example:

```php
<?php

use Livewire\Attributes\Url;

#[Url(except: '')]
public $search = '';

#[Url(except: '')]
public $status = '';
```


This keeps filter state in the URL, which is helpful for:

- page refreshes
- bookmarking
- sharing filtered views

---

### Step 5: Build the `rows()` method

Your `rows()` method should return a `LengthAwarePaginator`.

A typical flow is:

1. start with a query
2. apply search
3. apply filters
4. sort
5. paginate

Example:

```php
<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

public function rows(): LengthAwarePaginator
{
    return Idea::query()
        ->when($this->search, function (Builder $builder) {
            $str = preg_replace("/[^a-zA-Z0-9' -]/", " ", $this->search);

            return $builder
                ->where('title', 'like', "%{$str}%")
                ->orWhere('description', 'like', "%{$str}%")
                ->tap($this->resetPage() ?? fn () => null);
        })
        ->when($this->status, fn (Builder $query) => $query->where('status', $this->status))
        ->orderBy($this->safeSort(), $this->dir)
        ->paginate($this->limit);
}
```


### Notes

- `safeSort()` protects the sort column selection.
- `dir` controls ascending or descending order.
- `paginate($this->limit)` uses the current page size.

---

### Step 6: Render filters and actions

The table UI includes a filter wrapper component with slots for filters and actions.

Example:

```blade
<x-tkl-ui::table.livewire.filters :table="$this">
    <x-slot name="filters">
        <x-tkl-ui::table.livewire.filters.select
            wire:model.live="status"
            :name="$this->tableKey('status')"
            :options="[ '' => '- All Statuses -'] + IdeaStatus::getLabels()"
            value="{{ $this->status }}"
        />
    </x-slot>

    <x-slot name="actions">
        <div class="p-2 ps-0">
            <a href="{{ route('examples.ideas.create') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-plus-circle"></i> Create
            </a>
        </div>
    </x-slot>
</x-tkl-ui::table.livewire.filters>
```


### Step 7: Render the table itself

Use the Livewire table component:

```blade
<x-tkl-ui::table.livewire :table="$this" />
```


This component handles the table rendering for you, including:

- headers
- sorting UI
- pagination
- rows and cells

---

### Step 8: Use `clearFilters()` when needed

If you want to reset the table back to its defaults, call:

```php
$this->clearFilters();
```


This resets the component state, restores default paging and sorting, and resets pagination.

---

### Livewire example summary

A typical Livewire table page includes:

- `use WithPagination, IsLivewireTable;`
- column setup in `boot()`
- search/filter properties
- a `rows()` method returning `LengthAwarePaginator`
- `<x-tkl-ui::table.livewire.filters>`
- `<x-tkl-ui::table.livewire>`

---

### Livewire example

```php
<?php

use Livewire\Component;
use Livewire\WithPagination;
use Tk\Table\Cell;
use Tk\Table\IsLivewireTable;

new class extends Component
{
    use WithPagination, IsLivewireTable;

    public $search = '';

    public function boot()
    {
        $this->appendCell(new Cell('name'))->setSortable();
        $this->appendCell(new Cell('email'))->setSortable();
    }

    public function rows()
    {
        return User::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->orderBy($this->safeSort(), $this->dir)
            ->paginate($this->limit);
    }
};
?>

<div>
    <x-tkl-ui::table.livewire.filters :table="$this" />
    <x-tkl-ui::table.livewire :table="$this" />
</div>
```

---

## 2. Controller table with `\Tk\Table\IsTable`

Use `\Tk\Table\IsTable` when you want a **traditional controller + Blade view** flow.

This is a good choice when:

- the table is not Livewire-based
- your page is request-driven
- you want to render from a controller action

A useful reference is:

- `\App\Http\Controllers\Examples\ArrayTableController`

---

### Step 1: Add the trait to your controller

Example:

```php
<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use Tk\Table\IsTable;

class ArrayTableController extends Controller
{
    use IsTable;
}
```


---

### Step 2: Define columns with `appendCell()`

Set up your table columns before hydrating request state.

Example:

```php
$this->appendCell(new Cell(
    name: 'name',
    sortable: true,
))->addClass('fw-bold');

$this->appendCell(new Cell(
    name: 'email',
    sortable: true,
));

$this->appendCell(new Cell(
    name: 'roles',
    sortable: false,
));

$this->appendCell(new Cell('created_at'), 'roles')
    ->setHeader('Created')
    ->setSortable();
```


### What this means

- `name`, `email`, `roles`, etc. are your column keys
- `sortable: true` enables sorting
- `setHeader()` changes what appears in the table header
- `addClass()` adds styling to the column

---

### Step 3: Hydrate table state from the request

Inside your controller action, call:

```php
$this->hydrateTableFromRequest();
```


This reads table state from the request query string so the table can keep track of:

- search
- sort
- direction
- limit
- other table-specific parameters

---

### Step 4: Handle reset behavior

If you want a reset action to clear all table query parameters, check for the reset key and redirect to the current URL:

```php
if (request()->has($this->tableKey('reset'))) {
    return redirect(url()->current());
}
```


This gives the user a quick way to return to the default table state.

---

### Step 5: Return the view and pass the table instance

Example:

```php
return view('pages.examples.tables.table-array', [
    'table' => $this,
]);
```


Your Blade view can then render the table using the passed table object.

---

## 3. Search, filters, sorting, and pagination

The table system is designed to make these pieces work together consistently.

---

### Search

#### Livewire
Use a public property, such as:

```php
public $search = '';
```


Then filter inside `rows()`.

#### Controller
Read the value from the request:

```php
$search = request()->input($this->tableKey('search'), '');
```


Then filter the rows before sorting and paginating.

---

### Filters

Filters are extra inputs that narrow down the result set, such as:

- status
- role
- category
- date range

#### Livewire filters

Example:

```blade
<x-tkl-ui::table.livewire.filters.select
    wire:model.live="roles"
    :name="$this->tableKey('roles')"
    :options="[ '' => '- All Roles -', 'admin' => 'Admin', 'staff' => 'Staff', 'member' => 'Member']"
    value="{{ $this->roles }}"
/>
```


#### Controller filters

Read filter values from the request and apply them inside `rows()`:

```php
$roles = request()->input($this->tableKey('roles'), '');

if ($roles) {
    $rows = array_filter($rows, function ($row) use ($roles) {
        return str_contains(strtolower($row->roles), strtolower($roles));
    });
}
```


---

### Sorting

Use the table sorting helpers instead of trusting raw user input.

#### Livewire

```php
->orderBy($this->safeSort(), $this->dir)
```


#### Controller

```php
$sortCol = ($this->dir == 'desc' ? '-' : '') . $this->safeSort();
$rows = $this->sortRows($rows, $sortCol);
```


---

### Pagination

#### Livewire

Use Eloquent pagination:

```php
->paginate($this->limit)
```


#### Controller

Use the table helper for arrays:

```php
$this->rows = $this->paginateArray($rows);
return $this->rows;
```


---

## 4. Common patterns

### Livewire pattern

Use Livewire when you want:

- reactive search and filters
- state in the URL
- a single-file page component
- minimal controller code

Main pieces:

- `IsLivewireTable`
- `boot()`
- `rows()`
- public filter/search properties
- table filter component
- table render component

---

### Controller pattern

Use a controller when you want:

- a request-driven page
- a classic Laravel controller flow
- non-Livewire rendering
- array, collection, or custom data sources

Main pieces:

- `IsTable`
- `appendCell()`
- `hydrateTableFromRequest()`
- `rows()`
- a Blade view that receives `$table`

---

## 5. Recommended implementation order

When creating a new table, follow this order:

1. **Choose the implementation style**
    - Livewire for reactive UI
    - Controller for classic request-based pages

2. **Define the columns**
    - add cells
    - set headers
    - mark sortable fields
    - add classes or custom renderers if needed

3. **Add search and filters**
    - Livewire: public properties + bound inputs
    - Controller: request values + filtering logic

4. **Apply sorting**
    - use `safeSort()`
    - use `dir` for direction

5. **Paginate**
    - use `paginate()` for database queries
    - use `paginateArray()` for arrays

6. **Render the table**
    - Livewire table component for Livewire pages
    - normal view for controller pages

---

## 6. Practical tips

- Define columns before rendering the table.
- Keep filter names consistent with `tableKey(...)`.
- Use `safeSort()` instead of raw input for sorting.
- Reset pagination when search or filter values change.
- Use `#[Url]` in Livewire to preserve table state in the query string.
- Call `hydrateTableFromRequest()` early in controller actions.
- Use `clearFilters()` to restore the default Livewire table state.

---

### Controller example

```php
<?php

class ArrayTableController extends Controller
{
    use IsTable;

    public function index(Request $request)
    {
        $this->appendCell(new Cell('name'))->setSortable();
        $this->appendCell(new Cell('email'))->setSortable();

        $this->hydrateTableFromRequest();

        return view('pages.examples.tables.table-array', [
            'table' => $this,
        ]);
    }
}
```


---

## 8. Final note

` \Tk\Table` gives you a consistent table experience across both Livewire and controller-based pages.

If you follow the patterns above, you can quickly build tables that support:

- search
- filters
- sorting
- pagination
- reset behavior
- reusable UI components

If you want, I can also turn this into a **more polished project README** with:
- a short intro
- examples tailored to your app
- a troubleshooting section
- and copy-paste ready Livewire and controller templates.
