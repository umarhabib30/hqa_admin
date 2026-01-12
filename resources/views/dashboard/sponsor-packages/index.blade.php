@extends('layouts.layout')
@section('content')

<div>

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
            Sponsor Packages
        </h1>

        <a href="{{ route('sponsor-packages.create') }}"
            class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-[#00285E]
                   text-[#00285E] font-semibold
                   hover:bg-[#00285E] hover:text-white
                   transition active:scale-95">
            + Add Package
        </a>
    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 text-sm text-gray-600">
                <tr>
                    <th class="p-4 text-left">Title</th>
                    <th class="p-4 text-right">Price/Year</th>
                    <th class="p-4 text-center">Benefits Count</th>
                    <th class="p-4 text-center">Created At</th>
                    <th class="p-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($packages as $package)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-medium">{{ $package->title }}</td>
                    <td class="p-4 text-right font-semibold text-[#00285E]">
                        ${{ number_format($package->price_per_year, 2) }}
                    </td>
                    <td class="p-4 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            {{ count($package->benefits ?? []) }} benefit(s)
                        </span>
                    </td>
                    <td class="p-4 text-center text-sm text-gray-600">
                        {{ $package->created_at->format('M d, Y') }}
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('sponsor-packages.show', $package->id) }}"
                                class="px-3 py-1 rounded border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition">
                                View
                            </a>
                            <a href="{{ route('sponsor-packages.edit', $package->id) }}"
                                class="px-3 py-1 rounded border border-[#00285E] text-[#00285E] hover:bg-[#00285E] hover:text-white transition">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('sponsor-packages.destroy', $package->id) }}" class="delete-form inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1 rounded border border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition delete-btn"
                                    data-id="{{ $package->id }}"
                                    data-name="{{ $package->title }}">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-500">
                        No sponsor packages found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MOBILE CARDS -->
    <div class="md:hidden space-y-4">
        @forelse($packages as $package)
        <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $package->title }}</h3>
                    <p class="text-lg font-bold text-[#00285E] mt-1">
                        ${{ number_format($package->price_per_year, 2) }}/year
                    </p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                    {{ count($package->benefits ?? []) }} benefit(s)
                </span>
            </div>

            <div class="text-sm text-gray-700">
                <p><strong>Created:</strong> {{ $package->created_at->format('M d, Y') }}</p>
            </div>

            <div class="flex flex-col gap-2 pt-2">
                <a href="{{ route('sponsor-packages.show', $package->id) }}"
                    class="w-full text-center px-4 py-2 rounded-lg border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition">
                    View Details
                </a>
                <a href="{{ route('sponsor-packages.edit', $package->id) }}"
                    class="w-full text-center px-4 py-2 rounded-lg border border-[#00285E] text-[#00285E] hover:bg-[#00285E] hover:text-white transition">
                    Edit
                </a>

                <form method="POST" action="{{ route('sponsor-packages.destroy', $package->id) }}" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full px-4 py-2 rounded-lg border border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition delete-btn"
                        data-id="{{ $package->id }}"
                        data-name="{{ $package->title }}">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-500 py-8">
            No sponsor packages found.
        </div>
        @endforelse
    </div>

</div>

@endsection

