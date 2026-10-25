<?php

namespace App\Jobs;

use App\Imports\ProductsImport;
use App\Models\Product;
use App\Models\ProductImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ProcessProductImport implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [10, 60];
    public int $timeout = 60;

    public function __construct(
        private string $csvPath,
        private ProductImport $import,
    ) {}

    public function handle(): void
    {
        // Retry dimulai dari nol agar hitungan dan daftar error tidak dobel.
        $this->import->errors()->delete();
        $this->import->update(['status' => 'processing']);

        $success = $this->import->driver === 'excel'
            ? $this->runExcel()
            : $this->runManual();

        $this->import->update([
            'status' => 'done',
            'success_count' => $success,
            'failed_count' => $this->import->errors()->distinct()->count('row_number'),
        ]);

        Storage::delete($this->csvPath);
    }

    public function failed(Throwable $e): void
    {
        $this->import->update(['status' => 'failed']);
    }

    private function runExcel(): int
    {
        $import = new ProductsImport($this->import);

        Excel::import(
            $import,
            $this->csvPath,
            config('filesystems.default'),
            ExcelFormat::CSV
        );

        return $import->imported;
    }

    private function runManual(): int
    {
        $lines = file(
            Storage::path($this->csvPath),
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );

        $rows = array_map('str_getcsv', $lines);
        $header = array_shift($rows);
        $success = 0;

        foreach ($rows as $i => $row) {
            $rowNumber = $i + 2; // baris 1 adalah header

            if (count($row) !== count($header)) {
                $this->recordError($rowNumber, 'Jumlah kolom tidak sesuai header.');
                continue;
            }

            $validator = Validator::make(
                array_combine($header, $row),
                [
                    'name' => ['required', 'string'],
                    'category_id' => [
                        'required',
                        'exists:categories,id'
                    ],
                    'price' => ['required', 'integer', 'min:0'],
                ]
            );

            if ($validator->fails()) {
                $this->recordError($rowNumber, implode('; ', $validator->errors()->all()));
                continue;
            }

            $data = $validator->validated();

            Product::updateOrCreate(
                [
                    'name' => $data['name'],
                    'category_id' => $data['category_id'],
                ],
                [
                    'price' => $data['price'],
                ]
            );

            $success++;
        }

        return $success;
    }

    private function recordError(int $rowNumber, string $message): void
    {
        $this->import->errors()->create([
            'row_number' => $rowNumber,
            'message' => $message,
        ]);
    }
}
