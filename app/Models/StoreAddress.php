<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Uuids;
class StoreAddress extends Model
{

    use HasFactory, Uuids;

    protected $fillable = [
        'store_id',
        'main',
        'branch_number',
        'street',
        'barangay',
        'city',
        'province',
        'region',
        'zipcode',
    ];
}
