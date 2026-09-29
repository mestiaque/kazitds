<?php

namespace ME\Kazitds\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ME\Models\Setting;

class LowStock extends Model
{
    use HasFactory;

    protected static $threshold;

    public static function getThreshold()
    {
        if (is_null(self::$threshold)) {
            self::$threshold = (int) Setting::get('low_stock_threshold', 1);
        }
        return self::$threshold;
    }

    protected static function booted()
    {
        self::$threshold = (int) Setting::get('low_stock_threshold', 15);
    }

    public static function count()
    {
        return ProductVariant::all()->filter(function ($variant) {
            return $variant->getCurrentStock() <= self::getThreshold();
        })->count();
    }

    public static function isLow(ProductVariant $variant)
    {
        return $variant->getCurrentStock() <= self::getThreshold();
    }

    public static function list()
    {
        return ProductVariant::all()->filter(function ($variant) {
            return $variant->getCurrentStock() <= self::getThreshold();
        });
    }
}
