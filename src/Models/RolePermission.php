<?php

namespace ME\Kazitds\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ME\Models\Role;

class RolePermission extends Model
{
    use HasFactory;

    protected $table = 'role_permissions';

    protected $fillable = ['role_id', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
