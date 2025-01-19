<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAddress extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'default',
        'street',
        'barangay',
        'city',
        'province',
        'region',
        'zipcode',
    ];

}
