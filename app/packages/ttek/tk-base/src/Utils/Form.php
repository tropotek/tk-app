<?php

namespace Tk\Utils;

class Form
{

    /**
     * Create a label from a field name string
     * The default label uses the name (EG: `fieldNameSelect` -> `Field Name Select`)
     */
    public static function makeFieldLabel(string $name): string
    {
        $label = $name;

        $label = str_replace(['_', '-', '.'], ' ', $label);

        $label = ucwords(preg_replace('/[A-Z]/', ' $0', $label));

        $label = preg_replace('/(\[\])/', '', $label);

        if (str_ends_with(strtolower($label), ' id')) {
            $label = substr($label, 0, -3);
        }

        return $label;
    }

    /**
     * A basic comparison helper for select, checkbox, and radio fields
     */
    public static function isSelected(string $optValue, null|array|string $fieldValue): bool
    {
        if (is_null($fieldValue)) return false;
        if (is_array($fieldValue)) {
            return in_array($optValue, $fieldValue);
        }
        return $fieldValue == $optValue;
    }
}
