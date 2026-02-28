<?php

namespace Tk\Table\Records;

use Tk\Table\Table;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;

/**
 * Populate a table with rows from a CSV file.s
 */
class CsvRecords extends ArrayRecords
{
    protected string $csvFile;
    protected bool $hasHeaders = true;

    public function __construct(string $csvPath, bool $hasHeaders = true)
    {
        $this->csvFile = $csvPath;
        $this->hasHeaders = $hasHeaders;
        if (!is_file($csvPath)) {
            throw new \Exception("File not found: {$csvPath}");
        }
    }

    /**
     * filter, sort, and paginate the array returning the results
     */
    protected function initArray(): array
    {
        if (!$this->getTable()) {
            throw new \Exception('Table not set');
        }

        $this->rows = [];
        $header = [];

        // Open the file in read mode ('r')
        if (($handle = fopen($this->csvFile, "r")) !== false) {
            if ($this->hasHeaders) {
                // Read the first line to get the header columns
                $header = fgetcsv($handle, escape: '');
            }

            // Loop through the remaining rows
            while (($row = fgetcsv($handle, escape: '')) !== false) {
                if (count($header) < count($row)) {
                    $pad = count($row) - count($header);
                    for( $i = 0; $i < $pad; $i++) {
                        $header[] = 'row_'.(count($header)+1);
                    }
                }
                // Combine the header array with the current row array to create an associative array
                if (count($header) === count($row)) {
                    $this->rows[] = array_combine($header, $row);
                }
            }
            // Close the file handle
            fclose($handle);
        }

        return parent::initArray();
    }


}
