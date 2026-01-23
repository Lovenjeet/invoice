<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipTo extends Model
{
    use HasFactory;

    protected $table = 'ship_to';

    protected $fillable = [
        'name',
        'address'
    ];
}