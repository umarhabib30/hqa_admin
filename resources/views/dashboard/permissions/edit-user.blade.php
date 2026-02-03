@extends('layouts.layout')
@section('content')

<div class="max-w-6xl mx-auto">

    <!-- HEADER -->
    <div class="mb-6">
        <a href="{{ route('permissions.index') }}" class="text-sm text-[#00285E] hover:underline mb-2 inline-block">← Back to User Permissions</a>
        <h1 class="text-[20px] md:text-[24px] font-medium text-gray-800">
            Edit Permissions: {{ $user->name }}
        </h1>
        <p class="text-sm text-gray-600 mt-2">Set which permissions this user can access. Role: <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span></p>
    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('permissions.update-user', $user) }}" class="bg-white rounded-xl shadow overflow-hidden">
        @csrf
        @method('PUT')

        @php
            $permissionsByGroup = $permissions->groupBy('group');
        @endphp

        @foreach($permissionsByGroup as $group => $groupPermissions)
        <div class="p-6 border-b last:border-b-0">
            <h3 class="text-base font-medium text-gray-800 mb-3">{{ $group ?? 'General' }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($groupPermissions as $permission)
                <label class="flex items-start gap-2.5 p-2.5 rounded-lg border cursor-pointer hover:bg-gray-50 transition
                              {{ in_array($permission->id, $userPermissionIds) ? 'border-[#00285E] bg-blue-50' : 'border-gray-200' }}">
                    <input type="checkbox"
                           name="permissions[]"
                           value="{{ $permission->id }}"
                           {{ in_array($permission->id, $userPermissionIds) ? 'checked' : '' }}
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

        <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-4">
            <a href="{{ route('permissions.index') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-100 transition font-medium">
                Cancel
            </a>
            <button type="submit"
                    class="px-5 py-2.5 bg-[#00285E] text-white text-sm rounded-lg hover:bg-[#00285E]/90 transition font-medium">
                Save Permissions
            </button>
        </div>
    </form>

</div>

@endsection
