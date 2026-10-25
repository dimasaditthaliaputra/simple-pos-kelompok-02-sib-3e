<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductImport;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class ProductsImport implements ToModel, WithHeadingRow, WithChunkReading, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    public int $imported = 0;

    public function __construct(private ProductImport $import) {}

    public function model(array $row): Product
    {
        $this->imported++;

        // Disimpan oleh Laravel Excel setelah dikembalikan.
        return tap(
            Product::firstOrNew([
                'name' => $row['name'],
                'category_id' => $row['category_id'],
            ]),
            fn (Product $product) => $product->price = $row['price']
        );
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }

    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->import->errors()->create([
                'row_number' => $failure->row(),
                'message' => implode('; ', $failure->errors()),
            ]);
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
