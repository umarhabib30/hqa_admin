@extends('layouts.layout')
@section('content')

<div>

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
            Users
        </h1>

        <a href="{{ route('managers.create') }}"
            class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-[#00285E]
                   text-[#00285E] font-semibold
                   hover:bg-[#00285E] hover:text-white
                   transition active:scale-95">
            + Add User
        </a>
    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 text-sm text-gray-600">
                <tr>
                    <th class="p-4 text-left">Name</th>
                    <th class="p-4 text-left">Email</th>
                    <th class="p-4 text-center">Role</th>
                    <th class="p-4 text-center">Created At</th>
                    <th class="p-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($managers as $manager)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-medium">{{ $manager->name }}</td>
                    <td class="p-4">{{ $manager->email }}</td>
                    <td class="p-4 text-center">
                        @if($manager->role === 'super_admin')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                Super Admin
                            </span>
                        @elseif($manager->role === 'admin')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                Admin
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                Manager
                            </span>
                        @endif
                    </td>
                    <td class="p-4 text-center text-sm text-gray-600">
                        {{ $manager->created_at->format('M d, Y') }}
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('managers.edit', $manager->id) }}"
                                class="px-3 py-1 rounded border border-[#00285E] text-[#00285E] hover:bg-[#00285E] hover:text-white transition">
                                Edit
                            </a>

                            <a href="{{ route('managers.reset-password', $manager->id) }}"
                                class="px-3 py-1 rounded border border-yellow-600 text-yellow-600 hover:bg-yellow-600 hover:text-white transition">
                                Reset Password
                            </a>

                            <form method="POST" action="{{ route('managers.destroy', $manager->id) }}" class="delete-form inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1 rounded border border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition delete-btn"
                                    data-id="{{ $manager->id }}"
                                    data-name="{{ $manager->name }}">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-500">
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MOBILE CARDS -->
    <div class="md:hidden space-y-4">
        @forelse($managers as $manager)
        <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $manager->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $manager->email }}</p>
                </div>
                @if($manager->role === 'super_admin')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                        Super Admin
                    </span>
                @elseif($manager->role === 'admin')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                        Admin
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        Manager
                    </span>
                @endif
            </div>

            <div class="text-sm text-gray-700">
                <p><strong>Created:</strong> {{ $manager->created_at->format('M d, Y') }}</p>
            </div>

            <div class="flex flex-col gap-2 pt-2">
                <a href="{{ route('managers.edit', $manager->id) }}"
                    class="w-full text-center px-4 py-2 rounded-lg border border-[#00285E] text-[#00285E] hover:bg-[#00285E] hover:text-white transition">
                    Edit
                </a>

                <a href="{{ route('managers.reset-password', $manager->id) }}"
                    class="w-full text-center px-4 py-2 rounded-lg border border-yellow-600 text-yellow-600 hover:bg-yellow-600 hover:text-white transition">
                    Reset Password
                </a>

                <form method="POST" action="{{ route('managers.destroy', $manager->id) }}" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full px-4 py-2 rounded-lg border border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition delete-btn"
                        data-id="{{ $manager->id }}"
                        data-name="{{ $manager->name }}">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-500 py-8">
            No users found.
        </div>
        @endforelse
    </div>

</div>

@endsection

