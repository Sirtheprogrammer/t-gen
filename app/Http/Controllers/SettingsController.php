<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SettingsController extends Controller
{
    /**
     * Show the settings page.
     */
    public function index()
    {
        return view('dashboard.settings.index', [
            'adminEmail' => Auth::user()->email,
        ]);
    }

    /**
     * Save admin settings.
     * POST /settings
     */
    public function store(Request $request)
    {
        if ($request->has('allow_self_registration')) {
            $validated = $request->validate([
                'allow_self_registration' => 'required|in:0,1',
            ]);
            AdminSetting::set('allow_self_registration', $validated['allow_self_registration']);
        }

        if ($request->has('admin_email')) {
            $validated = $request->validate([
                'admin_email'    => 'required|email|max:255|unique:users,email,'.Auth::id(),
                'new_password'   => 'nullable|string|min:8|confirmed',
            ]);

            $user = Auth::user();
            $user->email = $validated['admin_email'];
            if (! empty($validated['new_password'])) {
                $user->password = Hash::make($validated['new_password']);
            }
            $user->save();

            // Sync global notification email to AdminSetting
            AdminSetting::set('admin_email', $validated['admin_email']);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Settings saved successfully!');
    }
}
