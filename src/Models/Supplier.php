<?php

namespace ME\Kazitds\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;
    
    // Use the suppliers table until migration renames it
    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'notes'
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'supplier_id');
    }
}
