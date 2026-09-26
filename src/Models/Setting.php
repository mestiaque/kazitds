<?php

namespace ME\Kazitds\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        if ($key === 'sms_permit') {
            $val = static::get('sms_notifications', []);
            return is_array($val) ? $val : json_decode($val, true);
        }

        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }


    public static function set(string $key, $value)
    {
        $setting = static::firstOrCreate(['key' => $key]);
        $setting->value = $value;
        $setting->save();

        return $setting;
    }
}
