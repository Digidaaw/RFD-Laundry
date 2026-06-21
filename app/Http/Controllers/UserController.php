<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'updated_latest');

        $query = User::where('role', 'kasir');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($sort === 'updated_oldest') {
            $query->orderBy('updated_at', 'asc');
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        $kasirs = $query->paginate(10)->appends(['search' => $search, 'sort' => $sort]);
        return view('admin.kasir', compact('kasirs', 'search', 'sort'));
    }

    public function store(UserStoreRequest $request)
    {
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password,
            'role' => 'kasir',
        ]);

        return redirect()->route('users.index')->with('success', 'User kasir berhasil ditambahkan.');
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        abort_unless($user->role === 'kasir', 404);

        $updateData = [
            'name' => $request->name,
            'username' => $request->username,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        $user->update($updateData);

        return redirect()->route('users.index')->with('success', 'Data kasir berhasil diperbarui.');
    }

    public function toggleStatus(User $user)
    {
        abort_unless($user->role === 'kasir', 404);

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('users.index')->with('success', "Kasir {$user->name} berhasil {$status}.");
    }
}
