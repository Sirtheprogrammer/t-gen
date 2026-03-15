<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    private function checkSuperAdmin()
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403, 'Unauthorized access.');
    }

    public function index()
    {
        $this->checkSuperAdmin();
        $users = User::withSum(['transactions' => function ($query) {
            $query->whereIn('payment_status', ['COMPLETED', 'completed']);
        }], 'amount')->get();
        return view('dashboard.users.index', compact('users'));
    }

    public function create()
    {
        $this->checkSuperAdmin();
        return view('dashboard.users.create');
    }

    public function store(Request $request)
    {
        $this->checkSuperAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:super_admin,user',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function destroy(User $user)
    {
        $this->checkSuperAdmin();
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Cannot delete yourself.']);
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
