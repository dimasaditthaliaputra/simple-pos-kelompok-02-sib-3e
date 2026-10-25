<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductImport extends Model
{
    protected $fillable = ['filename', 'driver', 'status', 'success_count', 'failed_count'];

    public function errors(): HasMany
    {
        return $this->hasMany(ImportError::class)->orderBy('row_number');
    }
}
