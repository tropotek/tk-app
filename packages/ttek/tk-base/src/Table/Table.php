<?php

namespace Tk\Table;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\View\ComponentAttributeBag;
use Tk\Table\Records\RecordsInterface;
use Tk\Table\Traits\HasAttributes;

class Table
{
    use HasAttributes;

    // table sessions id keys: `SESSION_PRE.$table->getId()`
    const string SESSION_PRE    = '_tbl_';

    // Table url query names
    const string QUERY_ID       = '_tid';   // reserved param for table queries
    const string QUERY_RESET    = 'tr_';
    const string QUERY_LIMIT    = 'tl_';
    const string QUERY_PAGE     = 'tp_';
    const string QUERY_ORDER    = 'to_';

    protected string     $id        = '';
    protected int        $limit     = 50;
    protected int        $page      = 1;
    protected string     $orderBy   = '';
    protected Collection $cells;
    protected RecordsInterface $records;
    protected mixed      $rowAttrs = null;   // ?callable


    public function __construct(string $id = '')
    {
        $this->_attributes = new ComponentAttributeBag();
        $this->cells = new Collection();
        $this->setId($id);

        $this->build();
    }

    /**
     * Called by Records to update the table from the request or session.
     * Called when a Records object is added to the table
     */
    public function refreshParams(): void
    {
        // set table state from storage
        $this->setPage((int)$this->getParam(self::QUERY_PAGE, $this->page));
        $this->setLimit((int)$this->getParam(self::QUERY_LIMIT, $this->limit));
        $this->setOrderBy((string)$this->getParam(self::QUERY_ORDER, $this->orderBy));
    }

    /**
     * return all available table state properties
     * Optionally remove the table id from the key
     */
    public function getParams(bool $removeId = true): array
    {
        $list = [];

        if (Session::exists(self::SESSION_PRE.$this->getId())) {
            foreach (Session::get(self::SESSION_PRE.$this->getId()) as $k => $v) {
                if ($removeId) {
                    $k = substr($k, strlen($this->getId() . '_'));
                }
                $list[$k] = $v;
            };
        }

        $query = request()->query();
        // TODO Should we be validating the query params here???
        //      not sure if its needed between the request and sql validations???
        //      NOTE: Test for potential SQL injection issues here!!!!
        foreach ($query as $k => $v) {
            if (str_starts_with($k, $this->getId().'_')) {
                if ($removeId) {
                    $k = substr($k, strlen($this->getId() . '_'));
                }
                $list[$k] = $v;
            }
        }
        return $list;
    }

    /**
     * Get the table's current state property from the session or the request
     */
    public function getParam(string $key, mixed $default = null): mixed
    {
        $key = $this->key($key);
        if (request()->has($this->key($key))) {
            return request()->input($this->key($key));
        }

        if (
            Session::exists(self::SESSION_PRE.$this->getId()) &&
            isset(Session::get(self::SESSION_PRE.$this->getId())[$key])
        ) {
            return Session::get(self::SESSION_PRE.$this->getId())[$key];
        }

        return $default;
    }

    // TODO This should not be needed as state params can be cleared by resetting the table
//    public function setParam(string|array $key, null|string|array $value = null): static
//    {
//        $keys = $key;
//        if (is_string($key)) {
//            $keys = [$key => $value];
//        }
//
//        if (Session::exists(self::SESSION_PRE.$this->getId())) {
//            $state = Session::get(self::SESSION_PRE.$this->getId());
//            foreach ($keys as $k => $v) {
//                $k = $this->key($k);
//                if (isset($state[$k])) {
//                    if (is_null($v)) {
//                        unset($state[$k]);
//                    } else {
//                        $state[$k] = $v;
//                    }
//                }
//            }
//
//            Session::put(self::SESSION_PRE.$this->getId(), $state);
//        }
//        return $this;
//    }

    /**
     * returns true if the current request is from this table id
     */
//    public function hasRequest(): bool
//    {
//        return request()->input(Table::QUERY_ID) == $this->getId();
//    }

    /**
     * Override this method in your parent table objects
     * Add cells and build record results
     */
    protected function build(): void { }

    public function getRecords(): RecordsInterface
    {
        if (!isset($this->records)) {
            throw new \Exception("Cannot access records before they are set.");
        }
        return $this->records;
    }

    public function setRecords(RecordsInterface $records): static
    {
        $this->records = $records;
        $records->setTable($this);
        return $this;
    }

    /**
     * return the number of records on the page
     */
    public function count(): int
    {
        return $this->getRecords()->count();
    }

    /**
     * return the total number of records available
     */
    public function countAll(): int
    {
        return $this->getRecords()->countAll();
    }

    public function hasRecords(): bool
    {
        return isset($this->records) && $this->count() > 0;
    }

    public function getPaginator(): ?AbstractPaginator
    {
        if (!$this->hasRecords()) return null;
        return $this->getRecords()->getPaginator();
    }

