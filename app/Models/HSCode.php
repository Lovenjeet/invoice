<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HSCode extends Model
{
    use HasFactory;

    protected $table = 'hs_codes';

    protected $fillable = [
        'model',
        'sku',
        'hs_code',
        'dimensions',
        'number_of_units',
        'weight',
        'temp_selected',
        'description',
        'desc1',
        'desc2',
        'desc3',
        'dg'
    ];
}