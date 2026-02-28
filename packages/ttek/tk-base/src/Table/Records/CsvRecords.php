<?php

namespace Tk\Table\Records;

use Tk\Table\Table;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;

class CsvRecords extends ArrayRecords
{
    protected string $csvFile;

    public function __construct(Table $table, string $csvPath, bool $hasHeaders = false)
    {
        $this->csvFile = $csvPath;

        // read cev file into array
        // for a more complex/efficient adaptor for large files,
        //    consider only reading the lines required???
        //    use scan_file and other low level file operations.
        $rows = [];

        parent::__construct($table, $rows);
    }

    public function getCsvFile(): string
    {
        return $this->csvFile;
    }

}
