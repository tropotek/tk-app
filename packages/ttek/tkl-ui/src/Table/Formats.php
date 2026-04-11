<?php

namespace Tk\Table;

use Carbon\Carbon;
//use Propaganistas\LaravelPhone\PhoneNumber;

class Formats
{

    public static function date($row, Cell $cell): string
    {
        $val = $cell->getRowValue($row);
        return $val ? Carbon::parse($val)->format('d M Y') : '';
    }

    // package not installed
//    public static function phone($row, Cell $cell): string
//    {
//        $val = $cell->getRowValue($row);
//        return $val ? new PhoneNumber($val)->formatInternational() : '';
//    }

    public static function yesNo($row, Cell $cell): string
    {
        $val = (string)$cell->getRowValue($row);
        return in_array(strtolower($val), ['1', 'true', 'yes', 'y']) ? 'Yes' : 'No';
    }

}