    /**
     * ensure the id is unique if duplicates exist
     */
    protected function setId(string $id): static
    {
        static $instances = [];
        if ($this->getId()) return $this;

        // generate unique id from url path
        if (empty($id)) $id = \Tk\Utils\Str::shortHash(request()->path(), 5);
        // md5 may not hav enough entropy, scope for clash is greater
        //$id = substr(md5(request()->path()), 0, 5);

        $instances[$id] = isset($instances[$id]) ? ($instances[$id]+1) : 0;
        $this->id = ($instances[$id] > 0) ? $id.$instances[$id] : $id;
        return $this;
    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    public function setOrderBy(string $orderBy): static
    {
        $this->orderBy = trim($orderBy);
        // validate orderBy string
        if (!empty($this->orderBy) && !preg_match('/^[a-z0-9_-]+$/i', $this->orderBy)) {
            Log::warning("Invalid orderBy value: " . trim($this->orderBy));
            $this->orderBy = '';
        }
        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): static
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @callable function (mixed $row, Table $table):array { return ['class' => 'test']; }
     */
    public function getRowAttrs(object|array|null $row = null): array
    {
        if (is_callable($this->rowAttrs) && !is_null($row)) {
            return ($this->rowAttrs)($row, $this) ?? [];
        }
        return $this->rowAttrs ?? [];
    }

    public function addRowAttrs(callable|array|null $rowAttrs): static
    {
        $this->rowAttrs = $rowAttrs;
        return $this;
    }

    public function getCell(string $name): ?Cell
    {
        /** @var Cell $cell */
        $cell = $this->cells->get($name);
        return $cell;
    }

    public function removeCell(string $name): static
    {
        $this->cells->forget([$name]);
        return $this;
    }

    /**
     * @return Collection<int,Cell>
     */
    public function getCells(object|array|null $row = null): Collection
    {
        if (!is_null($row)) {
            foreach ($this->cells as $cell) {
                $cell->setRow($row);
            }
        }
        return $this->cells;
    }

    public function appendCell(string|Cell $cell, ?string $after = null): Cell
    {
        if (is_string($cell)) {
            $cell = new Cell($cell);
        }
        if ($this->getCells()->has($cell->getName())) {
            throw new \Exception("Cell with name '{$cell->getName()}' already exists.");
        }
        $cell->setTable($this);

        if ($after) {
            $idx = $this->cells->keys()->search($after);
            // If the key is not found, add at the end
            $idx = ($idx === false) ? $this->cells->count() - 1 : $idx;

            $this->cells = $this->cells->slice(0, $idx + 1)
                ->merge([$cell->getName() => $cell])
                ->merge($this->cells->slice($idx + 1));
        } else {
            $this->cells->put($cell->getName(), $cell);
        }

        return $cell;
    }

    public function prependCell(string|Cell $cell, ?string $before = null): Cell
    {
        if (is_string($cell)) {
            $cell = new Cell($cell);
        }
        if ($this->getCells()->has($cell->getName())) {
            throw new \Exception("Cell with name '{$cell->getName()}' already exists.");
        }
        $cell->setTable($this);

        if ($before) {
            $idx = $this->cells->keys()->search($before);
            // If the key is not found, add at the beginning
            $idx = ($idx === false) ? 0 : $idx;

            $this->cells = $this->cells->splice($idx, 0, [$cell->getName() => $cell]);
        } else {
            $this->cells->prepend($cell, $cell->getName());
        }

        return $cell;
    }

    public function key(string $key): string
    {
        return self::makeKey($this->getId(), $key);
    }

    /**
     * Create a table specific key using the table id
     * returns: `{$tableId}_{$key}`
     */
    public static function makeKey(string $tableId, string $key): string
    {
        if (str_starts_with($key, $tableId.'_')) return $key;
        return $tableId . '_' . $key;
    }

    // View helper functions

    /**
     * modify url query params
     * renames all params to use the `{id}_key` format
     * Optionally send $params as the first argument to add the params to the current url
     *
     * @example
     *  $url = $table->url($myUrl, ['tp' => 1, 'tl' => 10]);
     *  // returns: /my-url?_tid={id}&{id}_tl=10&{id}_tp=1
     *  $url = $table->url(['tp' => 1, 'tl' => 10]);
     *  // returns: /current-url?_tid={id}&{id}_tl=10&{id}_tp=1
     */
    public function url(array|string $url, ?array $params = null): string
    {
        if (is_array($url)) {
            $params = $url;
            $url = request()->fullUrl();
        }
        $url = url()->query($url, [self::QUERY_ID => null]);

        $add = [];
        foreach ($params as $param => $value) {
            if ($param == self::QUERY_ID) continue;
            if (!str_starts_with($param, $this->getId().'_')) {
                $add[$this->key($param)] = $value;
            }
        }
        if (!isset($add[self::QUERY_ID]) && count($params)) {
            $add[self::QUERY_ID] = $this->getId();
        }

        return url()->query($url, $add);
    }

    /**
     * return a url with all table state params removed
     * optionally set/remove params with supplied query array
     */
//    public function resetUrl(array $query = []): string
//    {
//        $url = request()->fullUrl();
//        $q = request()->query();
//        foreach ($q as $k => $v) {
//            if (str_starts_with($k, $this->getId().'_')) {
//                $q[$k] = null;
//            }
//        }
//        $q[self::QUERY_ID] = null;
//        $query = array_merge($q, $query);
//        return url()->query($url, $query);
//    }
}
