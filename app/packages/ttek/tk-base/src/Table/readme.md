
# Table

`Tk\Table\Table` provides a small, composable way to build renderable tables with:

- named columns via `Tk\Table\Cell`
- record sources via `Tk\Table\Records\*`
- table state stored in request/query params and session
- sorting
- pagination
- row-level attributes
- custom cell rendering

This document explains the main objects and how they work together.

---

## Core objects

### `\Tk\Table\Table`

The main table definition object.

A table is responsible for:

- defining an ID
- registering cells
- attaching a records source
- reading table state from request/session
- exposing URLs for sorting and pagination
- exposing row and cell metadata to the view layer

In practice, you will usually create a custom table class that extends `Table` and overrides `build()`.

---

### `\Tk\Table\Cell`

Represents a single column in the table.

A cell controls:

- the column name
- header label
- sort key
- whether the column is sortable
- raw value extraction
- rendered HTML output
- optional custom component for rendering
- HTML attributes for the cell and header

---

### `\Tk\Table\Records\RecordsInterface`

Base abstraction for table data sources.

A records object is responsible for:

- attaching to a table
- reading table params
- filtering rows
- sorting rows
- paginating rows
- returning the final row list

Concrete implementations include:

- `ArrayRecords`
- `CsvRecords`
- `QueryRecords`

---

## Basic usage

## 1. Create a table class

Typically, you subclass `Table` and define columns and records in `build()`.
```php
<?php

namespace App\Tables;

use App\Models\User;
use Tk\Table\Cell;
use Tk\Table\Table;
use Tk\Table\Records\QueryRecords;

class UserTable extends Table
{
    protected function build(): void
    {
        $this->appendCell(
            (new Cell('id'))
                ->setHeader('ID')
                ->setSortable()
        );

        $this->appendCell(
            (new Cell('name'))
                ->setHeader('Name')
                ->setSortable()
        );

        $this->appendCell(
            (new Cell('email'))
                ->setHeader('Email')
                ->setSortable()
        );

        $this->appendCell(
            (new Cell('created_at'))
                ->setHeader('Created')
                ->setSortable()
                ->setHtml(fn ($row) => e((string) $row->created_at))
        );

        $this->setRecords(
            new QueryRecords(User::query())
        );
    }
}
```

Optionally, you can create the table object directly within your controller.
```
class UserController extends Controller
{
    public function index(Request $request)
    {
        $table = new Table();

        // Init the table and cells
        $table->setLimit(10);

        $table->addRowAttrs(function (Idea $row, Table $table) {
            return ['data-test-id' => $row->id];
        });

        $table->appendCell(
            (new Cell('id'))
                ->setHeader('ID')
                ->setSortable()
        );

        $table->appendCell(
            (new Cell('name'))
                ->setHeader('Name')
                ->setSortable()
        );

        $table->appendCell(
            (new Cell('email'))
                ->setHeader('Email')
                ->setSortable()
        );

        $table->appendCell(
            (new Cell('created_at'))
                ->setHeader('Created')
                ->setSortable()
                ->setHtml(fn ($row) => e((string) $row->created_at))
        );

        $table->setRecords(
            new QueryRecords(User::query())
        );

        return view('pages.user.index', [
            'table' => $table,
        ]);
    }
    
}
```

---

## 2. Render the table in a controller
```php
<?php

namespace App\Http\Controllers;

use App\Tables\UserTable;

class UserController extends Controller
{
    public function index()
    {
        $table = new UserTable('users');

        return view('pages.users.index', [
            'table' => $table,
        ]);
    }
}
```
---

## 3. Pass the table to a Blade view

How you render it depends on the Blade components available in your app, but the controller should pass the built table object into the view.

Example:
```php
return view('pages.users.index', [
    'table' => new UserTable('users'),
]);
```
---

## Defining cells

A `Cell` is keyed by its name.
```php
$cell = new Cell('email');
```
By default:

- the cell name is `email`
- the default sort key is also `email`
- the header is automatically generated from the name

For example:

- `email` becomes `Email`
- `created_at` becomes `Created At`
- `userId` becomes `User Id`

