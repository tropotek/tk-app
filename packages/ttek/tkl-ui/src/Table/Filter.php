<?php

namespace Tk\Table;

use Illuminate\View\ComponentAttributeBag;

class Filter
{
    const string TYPE_SELECT = 'select';
    const string TYPE_TEXT = 'text';
    const string TYPE_CHECKBOX = 'checkbox';
    const string TYPE_DATE = 'date';

    const array FILTER_TYPES = [
        self::TYPE_SELECT,
        self::TYPE_TEXT,
        self::TYPE_CHECKBOX,
        self::TYPE_DATE,
    ];

    public string $key = '';
    public string $label = '';
    public string $type = self::TYPE_SELECT;
    public mixed $options = []; // array|callable
    public string $dependsOn = '';
    public bool $visible = true;
    public string $defaultValue = '';

    protected mixed $table = null;  // HasTable trait
    protected ComponentAttributeBag $attrs;


    public function __construct(
        string $key,
        string $label = '',
        string $type = self::TYPE_SELECT,
        mixed $options = [],
        bool $visible = true,
        string $dependsOn = '',
        string $defaultValue = ''
    )
    {
        $this->attrs = new ComponentAttributeBag();
        $this->key = $key;
        if (empty($label)) {
            $label = strval(preg_replace('/(Id|_id)$/', '', $key));
            $label = str_replace(['_', '-'], ' ', $label);
            $label = ucwords(strval(preg_replace('/[A-Z]/', ' $0', $label)));
        }
        $this->setLabel($label);
        $this->setType($type);
        $this->setOptions($options);
        $this->setDependsOn($dependsOn);
        $this->setVisible($visible);
        $this->setDefaultValue($defaultValue);
    }

    /**
     * @return IsTable
     */
    public function getTable(): mixed
    {
        return $this->table;
    }

    public function setTable(mixed $table): static
    {
        if (is_null($table)) {
            throw new \InvalidArgumentException("cannot set a null table object");
        }
        if (!method_exists($table, 'rows')) {
            throw new \InvalidArgumentException('expected table object using the isTable trait');
        }
        $this->table = $table;
        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        if (!in_array($type, self::FILTER_TYPES)) {
            throw new \InvalidArgumentException("Invalid filter type: {$type}");
        }
        $this->type = $type;
        return $this;
    }

    public function getOptions(): array
    {
        if (is_callable($this->options)) {
            return call_user_func($this->options, $this->dependsOn);
        }
        return $this->options;
    }

    /**
     * @callable function (string $dependsOn): array { }
     */
    public function setOptions(array|callable $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function getDependsOn(): string
    {
        return $this->dependsOn;
    }

    public function setDependsOn(string $dependsOn): static
    {
        $this->dependsOn = $dependsOn;
        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): static
    {
        $this->visible = $visible;
        return $this;
    }

    public function addClass(string $class): static
    {
        $this->attrs = $this->getAttrs()->class($class);
        return $this;
    }

    public function addAttrs(array $attrs): static
    {
        $this->attrs = $this->getAttrs()->merge($attrs);
        return $this;
    }

    public function getAttrs(): ComponentAttributeBag
    {
        $this->attrs ??= new ComponentAttributeBag();
        return $this->attrs;
    }

    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(string $defaultValue): static
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

}
