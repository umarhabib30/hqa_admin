@extends('layouts.layout')
@section('content')

<div class="max-w-6xl mx-auto">

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-[20px] md:text-[24px] font-medium text-gray-800">
            Role Permissions Management
        </h1>
        <p class="text-sm text-gray-600 mt-2">Set permissions for Admin and Manager roles. Super Admin has access to all features.</p>
    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- PERMISSIONS FOR EACH ROLE -->
    <div class="space-y-6">
        @foreach(['admin', 'manager'] as $role)
        <div x-data="{ open: {{ session('openRole') === $role ? 'true' : 'false' }} }" class="bg-white rounded-xl shadow overflow-hidden">
            <!-- ROLE HEADER (click to expand/collapse) -->
            <button type="button"
                    @click="open = !open"
                    class="w-full flex items-center justify-between bg-gradient-to-r {{ $role === 'admin' ? 'from-purple-50 to-purple-100' : 'from-blue-50 to-blue-100' }} p-5 border-b">
                <span class="flex items-center gap-3 text-lg font-semibold text-gray-800 capitalize">
                    <span class="px-3 py-1.5 rounded-lg text-sm {{ $role === 'admin' ? 'bg-purple-200 text-purple-800' : 'bg-blue-200 text-blue-800' }}">
                        {{ ucfirst($role) }} Permissions
                    </span>
                </span>
                <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-gray-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- PERMISSIONS FORM (collapsible) -->
            <form method="POST" action="{{ route('permissions.update-role') }}" class="p-6" x-show="open" x-transition x-cloak>
                @csrf
                <input type="hidden" name="role" value="{{ $role }}">

                @php
                    $permissionsByGroup = $permissions->groupBy('group');
                    $selectedPermissions = isset($rolePermissions[$role]) ? $rolePermissions[$role]->pluck('permission_id')->toArray() : [];
                @endphp

                @foreach($permissionsByGroup as $group => $groupPermissions)
                <div class="mb-5 pb-5 border-b last:border-b-0">
                    <h3 class="text-base font-medium text-gray-800 mb-3">{{ $group ?? 'General' }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($groupPermissions as $permission)
                        <label class="flex items-start gap-2.5 p-2.5 rounded-lg border cursor-pointer hover:bg-gray-50 transition
                                      {{ in_array($permission->id, $selectedPermissions) ? 'border-[#00285E] bg-blue-50' : 'border-gray-200' }}">
                            <input type="checkbox" 
                                   name="permissions[]" 
                                   value="{{ $permission->id }}"
                                   {{ in_array($permission->id, $selectedPermissions) ? 'checked' : '' }}
                                   class="mt-0.5 w-4 h-4 text-[#00285E] border-gray-300 rounded focus:ring-[#00285E]">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ $permission->display_name }}</p>
                                @if($permission->description)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $permission->description }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $permission->name }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="flex justify-end gap-4 pt-4 border-t">
                    <button type="submit"
                        class="px-5 py-2.5 bg-[#00285E] text-white text-sm rounded-lg hover:bg-[#00285E]/90 transition font-medium">
                        Save {{ ucfirst($role) }} Permissions
                    </button>
                </div>
            </form>
        </div>
        @endforeach
    </div>

</div>

@endsection