### Common cell methods

#### `setHeader(string $header)`

Overrides the display label for the column header.
```php
(new Cell('created_at'))->setHeader('Created')
```
#### `setSortable(bool $sortable = true)`

Marks the column as sortable.
```php
(new Cell('name'))->setSortable()
```
#### `setOrderBy(string $orderBy)`

Overrides the database/property field used for sorting (cell name by default).

Useful when the display column name differs from the actual sortable field.
```php
(new Cell('author'))
    ->setHeader('Author')
    ->setSortable()
    ->setOrderBy('users.name')
```

#### `setValue(null|string|callable $value)`

Controls the raw value returned for the cell.

- `null`: use `$row[$name]` or `$row->{$name}`
- `string`: use a static value
- `callable`: compute a value dynamically
```php
(new Cell('full_name'))
    ->setValue(fn ($row) => $row->first_name . ' ' . $row->last_name)
```
#### `setHtml(null|string|callable $html)`

Controls rendered display output.

If `html` is not set, the cell falls back to the raw value.
```php
(new Cell('status'))
    ->setHtml(fn ($row) => $row->active ? 'Active' : 'Inactive')
```
Use this for formatting, badges, links, icons, or other display markup.

#### `setComponent(string $component)`

Sets a Blade component name used for rendering the cell.
```php
(new Cell('actions'))->setComponent('table.action')
```

#### `addClass(string $class)`

Adds HTML classes to the cell attributes.

This comes from the shared `HasAttributes` trait.
```php
(new Cell('email'))->addClass('text-nowrap')
```
#### `addHeaderClass(string $class)`

Adds HTML classes to the header cell.
```php
(new Cell('email'))->addHeaderClass('w-25')
```
#### `addHeaderAttr(array $attrs)`

Adds arbitrary header attributes.
```php
(new Cell('email'))->addHeaderAttr(['data-test' => 'email-header'])
```
---

## Adding cells to a table

### `appendCell(string|Cell $cell, ?string $after = null)`

Adds a cell to the end of the table, or after a named column.
```php
$this->appendCell(new Cell('name'));
$this->appendCell(new Cell('email'), 'name');
```
If you pass a string, a `Cell` is created automatically:
```php
$this->appendCell('email');
```
### `prependCell(string|Cell $cell, ?string $before = null)`

Adds a cell to the beginning of the table, or before a named column.
```php
$this->prependCell(new Cell('id'));
$this->prependCell(new Cell('status'), 'email');
```
### `getCell(string $name)`

Returns a cell by name.
```php
$emailCell = $this->getCell('email');
```
### `removeCell(string $name)`

Removes a cell from the table.
```php
$this->removeCell('internal_notes');
```
### `getCells(?object|array $row = null)`

Returns the cell collection.

If a row is provided, each cell is updated with the current row before rendering.
```php
$cells = $table->getCells($row);
```
---

## Attaching records

A table must have a records source before it can return rows.

### `setRecords(RecordsInterface $records)`
```php
$this->setRecords(new QueryRecords(User::query()));
```
When records are attached, the records object also attaches itself back to the table and refreshes table state from the request/session.

### `getRecords()`

Returns the current records source.

If records have not been set yet, this throws an exception.

---

## Table state

The table stores and reads state for:

- current page
- page size / limit
- current sort order

### Reserved query keys

The table uses these reserved parameter names internally:

- `_tid` — table ID
- `tr_` — reset
- `tl_` — limit
- `tp_` — page
- `to_` — order

Each actual table parameter is prefixed with the table ID.

For a table with ID `users`, page might be stored as:
```
text
users_tp_
```
and order as:
```
text
users_to_
```
### Why this matters

Multiple tables can coexist on the same page without clobbering each other’s query params.

That is the polite, civilized thing for tables to do.

---

## Table IDs

Each table has an ID.
```php
$table = new UserTable('users');
```
If you do not provide one, a short hash based on the current request path is generated.

If duplicate IDs are created on the same request, a numeric suffix is added automatically.

### `getId()`

Returns the resolved table ID.
```php
$table->getId();
```
---

## Pagination and counts

### `count()`

