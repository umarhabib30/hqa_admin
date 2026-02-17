@extends('layouts.layout')
@section('content')

<div class="w-full">

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-[20px] md:text-[24px] font-medium text-gray-800">
            Permissions
        </h1>
        {{-- <p class="text-sm text-gray-600 mt-2">Set permissions for each user individually. Super Admin has access to all features and is not listed here.</p> --}}
    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- USERS LIST -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto p-4">
            <table id="permissionsTable" class="display w-full text-left" style="width:100%">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider font-bold">
                        <th class="px-4 py-3 border-b border-gray-200">Name</th>
                        <th class="px-4 py-3 border-b border-gray-200">Email</th>
                        <th class="px-4 py-3 border-b border-gray-200">Role</th>
                        <th class="px-4 py-3 border-b border-gray-200">Permissions</th>
                        <th class="px-4 py-3 border-b border-gray-200 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $u->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $u->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $u->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $u->role)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @php $count = $u->permissions()->count(); @endphp
                            {{ $count }} permission{{ $count !== 1 ? 's' : '' }} assigned
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('permissions.edit-user', $u) }}" class="text-[#00285E] hover:text-[#00285E]/80 font-medium">
                                Edit Permissions
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No users to manage (only Super Admin exists).</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <x-datatable-init table-id="permissionsTable" />
    @endpush

</div>

@endsection
