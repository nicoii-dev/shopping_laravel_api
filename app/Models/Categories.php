<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Uuids;
class Categories extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        'name',
        'image',
        'description',
    ];

}