Returns the number of rows on the current page.
```php
$table->count();
```
### `countAll()`

Returns the total number of available rows before pagination.
```php
$table->countAll();
```
### `hasRecords()`

Returns `true` when records are set and the current page has rows.
```php
$table->hasRecords();
```
### `getPaginator()`

Returns a Laravel paginator when supported by the records' implementation.

- `ArrayRecords` returns a `LengthAwarePaginator`
- `QueryRecords` returns the paginator created from the query
- `RecordsInterface` base implementation returns `null`
```php
$paginator = $table->getPaginator();
```
---

## Sorting

Sorting is driven by the table’s `orderBy` state. Order by states starting with `-` are descending.

### Table-level sorting
```php
$table->getOrderBy();
$table->setOrderBy('name');
$table->setOrderBy('-created_at');
```
Validation is applied in `Table::setOrderBy()` and only values matching:
```
text
[a-z0-9_-]+
```
are accepted.

Invalid values are cleared and logged.

### Cell-level sorting

For a sortable cell:
```php
(new Cell('name'))->setSortable()
```
you can use:

- `getOrderByUrl()` to generate the next sort URL
- `getOrderByDir()` to determine current direction:
  - `asc`
  - `desc`
  - `''`

Sort cycling works like this:

1. not sorted → ascending
2. ascending → descending
3. descending → cleared

---

## URL generation

### `url(array|string $url, ?array $params = null)`

Builds a table-aware URL by prefixing params with the table ID and appending `_tid`.

Examples:
```php
$url = $table->url(['tp_' => 1, 'tl_' => 25]);
```
or:
```php
$url = $table->url('/users', ['tp_' => 2]);
```
In practice, you would usually pass the table constants:
```php
$url = $table->url([
    Table::QUERY_PAGE => 2,
    Table::QUERY_LIMIT => 25,
]);
```
This ensures generated links are scoped to the correct table instance.

### `key(string $key)`

Prefixes a key with the table ID.
```php
$table->key(Table::QUERY_PAGE);
```
### `Table::makeKey(string $tableId, string $key)`

Static version of the same behavior.
```php
Table::makeKey('users', Table::QUERY_PAGE);
```
---

## Row attributes

You can set HTML attributes for table rows.

### Static row attributes
```php
$table->addRowAttrs([
    'class' => 'align-middle',
]);
```
### Dynamic row attributes

Pass a callable:
```php
$table->addRowAttrs(function ($row, $table) {
    return [
        'class' => $row->active ? 'table-success' : 'table-secondary',
    ];
});
```
### Reading row attributes
```php
$attrs = $table->getRowAttrs($row);
```
---

## Filtering records

Filtering is handled by the records object, not the table itself.

The filter callback receives:

1. the current table params
2. the underlying row/query source

---

### Filtering `ArrayRecords`
```php
use Tk\Table\Records\ArrayRecords;

$this->setRecords(new ArrayRecords($rows))
    ->filter(function (array $filters, array $rows) {
        if (!empty($filters['status'])) {
            $rows = array_filter($rows, function ($row) use ($filters) {
                return ($row['status'] ?? null) === $filters['status'];
            });
        }

        return array_values($rows);
    });
```
---

### Filtering `QueryRecords`
```php
use App\Models\User;
use Tk\Table\Records\QueryRecords;

$this->setRecords(new QueryRecords(User::query()))
    ->filter(function (array $filters, $query) {
        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        if (!empty($filters['active'])) {
            $query->where('active', (bool) $filters['active']);
        }

        return $query;
    });
```
The callback should return the modified query.

---

## Working with `ArrayRecords`

Use `ArrayRecords` when your data is already available as an array.
```php
use Tk\Table\Records\ArrayRecords;

$this->setRecords(
    new ArrayRecords([
        ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.test'],
        ['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.test'],
    ])
);
```
Rows may be:

- associative arrays
- objects

Cell values are read by matching the cell name against the row key/property.

### Notes

- sorting happens in PHP
- pagination happens in PHP
- total count is based on the filtered row array

---

## Working with `CsvRecords`

