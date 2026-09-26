<?php

namespace ME\Kazitds\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_number',
        'purchase_id',
        'product_variant_id',
        'returned_quantity',
        'return_price_per_unit',
        'total_return_amount',
        'return_date',
        'reason',
        'notes',
        'status',
        'total_amount'
    ];

    protected $casts = [
        'return_date' => 'date',
        'returned_quantity' => 'integer',
        'return_price_per_unit' => 'decimal:2',
        'total_return_amount' => 'decimal:2',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public static function generateReturnNumber()
    {
        $lastReturn = self::latest()->first();
        $number = $lastReturn ? (int) substr($lastReturn->return_number, 3) + 1 : 1;
        return 'PR-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
