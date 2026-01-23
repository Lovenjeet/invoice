<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillTo extends Model
{
    use HasFactory;

    protected $table = 'bill_to';
    
    protected $fillable = [
        'location',
        'name',
        'address1',
        'address2',
        'city',
        'vat_eori',
        'vat_eori2',
        'contact2',
    ];
}