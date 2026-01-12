@extends('layouts.layout')
@section('content')

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-6">Reset Password for {{ $manager->name }}</h2>

    <form method="POST" action="{{ route('managers.reset-password.update', $manager->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Current Email (Read-only) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input type="email" 
                   value="{{ $manager->email }}"
                   disabled
                   class="w-full px-4 py-3 border rounded-lg bg-gray-100 text-gray-600">
        </div>

        <!-- New Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password <span class="text-red-500">*</span></label>
            <input type="password" 
                   id="password" 
                   name="password" 
                   placeholder="Enter new password (min 8 characters)"
                   required
                   minlength="8"
                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('password') border-red-500 @enderror">
            @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password <span class="text-red-500">*</span></label>
            <input type="password" 
                   id="password_confirmation" 
                   name="password_confirmation" 
                   placeholder="Confirm new password"
                   required
                   minlength="8"
                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent">
        </div>

        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('managers.index') }}"
                class="px-6 py-3 border rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>

            <button type="submit"
                class="px-8 py-3 border-2 border-yellow-600 text-yellow-600 rounded-lg hover:bg-yellow-600 hover:text-white transition">
                Reset Password
            </button>
        </div>
    </form>
</div>

@endsection

