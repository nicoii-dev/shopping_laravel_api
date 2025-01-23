<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Uuids;

class Products extends Model
{

    use HasFactory;

    protected $fillable = [
        'category_id',
        'stripe_product_id',
        'name',
        'price',
        'quantity',
        'description',
        'images',
    ];
}
