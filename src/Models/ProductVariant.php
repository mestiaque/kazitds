<?php

namespace ME\Kazitds\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'brand_id', 'pack_id', 'is_active'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function saleReturns(): HasMany
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function purchaseReturns(): HasMany
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    /**
     * Current stock of many variants at once: [variant_id => stock], one grouped query per table
     * instead of getCurrentStock()'s 4 queries per variant. Same formula as getCurrentStock().
     */
    public static function stockMap(?array $variantIds = null): array
    {
        $sum = function (string $table, string $column, bool $approvedOnly = false) use ($variantIds) {
            return DB::table($table)
                ->when($approvedOnly, fn ($q) => $q->where('status', 'approved'))
                ->when($variantIds !== null, fn ($q) => $q->whereIn('product_variant_id', $variantIds))
                ->groupBy('product_variant_id')
                ->pluck(DB::raw("SUM({$column})"), 'product_variant_id');
        };

        $purchased = $sum('purchases', 'quantity');
        $sold = $sum('sale_items', 'quantity');
        $saleReturned = $sum('sale_returns', 'returned_quantity', true);
        $purchaseReturned = $sum('purchase_returns', 'returned_quantity', true);

        $ids = $variantIds ?? $purchased->keys()->merge($sold->keys())->merge($saleReturned->keys())->merge($purchaseReturned->keys())->unique()->all();

        $stock = [];
        foreach ($ids as $id) {
            $stock[$id] = (int) (($purchased[$id] ?? 0) - ($sold[$id] ?? 0) + ($saleReturned[$id] ?? 0) - ($purchaseReturned[$id] ?? 0));
        }

        return $stock;
    }

    // Helper method to get current stock
    public function getCurrentStock()
    {
        $purchased = $this->purchases()->sum('quantity');
        $sold = $this->saleItems()->sum('quantity');

        // Sale returns increase stock (returned products come back)
        $saleReturned = $this->saleReturns()->where('status', 'approved')->sum('returned_quantity');

        // Purchase returns decrease stock (returned products go out)
        $purchaseReturned = $this->purchaseReturns()->where('status', 'approved')->sum('returned_quantity');

        return $purchased - $sold + $saleReturned - $purchaseReturned;
    }

    /**
     * Get the total quantity purchased for this variant
     */
    public function getTotalPurchased()
    {
        return $this->purchases()->sum('quantity');
    }

    /**
     * Get the total quantity sold for this variant
     */
    public function getTotalSold()
    {
        return $this->saleItems()->sum('quantity');
    }

    /**
     * Get the total quantity returned from sales (increases stock)
     */
    public function getTotalSaleReturned()
    {
        return $this->saleReturns()->where('status', 'approved')->sum('returned_quantity');
    }

    /**
     * Get the total quantity returned to purchases (decreases stock)
     */
    public function getTotalPurchaseReturned()
    {
        return $this->purchaseReturns()->where('status', 'approved')->sum('returned_quantity');
    }

    /**
     * Check if there's enough stock for a given quantity
     */
    public function hasStock($quantity)
    {
        return $this->getCurrentStock() >= $quantity;
    }

    /**
     * Get available stock for purchase returns (what can be returned)
     */
    public function getAvailableForPurchaseReturn()
    {
        return $this->getCurrentStock();
    }

    public function getVariantName(){
        return $this->product->name . ' - ' . ($this->brand->name ?? '--') . ' - ' . $this->pack->name;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