Use `CsvRecords` to build a table from a CSV file.
```php
use Tk\Table\Records\CsvRecords;

$this->setRecords(
    new CsvRecords(storage_path('app/import/users.csv'))
);
```
If the CSV has no header row:
```php
new CsvRecords(storage_path('app/import/users.csv'), false)
```
### Behavior

- if headers are present, they become array keys
- if a row has more columns than the header, generated keys like `row_1`, `row_2`, etc. are added
- once loaded, the CSV behaves like `ArrayRecords`

### Best practice

Use this for modestly sized CSV files. For large files, create your own `QueryRecords` object to serially load the data.

---

## Working with `QueryRecords`

Use `QueryRecords` for Eloquent-backed tables.
```php
use App\Models\User;
use Tk\Table\Records\QueryRecords;

$this->setRecords(
    new QueryRecords(User::query())
);
```
### Behavior

When attached to a table, `QueryRecords`:

1. applies filter callback if present
2. applies sort order from the table
3. counts total rows
4. paginates using Laravel pagination if a limit is set

### Notes

- sorting happens in SQL using `orderBy()`
- pagination happens in SQL using `paginate()`
- query-string values are preserved with pagination links

### Important caution

`QueryRecords` applies sort fields directly from the table order string. In practice, only allow sortable fields you trust and define through your cells/table setup.

---

## Accessing records and rows

Because `RecordsInterface` implements `IteratorAggregate` and `Countable`, you can iterate over records easily.
```php
foreach ($table->getRecords() as $row) {
    // ...
}
```
Or, if your view receives the table, iterate there.

The final rows are produced by `toArray()` internally.

---

## Value vs HTML

A common pattern is:

- use `setValue()` for raw/computed data
- use `setHtml()` for presentation

Example:
```php
(new Cell('status'))
    ->setValue(fn ($row) => $row->active ? 'active' : 'inactive')
    ->setHtml(fn ($row, $cell) => $cell->getValue($row) === 'active'
        ? '<span class="badge text-bg-success">Active</span>'
        : '<span class="badge text-bg-secondary">Inactive</span>'
    )
```
Use this split when:

- you want a raw canonical value
- but display a formatted label or HTML badge

---

## Custom cell components

A cell can specify a custom component renderer.
```php
(new Cell('actions'))->setComponent('table.action')
```
You can inspect component existence using:
```php
$cell->componentExists($cell->getComponent());
```
Cell header components follow the convention:
```
text
{component}-head
```
So if the component is:
```
text
table.action
```
the header component name becomes:
```
text
table.action-head
```
---

## Example: complete table using `QueryRecords`
```php
<?php

namespace App\Tables;

use App\Models\User;
use Tk\Table\Cell;
use Tk\Table\Table;
use Tk\Table\Records\QueryRecords;

class UserTable extends Table
{
    protected function build(): void
    {
        $this->setLimit(25);

        $this->appendCell(
            (new Cell('id'))
                ->setHeader('ID')
                ->setSortable()
        );

        $this->appendCell(
            (new Cell('name'))
                ->setHeader('Name')
                ->setSortable()
        );

        $this->appendCell(
            (new Cell('email'))
                ->setHeader('Email')
                ->setSortable()
                ->addClass('text-nowrap')
        );

        $this->appendCell(
            (new Cell('active'))
                ->setHeader('Status')
                ->setHtml(fn ($row) => $row->active ? 'Active' : 'Inactive')
        );

        $this->addRowAttrs(function ($row) {
            return [
                'class' => $row->active ? '' : 'opacity-50',
            ];
        });

        $this->setRecords(new QueryRecords(User::query()))
            ->filter(function (array $filters, $query) {
                if (!empty($filters['name'])) {
                    $query->where('name', 'like', '%' . $filters['name'] . '%');
                }

                return $query;
            });
    }
}
```
---

