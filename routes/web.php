<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\SettingsController;
use App\Mail\AdminLoginNotification;
use App\Models\AdminSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// Public Routes - Login
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function () {
        $credentials = request()->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Send login notification email (non-blocking)
            $storedEmail = AdminSetting::where('key', 'admin_email')->whereNull('user_id')->value('value') ?? 'admin@example.com';
            try {
                Mail::to($storedEmail)->send(
                    new AdminLoginNotification(
                        request()->ip(),
                        request()->userAgent() ?? 'Unknown'
                    )
                );
            } catch (\Exception $e) {
                \Log::warning('Login notification email failed: ' . $e->getMessage());
            }

            return redirect('/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    })->name('login.store');

    Route::get('/register', function () {
        $allowRegistration = AdminSetting::where('key', 'allow_self_registration')->whereNull('user_id')->value('value');
        if ($allowRegistration !== '1') {
            abort(404);
        }
        return view('auth.register');
    })->name('register');

    Route::post('/register', function () {
        $allowRegistration = AdminSetting::where('key', 'allow_self_registration')->whereNull('user_id')->value('value');
        if ($allowRegistration !== '1') {
            abort(404);
        }

        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    })->name('register.store');
});

// Protected Routes - Dashboard
Route::middleware(['auth.custom'])->group(function () {
    // Super Admin Routes
    Route::prefix('admin/users')->group(function () {
        Route::get('/', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::get('/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
        Route::post('/', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::delete('/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
    });

    // Dashboard
    Route::get('/dashboard', function () {
        // Scope queries to the authenticated user unless Super Admin
        $user = Auth::user();
        if ($user->isSuperAdmin()) {
            $totalPages    = \App\Models\Page::count();
            $activePages   = \App\Models\Page::where('is_active', true)->count();
            $inactivePages = \App\Models\Page::where('is_active', false)->count();
            $totalRevenue  = \App\Models\Transaction::where('payment_status', 'COMPLETED')
                                ->orWhere('payment_status', 'completed')
                                ->sum('amount');
            $recentPages   = \App\Models\Page::latest()->take(5)->get();
        } else {
            $totalPages    = $user->pages()->count();
            $activePages   = $user->pages()->where('is_active', true)->count();
            $inactivePages = $user->pages()->where('is_active', false)->count();
            $totalRevenue  = $user->transactions()->where('payment_status', 'COMPLETED')
                                ->orWhere('payment_status', 'completed')
                                ->sum('amount');
            $recentPages   = $user->pages()->latest()->take(5)->get();
        }

        return view('dashboard.index', [
            'totalPages'    => $totalPages,
            'activePages'   => $activePages,
            'inactivePages' => $inactivePages,
            'totalRevenue'  => $totalRevenue,
            'recentPages'   => $recentPages,
        ]);
    })->name('dashboard');

    // Pages Management
    Route::controller(PageController::class)->prefix('pages')->group(function () {
        Route::get('/', 'index')->name('pages.index');
        Route::get('/create', 'create')->name('pages.create');
        Route::post('/', 'store')->name('pages.store');
        Route::get('/{page}/edit', 'edit')->name('pages.edit');
        Route::put('/{page}', 'update')->name('pages.update');
        Route::delete('/{page}', 'destroy')->name('pages.destroy');
        Route::patch('/{page}/toggle', 'toggle')->name('pages.toggle');
    });

    // Templates
    Route::get('/templates', function () {
        $templates = [
            ['id' => 'template1', 'name' => 'template1', 'cover' => '/images/youtubex.jpeg'],
            ['id' => 'template2', 'name' => 'template2', 'cover' => '/images/utamuplus.png'],
        ];

        return view('dashboard.templates.index', ['templates' => $templates]);
    })->name('templates.index');

    // Payment Gateway Settings
    Route::controller(PaymentGatewayController::class)->prefix('payment-gateways')->group(function () {
        Route::get('/', 'index')->name('payment-gateways.index');
        Route::post('/{name}/update', 'update')->name('payment-gateways.update');
        Route::post('/{gateway}/toggle', 'toggle')->name('payment-gateways.toggle');
    });

    // Settings
    Route::controller(SettingsController::class)->group(function () {
        Route::get('/settings', 'index')->name('settings.index');
        Route::post('/settings', 'store')->name('settings.store');
    });

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    })->name('logout');
});

// Payment Routes (accessible by anyone for public pages)
Route::controller(\App\Http\Controllers\PaymentController::class)->prefix('api/payments')->group(function () {
    Route::post('/create-order', 'createOrder')->name('payments.create-order');
    Route::post('/check-status', 'checkStatus')->name('payments.check-status');
});

// Public Routes - Pages (must be last so dashboard routes take priority)
Route::get('/{page}', [PageController::class, 'show'])->where('page', '[a-z0-9-]+')->name('page.show');

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }

    return redirect('/login');
});
