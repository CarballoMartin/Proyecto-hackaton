<?php

namespace App\Services;

use App\Exceptions\MissingHeadersException;
use App\Interfaces\FileProcessorInterface;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetException;
use Illuminate\Support\Facades\Log;

class CsvExcelProcessor implements FileProcessorInterface
{
    private array $requiredHeaders = [
        'apellido y nombre',
        //'dni',
        //'cuil',
        //'email',
        'telefono',
        'rnspa'
    ];

    public function process(string $filePath, ?string $originalName = null): Collection
    {
        $extension = strtolower(pathinfo($originalName ?? $filePath, PATHINFO_EXTENSION));

        $supportedExtensions = ['csv', 'txt', 'xls', 'xlsx', 'ods'];
        if (!in_array($extension, $supportedExtensions)) {
            throw new \InvalidArgumentException(
                "Formato de archivo no soportado. Formatos permitidos: " . implode(', ', $supportedExtensions)
            );
        }

        try {
            if (in_array($extension, ['csv', 'txt'])) {
                list($headerRow, $rows) = $this->processCsv($filePath);
            } else {
                list($headerRow, $rows) = $this->processSpreadsheet($filePath);
            }
        } catch (SpreadsheetException $e) {
            throw new \RuntimeException("Error al procesar el archivo: " . $e->getMessage());
        }

        $headerRowOriginal = $headerRow;
        $headerRow = array_map('trim', array_map('strtolower', $headerRowOriginal));

        $missingHeaders = array_diff($this->requiredHeaders, $headerRow);

        if (!empty($missingHeaders)) {
            throw new MissingHeadersException($missingHeaders, "El archivo '{$originalName}' no es válido.");
        }

        $collection = collect();
        foreach ($rows as $row) {
            $collection->push(array_combine($headerRow, $row));
        }

        return $collection;
    }

    protected function processCsv(string $path): array
    {
        $fileHandle = fopen($path, 'r');
        if ($fileHandle === false) {
            throw new \RuntimeException("No se pudo abrir el archivo CSV");
        }

        try {
            $headers = array_map('trim', fgetcsv($fileHandle));
            if (empty($headers)) {
                return [[], []];
            }

            $rows = [];
            while (($row = fgetcsv($fileHandle)) !== false) {
                $rows[] = array_map('trim', $row);
            }
        } finally {
            fclose($fileHandle);
        }
        return [$headers, $rows];
    }

    protected function processSpreadsheet(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, false, false);

        if (empty($rows)) {
            return [[], []];
        }

        $headers = array_map('trim', array_shift($rows));
        if (empty($headers)) {
            return [[], []];
        }

        foreach ($rows as &$row) {
            $row = array_map('trim', $row);
        }

        return [$headers, $rows];
    }
}
