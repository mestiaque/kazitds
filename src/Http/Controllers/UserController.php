<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Role;
use ME\Kazitds\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('authorization:user.view')->only(['index', 'show']);
        $this->middleware('authorization:user.create')->only(['create', 'store']);
        $this->middleware('authorization:user.edit')->only(['edit', 'update', 'toggleActive']);
        $this->middleware('authorization:user.delete')->only('destroy');
        $this->middleware('authorization:user.print')->only('printInvoice');
    }

    public function index()
    {
        $users = User::with('roles')->latest()->paginate(get_setting('pagination', 10))
;
        return view('kazitds::users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('kazitds::users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|exists:roles,id',
            'is_active' => 'nullable|boolean',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => $request->has('is_active') ? 1 : 0,
        ];

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = storage_path('app/public/images/profile_images');

            // Ensure the directory exists
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            $image->move($imagePath, $imageName);
            $data['profile_image'] = $imageName;
        }

        $user = User::create($data);

        // Assign role
        if ($request->has('role')) {
            $user->roles()->sync([$request->role]);
        }

        return redirect()->route('users.index')->with('success', __('kazitds::kazitds.User created successfully'));
    }

    public function show(User $user)
    {
        $user->load('roles');
        return view('kazitds::users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        return view('kazitds::users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|confirmed|min:8',
            'role' => 'required|exists:roles,id',
            'is_active' => 'nullable|boolean',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // For security, don't allow users to deactivate their own account
        if ($user->id !== auth()->id()) {
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Unlink old image if it exists
            if ($user->profile_image && file_exists(storage_path('app/public/images/profile_images/' . $user->profile_image))) {
                unlink(storage_path('app/public/images/profile_images/' . $user->profile_image));
            }

            $image = $request->file('profile_image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = storage_path('app/public/images/profile_images');

            // Ensure the directory exists
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            $image->move($imagePath, $imageName);
            $data['profile_image'] = $imageName;
        }

        $user->update($data);

        // Sync role
        if ($request->has('role')) {
            $user->roles()->sync([$request->role]);
        } else {
            $user->roles()->detach();
        }

        return redirect()->route('users.index')->with('success', __('kazitds::kazitds.User updated successfully'));
    }

    public function toggleActive(User $user)
    {
        // Prevent deactivating your own account
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot deactivate your own account');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->route('users.index')
            ->with('success', "User {$status} successfully");
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}
