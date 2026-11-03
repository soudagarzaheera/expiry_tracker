<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::withCount('products')
            ->when($request->search, fn ($q, $s) => $q->where(fn ($i) => $i->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user)],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($request->user()->is($user) && ($validated['role'] !== 'admin' || ! $validated['is_active'])) {
            return back()->withErrors(['role' => 'You cannot remove your own admin access or deactivate your own account.'])->withInput();
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User account updated.');
    }

    public function toggle(Request $request, User $user)
    {
        abort_if($request->user()->is($user), 422, 'You cannot deactivate your own account.');
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'User activated.' : 'User deactivated.');
    }
}
