<?php

namespace ME\Kazitds\Models;

use Illuminate\Database\Eloquent\Model;


class Due extends Model
{
    protected $table = 'dues';

    protected $fillable = [
        'sale_id',
        'customer_id',
        'sale_amount',
        'paid_amount',
        'due',
        'previous_due',
        'total_due',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
