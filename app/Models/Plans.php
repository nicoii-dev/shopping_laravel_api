<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;
class Plans extends Model
{
    use Uuids;
    protected $fillable = [
        'name',
        'description',
        'price',
        'post_per_day',
    ];
}
