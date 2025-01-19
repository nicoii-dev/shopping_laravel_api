<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Products extends Model
{

    use HasFactory;

    protected $fillable = [
        'categories_id',
        'name',
        'price',
        'quantity',
        'description',
        'images',
    ];
}
