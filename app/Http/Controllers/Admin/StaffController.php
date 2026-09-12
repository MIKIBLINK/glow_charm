<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'staff');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->boolean('status'));
        }

        $staff = $query->latest()->paginate(10)->withQueryString();

        return view('admin.staff.index', compact('staff'));
    }

    public function create(): View
    {
        $permissionGroups = config('permissions.groups');

        return view('admin.staff.create', compact('permissionGroups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,staff'],
            'status' => ['boolean'],
            'permissions' => ['array'],
            'permissions.*' => ['string'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->boolean('status', true),
            'permissions' => $this->validPermissions($request->input('permissions', [])),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member created successfully.');
    }

    public function edit(User $staff): View
    {
        abort_if($staff->isAdmin(), 404);

        $permissionGroups = config('permissions.groups');
        $staffPermissions = $staff->permissions ?? [];

        return view('admin.staff.edit', compact('staff', 'permissionGroups', 'staffPermissions'));
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        abort_if($staff->isAdmin(), 404);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:'.User::class.',name,'.$staff->id],
            'email' => ['required', 'email', 'max:255', 'unique:'.User::class.',email,'.$staff->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,staff'],
            'status' => ['boolean'],
            'permissions' => ['array'],
            'permissions.*' => ['string'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->boolean('status', true),
            'permissions' => $this->validPermissions($request->input('permissions', [])),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(User $staff): RedirectResponse
    {
        abort_if($staff->isAdmin(), 404);

        $staff->delete();

        return redirect()->route('staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    /**
     * Keep only permission keys that actually exist in the config map.
     */
    protected function validPermissions(array $input): array
    {
        $allowed = [];

        foreach (config('permissions.groups', []) as $perms) {
            foreach ($perms as $key => $label) {
                $allowed[] = $key;
            }
        }

        return array_values(array_intersect($input, $allowed));
    }
}
