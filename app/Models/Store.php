<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Uuids;
class Store extends Model
{

    use HasFactory, Uuids;

    protected $fillable = [
        'user_id',
        'name',
        'images',
        'description',
    ];
}
