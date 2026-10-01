<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'price', 'stock'];
    
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}