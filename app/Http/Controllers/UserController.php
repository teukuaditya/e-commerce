<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminUserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $users = User::all();

        return view('pages.admin.users.index', compact('users'));
    }

    public function store(AdminUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User has been added successfully.');
    }

    public function update(AdminUserRequest $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        // Password hanya di-update jika dikosongkan tidak; kalau diisi, hash dulu
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User has been updated successfully.');
    }

    public function destroy(User $user): \Illuminate\Http\RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
