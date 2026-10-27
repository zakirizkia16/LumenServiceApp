<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    // 7 fields fillable
    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'price',
        'stock',
        'description',
        'status'
    ];

    public $timestamps = true;
}