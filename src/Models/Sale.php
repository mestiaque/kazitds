<?php

namespace ME\Kazitds\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'customer_name',
        'mobile_number',
        'total_amount',
        'discount',
        'previous_due',
        'net_amount',
        'sale_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Every sale gets a random token for its public invoice link
        static::creating(function (Sale $sale) {
            $sale->public_token ??= Str::random(12);   // 12 is plenty to be unguessable and keeps the invoice QR small
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function due(): HasOne
    {
        return $this->hasOne(Due::class);
    }

    public function paymentHistories(): HasMany
    {
        return $this->hasMany(PaymentHistory::class);
    }

    // Helper method to calculate totals
    public function calculateTotals()
    {
        $totalAmount = $this->items->sum('total_price');
        $this->total_amount = $totalAmount;
        $this->net_amount = $totalAmount - $this->discount + $this->previous_due;
        $this->save();
    }

    public function getCustomerNameAttribute($value)
    {
        return $value ?: ($this->customer ? $this->customer->name : null);
    }

    public function getMobileNumberAttribute($value)
    {
        return $value ?: ($this->customer ? $this->customer->phone : null);
    }
}
