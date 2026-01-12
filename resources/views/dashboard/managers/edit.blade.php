@extends('layouts.layout')
@section('content')

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-6">Edit User</h2>

    <form method="POST" action="{{ route('managers.update', $manager->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name', $manager->name) }}"
                   placeholder="Enter user name"
                   required
                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('name') border-red-500 @enderror">
            @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email', $manager->email) }}"
                   placeholder="Enter email address"
                   required
                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('email') border-red-500 @enderror">
            @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role -->
        <div>
            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
            <select id="role" 
                    name="role" 
                    required
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('role') border-red-500 @enderror">
                <option value="manager" {{ old('role', $manager->role) === 'manager' ? 'selected' : '' }}>Manager</option>
                <option value="admin" {{ old('role', $manager->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                @if(auth()->user()->isSuperAdmin())
                <option value="super_admin" {{ old('role', $manager->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                @endif
            </select>
            @error('role')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('managers.index') }}"
                class="px-6 py-3 border rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>

            <button type="submit"
                class="px-8 py-3 border-2 border-[#00285E] text-[#00285E] rounded-lg hover:bg-[#00285E] hover:text-white transition">
                Update User
            </button>
        </div>
    </form>
</div>

@endsection

