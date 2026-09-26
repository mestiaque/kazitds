<?php

namespace ME\Kazitds\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use ME\Kazitds\Models\Role;
use ME\Kazitds\Models\RolePermission;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'profile_image',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'role_id',
        'role_title',
        'approved_by',
        'approved_at',
        'is_active',
        'status',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($roleSlug)
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    public function hasPermission($permission)
    {
        if ($this->hasRole('admin')) {
            return true;
        }
        foreach ($this->roles as $role) {
            $rolePermission = RolePermission::where('role_id', $role->id)->first();
            if ($rolePermission && is_array($rolePermission->permissions)) {
                if (in_array($permission, $rolePermission->permissions)) {
                    return true;
                }
            }
        }
        return false;
    }

    //is admin
    public function is_admin()
    {
        return $this->hasRole('admin');
    }

    // public function hasPermission($action)
    // {
    //     return Permission::query()
    //                 ->join('roles', 'roles.id', 'role_permission.role_id')
    //                 ->whereIn('roles.id', Auth::user()->userRoles->pluck('id')->toArray())
    //                 ->where('role_permission.action', $action)
    //                 ->exists();
    // }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }
        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }
        $this->roles()->detach($role);
    }

    public function getAllPermissions()
    {
        $permissions = [];
        foreach ($this->roles as $role) {
            $rolePermission = RolePermission::where('role_id', $role->id)->first();
            if ($rolePermission && is_array($rolePermission->permissions)) {
                $permissions = array_merge($permissions, $rolePermission->permissions);
            }
        }
        return array_unique($permissions);
    }

    public function isActive()
    {
        return $this->is_active == 1;
    }
}
