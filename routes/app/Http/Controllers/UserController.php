<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all(); // Mengambil semua data pengguna
        return view('pages.admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'nullable|string|max:15',
        'role' => 'required|in:user,admin',
        'password' => 'required|string|min:8', // Wajib untuk tambah user
    ]);

    // Hash password
    $password = bcrypt($validated['password']);

    // Simpan data user
    User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'phone' => $validated['phone'],
        'role' => $validated['role'],
        'password' => $password,
    ]);

    return redirect()->route('admin.users.index')->with('success', 'User has been added successfully.');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'phone' => 'nullable|string|max:15',
        'role' => 'required|in:user,admin',
        'password' => 'nullable|string|min:8', // Password tidak wajib untuk edit
    ]);

    $user = User::findOrFail($id);

    // Update password jika diubah
    if ($request->filled('password')) {
        $validated['password'] = bcrypt($validated['password']);
    } else {
        unset($validated['password']); // Jika tidak ada password, jangan update
    }

    // Update data user
    $user->update($validated);

    return redirect()->route('admin.users.index')->with('success', 'User has been updated successfully.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete(); // Menghapus data pengguna
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
