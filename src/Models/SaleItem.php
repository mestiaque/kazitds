<?php

namespace ME\Kazitds\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id', 
        'product_variant_id', 
        'quantity', 
        'price_per_unit',
        'total_price',
        'item_discount'
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
    
    // Helper method to calculate total price
    public function calculateTotal()
    {
        $this->total_price = $this->quantity * $this->price_per_unit - $this->item_discount;
        $this->save();
    }
}
