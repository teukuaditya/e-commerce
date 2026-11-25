<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

        $newRole = $validated['role'] ?? $user->role;
        if ($user->role === 'admin' && $newRole !== 'admin') {
            if (Auth::id() === $user->id) {
                return redirect()
                    ->route('admin.users.index')
                    ->with('error', 'Anda tidak dapat menurunkan role akun sendiri.');
            }
            if (User::where('role', 'admin')->count() <= 1) {
                return redirect()
                    ->route('admin.users.index')
                    ->with('error', 'Tidak dapat menurunkan role admin terakhir.');
            }
        }

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
        if (Auth::id() === $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri dari halaman admin.');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Tidak dapat menghapus admin terakhir.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
