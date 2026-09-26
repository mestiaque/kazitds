<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use ME\Kazitds\Models\RolePermission;
use Illuminate\Support\Collection;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('authorization:role.view')->only(['index', 'show']);
        $this->middleware('authorization:role.create')->only(['create', 'store']);
        $this->middleware('authorization:role.edit')->only(['edit', 'update']);
        $this->middleware('authorization:role.delete')->only('destroy');
    }

    public function index()
    {
        $roles = Role::withCount('users')->latest()->paginate(get_setting('pagination', 10))
;
        return view('kazitds::roles.index', compact('roles'));
    }

    public function create()
    {
        $configPermissions = config('permissions') ?? [];
        $permissions = $this->getPermissionsFromConfig($configPermissions);

        return view('kazitds::roles.create', compact('permissions'));
    }

    private function getPermissionsFromConfig(array $configPermissions): Collection
    {
        $permissions = collect();
        $id = 1;

        foreach ($configPermissions as $module => $config) {
            $actions = explode(',', $config['actions']);

            foreach ($actions as $action) {
                $slug = $module . '.' . trim($action);
                $name = ucfirst(trim($action)) . ' ' . $config['title'];

                $permissions->push((object)[
                    'id' => $id++,
                    'name' => $name,
                    'slug' => $slug,
                ]);
            }
        }

        return $permissions;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $configPermissions = config('permissions') ?? [];
            $allPermissions = $this->getPermissionsFromConfig($configPermissions);

            $selectedPermissions = collect($request->permissions)
                ->map(function ($id) use ($allPermissions) {
                    $permission = $allPermissions->firstWhere('id', $id);
                    return $permission ? $permission->slug : null;
                })
                ->filter()
                ->toArray();

            RolePermission::create([
                'role_id' => $role->id,
                'permissions' => $selectedPermissions,
            ]);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }

    public function show(Role $role)
    {
        $role->load('users', 'rolePermission');
        return view('kazitds::roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $configPermissions = config('permissions') ?? [];
        $permissions = $this->getPermissionsFromConfig($configPermissions);

        $rolePermissions = $role->rolePermission ? $role->rolePermission->permissions : [];

        $selectedPermissionIds = $permissions
            ->filter(function ($permission) use ($rolePermissions) {
                return in_array($permission->slug, $rolePermissions);
            })
            ->pluck('id')
            ->toArray();

        return view('kazitds::roles.edit', compact('role', 'permissions', 'selectedPermissionIds'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        if ($role->name !== $request->name && $role->slug !== 'super_admin') {
            $role->slug = Str::slug($request->name);
        }

        $role->name = $request->name;
        $role->description = $request->description;
        $role->save();

        $configPermissions = config('permissions') ?? [];
        $allPermissions = $this->getPermissionsFromConfig($configPermissions);

        $selectedPermissions = collect($request->permissions ?? [])
            ->map(function ($id) use ($allPermissions) {
                $permission = $allPermissions->firstWhere('id', $id);
                return $permission ? $permission->slug : null;
            })
            ->filter()
            ->toArray();

        RolePermission::updateOrCreate(
            ['role_id' => $role->id],
            ['permissions' => $selectedPermissions]
        );

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }


    public function destroy(Role $role)
    {
        if (in_array($role->slug, ['admin', 'super_admin'])) {
            return redirect()->route('roles.index')
                ->with('error', __('kazitds::kazitds.This role cannot be deleted.'));
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }
}
