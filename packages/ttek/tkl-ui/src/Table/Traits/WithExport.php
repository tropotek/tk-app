<?php

namespace Tk\Table\Traits;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tk\Support\ItemCollection;
use Tk\Table\ActionColumn;
use Tk\Table\Column;

/**
 *
 * IsTable methods used:
 * @method ItemCollection getVisibleColumns()
 */
trait WithExport
{

    /**
     * By default, a table is exportable if the export() method exists
     * override and return true when using a route to export a table
     */
    public function exportable(): bool
    {
        return method_exists($this, 'export');
    }

    /**
     * CSV export route name. Defaults to current route name + '.export'.
     */
    public function exportRoute(): string
    {
        return (request()->route()->getName() ?? '') . '.export';
    }

    /**
     * build a CSV file from an array of rows
     * Expects the rows to be a key/value map of column names to values
     * @param array<string,mixed>|Collection<int,array<string,mixed>> $rows
     */
    public function exportCsv(array|Collection $rows, string $fileName = 'unknown.csv'): StreamedResponse
    {
        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            $headersWritten = false;

            $exportColumns = $this->exportColumns();
            foreach ($rows as $row) {
                if (!$headersWritten) {
                    fputcsv(
                        stream: $handle,
                        fields: $exportColumns->pluck('label')->all(),
                        escape: '',
                    );
                    $headersWritten = true;
                }
                $export = $exportColumns->map(fn($col) => $col['value']($row))->all();
                fputcsv(
                    stream: $handle,
                    fields: $export,
                    escape: '',
                );
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * The default exportable columns are the visible columns
     * Override this method to customize the exportable columns
     *
     * Eg:
     * return collect([
     *     ['label' => 'Name',    'value' => fn($row) => $row->name_last_first],
     *     ['label' => 'Email',   'value' => fn($row) => $row->email],
     *     ['label' => 'City',    'value' => fn($row) => $row->city],
     *     ['label' => 'Country', 'value' => fn($row) => $row->country],
     *     ['label' => 'Created', 'value' => fn($row) => Carbon::parse($row->created_at)->format('d M Y')],
     * ]);
     */
    public function exportColumns(): Collection
    {
        return $this->getVisibleColumns()
            ->filter(fn($column) => !($column instanceof ActionColumn))
            ->map(fn(Column $column) => [
                'label' => $column->header,
                'value' => fn($row) => $column->value($row),
            ]);
    }
}
