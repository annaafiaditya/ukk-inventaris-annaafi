<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Only admin can access this page
        if ($request->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => ['required', Rule::in(['admin', 'staff'])],
        ]);

        // Insert first to get the ID, we'll use a temporary random password
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make(Str::random(10)),
        ]);

        // Generate password: first 4 letters of email + user id
        // Example: admin@gmail.com -> admi1
        $prefix = substr($user->email, 0, 4);
        $generatedPassword = $prefix . $user->id;

        // Update password properly
        $user->password = Hash::make($generatedPassword);
        $user->save();

        return redirect()->route('users.index')->with('success', "Akun berhasil dibuat! Password akun ini adalah: {$generatedPassword}");
    }

    public function resetPassword(Request $request, User $user)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $prefix = substr($user->email, 0, 4);
        $generatedPassword = $prefix . $user->id;

        $user->password = Hash::make($generatedPassword);
        $user->save();

        return redirect()->route('users.index')->with('success', "Password untuk {$user->name} berhasil direset menjadi: {$generatedPassword}");
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        if ($request->user()->id === $user->id) {
            return redirect()->route('users.index')->with('error', 'Tidak bisa menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Akun berhasil dihapus.');
    }
}
