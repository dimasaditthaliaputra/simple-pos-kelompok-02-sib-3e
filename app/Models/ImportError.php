<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportError extends Model
{
    protected $fillable = ['product_import_id', 'row_number', 'message'];

    public function productImport(): BelongsTo
    {
        return $this->belongsTo(ProductImport::class);
    }
}
