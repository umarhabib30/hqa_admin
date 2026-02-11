@extends('layouts.layout')

@section('content')

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold">Job Applications</h1>

        {{-- <a href="{{ route('jobApp.create') }}" class="px-5 py-2 bg-[#00285E] text-white rounded-lg
                   hover:bg-[#001c42] transition w-fit">
            + New Application
        </a> --}}
    </div>

    {{-- ===================== DESKTOP / TABLET TABLE ===================== --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto p-4">
            <table id="jobAppTable" class="display w-full text-left" style="width:100%">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider font-bold">
                        <th class="px-4 py-3 border-b border-gray-200">Name</th>
                        <th class="px-4 py-3 border-b border-gray-200">Email</th>
                        <th class="px-4 py-3 border-b border-gray-200">Experience</th>
                        <th class="px-4 py-3 border-b border-gray-200">CV</th>
                        <th class="px-4 py-3 border-b border-gray-200 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($applications as $app)
                    <tr class="border-t hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            {{ $app->first_name }} {{ $app->last_name }}
                        </td>

                        <td class="px-4 py-3">{{ $app->email }}</td>

                        <td class="px-4 py-3 text-center">
                            {{ $app->years_experience }} yrs
                        </td>

                        <td class="px-4 py-3 text-center">
                            <a href="{{ asset('storage/' . $app->cv_path) }}" target="_blank" class="text-blue-600 underline">
                                View CV
                            </a>
                        </td>

                        <td class="px-4 py-3 text-center flex gap-3 justify-center">
                            <a href="{{ route('jobApp.edit', $app) }}" class="px-3 py-1 border border-blue-500 text-blue-500 rounded
                                       hover:bg-blue-500 hover:text-white transition">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('jobApp.destroy', $app) }}">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Delete this application?')" class="px-3 py-1 border border-red-500 text-red-500 rounded
                                           hover:bg-red-500 hover:text-white transition">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            No applications found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <x-datatable-init table-id="jobAppTable" />
    @endpush

    {{-- ===================== MOBILE CARDS ===================== --}}
    <div class="md:hidden space-y-4">
        @forelse($applications as $app)
            <div class="bg-white rounded-xl shadow p-4 space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Name</p>
                    <p class="font-semibold">
                        {{ $app->first_name }} {{ $app->last_name }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="break-all">{{ $app->email }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Experience</p>
                    <p>{{ $app->years_experience }} yrs</p>
                </div>

                <div>
                    <a href="{{ asset('storage/' . $app->cv_path) }}" target="_blank"
                        class="inline-block text-blue-600 underline">
                        View CV
                    </a>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('jobApp.edit', $app) }}" class="flex-1 text-center px-3 py-2
                               border border-blue-500 text-blue-500 rounded
                               hover:bg-blue-500 hover:text-white transition">
                        Edit
                    </a>

                    <form method="POST" action="{{ route('jobApp.destroy', $app) }}" class="flex-1">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete this application?')" class="w-full px-3 py-2
                                   border border-red-500 text-red-500 rounded
                                   hover:bg-red-500 hover:text-white transition">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500">
                No applications found
            </div>
        @endforelse
    </div>

@endsection