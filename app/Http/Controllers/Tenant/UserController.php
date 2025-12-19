<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $tenant = app('tenant');
        
        $users = $tenant->users()
            ->with('role')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Tenant/Users/Index', [
            'users' => $users,
            'tenant' => $tenant,
        ]);
    }

    public function show(User $user)
    {
        $tenant = app('tenant');
        
        // Ensure user belongs to this tenant
        if ($user->tenant_id !== $tenant->id) {
            abort(404);
        }

        $user->load('role');

        return Inertia::render('Tenant/Users/Show', [
            'user' => $user,
            'tenant' => $tenant,
        ]);
    }

    public function create()
    {
        $tenant = app('tenant');
        $roles = Role::where('is_system', true)->get();

        return Inertia::render('Tenant/Users/Create', [
            'roles' => $roles,
            'tenant' => $tenant,
        ]);
    }

    public function store(Request $request)
    {
        $tenant = app('tenant');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'tenant_id' => $tenant->id,
            'active' => true,
            'email_verified_at' => now(), // Auto-verify for admin-created users
        ]);

        return redirect()
            ->route('tenant.users.index')
            ->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        $tenant = app('tenant');
        
        // Ensure user belongs to this tenant
        if ($user->tenant_id !== $tenant->id) {
            abort(404);
        }

        $roles = Role::where('is_system', true)->get();

        return Inertia::render('Tenant/Users/Edit', [
            'user' => $user,
            'roles' => $roles,
            'tenant' => $tenant,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $tenant = app('tenant');
        
        // Ensure user belongs to this tenant
        if ($user->tenant_id !== $tenant->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id',
            'active' => 'boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'active' => $validated['active'] ?? true,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()
            ->route('tenant.users.show', $user)
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $tenant = app('tenant');
        
        // Ensure user belongs to this tenant
        if ($user->tenant_id !== $tenant->id) {
            abort(404);
        }

        // Prevent deleting the last owner
        if ($user->role?->key === 'owner') {
            $ownerCount = $tenant->users()
                ->whereHas('role', function ($query) {
                    $query->where('key', 'owner');
                })
                ->count();

            if ($ownerCount <= 1) {
                return back()->with('error', 'Cannot delete the last owner of the tenant');
            }
        }

        // Prevent users from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account');
        }

        $user->delete();

        return redirect()
            ->route('tenant.users.index')
            ->with('success', 'User deleted successfully');
    }

    public function toggleStatus(User $user)
    {
        $tenant = app('tenant');
        
        // Ensure user belongs to this tenant
        if ($user->tenant_id !== $tenant->id) {
            abort(404);
        }

        // Prevent users from deactivating themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account');
        }

        $user->update(['active' => !$user->active]);

        $status = $user->active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully");
    }
}