## Example: complete table using `ArrayRecords`
```php
<?php

namespace App\Tables;

use Tk\Table\Cell;
use Tk\Table\Table;
use Tk\Table\Records\ArrayRecords;

class ReportTable extends Table
{
    protected function build(): void
    {
        $rows = [
            ['name' => 'Alpha', 'score' => 10],
            ['name' => 'Bravo', 'score' => 20],
            ['name' => 'Charlie', 'score' => 15],
        ];

        $this->appendCell(
            (new Cell('name'))
                ->setHeader('Name')
                ->setSortable()
        );

        $this->appendCell(
            (new Cell('score'))
                ->setHeader('Score')
                ->setSortable()
        );

        $this->setRecords(
            new ArrayRecords($rows)
        );
    }
}
```
---

## Recommended patterns

### 1. Create one table class per page/table concern

Good:

- `UserTable`
- `IdeaTable`
- `AuditLogTable`

This keeps column and filter logic organized.

### 2. Prefer `QueryRecords` for database-backed pages

Use `ArrayRecords` only when rows are already in memory or when the data set is small.

### 3. Keep cell rendering small and focused

If a cell starts doing too much work, move the formatting into:

- a dedicated method
- a presenter
- a Blade component

### 4. Be deliberate with sorting fields

Only mark columns sortable when the sort field is valid for the underlying records source.

### 5. Always give tables stable IDs when multiple tables can appear on one page
```php
new UserTable('users')
new AuditTable('audit')
```
This avoids query parameter collisions.

---

## Common pitfalls

### Records not set

Calling methods that depend on records before `setRecords()` will fail.
```php
$table->getRecords();
```
Make sure records are attached during `build()`.

### Duplicate cell names

Cells are keyed by name. Adding two cells with the same name throws an exception.

### Mismatched sort field

If a cell sorts by a field that does not exist in the array row or query, sorting may fail or behave unexpectedly.

### Large array or CSV sources

`ArrayRecords` and `CsvRecords` do sorting/pagination in PHP, so they are not ideal for very large data sets.

---

## Quick reference

### `Table`

- `build(): void`
- `setRecords(RecordsInterface $records): static`
- `getRecords(): RecordsInterface`
- `appendCell(string|Cell $cell, ?string $after = null): Cell`
- `prependCell(string|Cell $cell, ?string $before = null): Cell`
- `getCell(string $name): ?Cell`
- `getCells(?object|array $row = null)`
- `removeCell(string $name): static`
- `setLimit(int $limit): static`
- `getLimit(): int`
- `setPage(int $page): static`
- `getPage(): int`
- `setOrderBy(string $orderBy): static`
- `getOrderBy(): string`
- `count(): int`
- `countAll(): int`
- `hasRecords(): bool`
- `getPaginator(): ?AbstractPaginator`
- `addRowAttrs(callable|array|null $rowAttrs): static`
- `getRowAttrs(object|array|null $row = null): array`
- `getParams(bool $removeId = true): array`
- `getParam(string $key, mixed $default = null): mixed`
- `url(array|string $url, ?array $params = null): string`
- `key(string $key): string`
- `makeKey(string $tableId, string $key): string`

### `Cell`

- `setHeader(string $header): static`
- `getHeader(): string`
- `setSortable(bool $sortable = true): static`
- `isSortable(): bool`
- `setOrderBy(string $orderBy): static`
- `getOrderBy(): string`
- `setValue(null|string|callable $value): Cell`
- `getValue(object|array $row): mixed`
- `setHtml(null|string|callable $html): Cell`
- `getHtml(object|array $row): string`
- `setComponent(string $component): static`
- `getComponent(): string`
- `getComponentHead(): string`
- `componentExists(string $component): bool`
- `addHeaderClass(string $class): static`
- `addHeaderAttr(array $attrs): static`
- `getOrderByUrl(): string`
- `getOrderByDir(): string`

### `RecordsInterface`

- `toArray(): array`
- `filter(callable $filter): static`
- `setTable(Table $table): static`
- `getTable(): Table`
- `count(): int`
- `countAll(): int`
- `getPaginator(): ?AbstractPaginator`

---

## Summary

Use the table system like this:

1. extend `\Tk\Table\Table`
2. define columns with `\Tk\Table\Cell`
3. attach a records source:
   - `ArrayRecords`
   - `CsvRecords`
   - `QueryRecords`
4. optionally add filtering, sorting, and row attributes
5. pass the table to your Blade view for rendering

