@extends('layouts.app')

@section('title', 'Add New User')
@section('page_title', 'Add New User')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('users.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Users
        </a>
    </div>

    <!-- Error Summary -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were {{ $errors->count() }} errors with your submission</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-900">User Setup</h3>
            <p class="text-sm text-gray-500 mt-1">Create a new user to access the administration dashboard.</p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Row 1: Basic Info -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-900 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="block w-full rounded-lg border-gray-300 px-4 py-3 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border transition-colors shadow-sm @error('name') border-red-300 @enderror"
                            placeholder="e.g. John Doe">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-900 mb-2">
                            Email address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="block w-full rounded-lg border-gray-300 px-4 py-3 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border transition-colors shadow-sm @error('email') border-red-300 @enderror"
                            placeholder="john@example.com">
                    </div>
                </div>

                <!-- Row 2: Roles and Password -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-900 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select id="role" name="role" required class="block w-full rounded-lg border-gray-300 px-4 py-3 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border transition-colors shadow-sm">
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Regular User</option>
                            <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-2">Super Admins can manage users, settings, and view all items. Regular users can only see their own items.</p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-900 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="password" required
                            class="block w-full rounded-lg border-gray-300 px-4 py-3 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border transition-colors shadow-sm @error('password') border-red-300 @enderror"
                            placeholder="Min 8 characters">
                    </div>
                </div>

            </div>

            <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end">
                <button type="submit" class="inline-flex justify-center items-center rounded-lg border border-transparent bg-indigo-600 px-6 py-3 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
