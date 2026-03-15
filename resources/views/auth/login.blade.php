@extends('layouts.auth')

@section('title', 'Admin Login')

@section('content')
<div class="min-h-screen relative overflow-hidden flex items-center justify-center bg-slate-950 py-12 px-4 sm:px-6 lg:px-8">
    <!-- Floating blobs -->
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -top-24 -left-16 h-52 w-52 rounded-full bg-emerald-600 opacity-70 blur-2xl"></div>
        <div class="absolute -bottom-32 -right-10 h-64 w-64 rounded-full bg-emerald-500 opacity-70 blur-2xl"></div>
        <div class="hidden sm:block absolute top-1/3 right-1/4 h-40 w-40 rounded-full bg-sky-500 opacity-60 blur-2xl"></div>
    </div>

    <div class="relative w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-xl shadow-md p-8 sm:p-10">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-900 border-2 border-red-600 mb-4">
                    <span class="text-2xl font-bold text-black">18+</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 mb-2">TempGen</h1>
                <p class="text-sm text-slate-600 uppercase tracking-wide">TempGen Dashboard</p>
            </div>

            <!-- Form -->
            <form method="POST" action="/login" class="space-y-6">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-900 mb-2">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full px-4 py-3 border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                        placeholder="mail@example.com"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-900 mb-2">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full px-4 py-3 border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        class="w-4 h-4 border-gray-300 rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                    >
                    <label for="remember" class="ml-2 text-sm font-medium text-slate-700 cursor-pointer">
                        Remember me
                    </label>
                </div>

                <!-- Error Message -->
                @if ($errors->any())
                    <div class="rounded-lg bg-rose-50 border border-rose-200 p-4">
                        <p class="text-sm text-rose-700 font-medium">Login Failed</p>
                        <p class="text-sm text-rose-600 mt-1">Please check your credentials and try again.</p>
                    </div>
                @endif

                

                <!-- Login Button -->
                <button
                    type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-white"
                >
                    Sign In
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-slate-200">
                <p class="text-center text-xs text-slate-500">
                    © 2026 powered by. <a href="https://devconnecttz.site" class="font-semibold text-emerald-700 hover:text-emerald-800">DevconnectTz</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
