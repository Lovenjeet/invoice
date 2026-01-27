<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'bill_to_id',   
        'ship_to_id',
        'invoice_no',
        'invoice_date',
        'terms',
        'remarks',
        'unc_number',
        'approval_email',
        'status',
        'subtotal',
        'shipping_value',
        'total',
        'total_boxes',
        'total_gw',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'shipping_value' => 'decimal:2',
        'total' => 'decimal:2',
        'total_gw' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function billTo(): BelongsTo
    {
        return $this->belongsTo(BillTo::class, 'bill_to_id');
    }

    public function shipTo(): BelongsTo
    {
        return $this->belongsTo(ShipTo::class, 'ship_to_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}

