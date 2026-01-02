@extends('layouts.layout')

@section('content')

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Fee Per Person</h1>

    <a href="{{ route('fee.create') }}"
        class="px-5 py-2 bg-[#00285E] text-white rounded-lg
               hover:bg-[#001c42] transition w-fit">
        + Add Fee
    </a>
</div>

{{-- ================= DESKTOP / TABLET TABLE ================= --}}
<div class="hidden md:block bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-3 text-left">Title</th>
                <th class="px-4 py-3 text-center">Price</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($fees as $fee)
            <tr class="border-t hover:bg-gray-50 transition">
                <td class="px-4 py-3">{{ $fee->title }}</td>

                <td class="px-4 py-3 text-center font-semibold">
                    ${{ number_format($fee->price, 2) }}
                </td>

                <td class="px-4 py-3 text-center">
                    @if($fee->is_active)
                    <span class="px-3 py-1 text-xs rounded-full
                                     bg-green-100 text-green-700">
                        Active
                    </span>
                    @else
                    <span class="px-3 py-1 text-xs rounded-full
                                     bg-gray-200 text-gray-600">
                        Inactive
                    </span>
                    @endif
                </td>

                <td class="px-4 py-3 text-center flex gap-2 justify-center">
                    <a href="{{ route('fee.edit', $fee) }}"
                        class="px-3 py-1 border border-blue-500 text-blue-500 rounded
                               hover:bg-blue-500 hover:text-white transition">
                        Edit
                    </a>

                    <form method="POST"
                        action="{{ route('fee.destroy', $fee) }}">
                        @csrf @method('DELETE')
                        <button
                            onclick="return confirm('Delete this fee?')"
                            class="px-3 py-1 border border-red-500 text-red-500 rounded
                                   hover:bg-red-500 hover:text-white transition">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4"
                    class="px-4 py-6 text-center text-gray-500">
                    No fee records found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ================= MOBILE CARDS ================= --}}
<div class="md:hidden space-y-4">
    @forelse($fees as $fee)
    <div class="bg-white rounded-xl shadow p-4 space-y-3">
        <div>
            <p class="text-xs text-gray-500">Title</p>
            <p class="font-semibold">{{ $fee->title }}</p>
        </div>

        <div>
            <p class="text-xs text-gray-500">Price</p>
            <p class="font-semibold">
                ${{ number_format($fee->price, 2) }}
            </p>
        </div>

        <div>
            <p class="text-xs text-gray-500">Status</p>
            @if($fee->is_active)
            <span class="inline-block px-3 py-1 text-xs rounded-full
                             bg-green-100 text-green-700">
                Active
            </span>
            @else
            <span class="inline-block px-3 py-1 text-xs rounded-full
                             bg-gray-200 text-gray-600">
                Inactive
            </span>
            @endif
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('fee.edit', $fee) }}"
                class="flex-1 text-center px-3 py-2
                       border border-blue-500 text-blue-500 rounded
                       hover:bg-blue-500 hover:text-white transition">
                Edit
            </a>

            <form method="POST"
                action="{{ route('fee.destroy', $fee) }}"
                class="flex-1">
                @csrf @method('DELETE')
                <button
                    onclick="return confirm('Delete this fee?')"
                    class="w-full px-3 py-2
                           border border-red-500 text-red-500 rounded
                           hover:bg-red-500 hover:text-white transition">
                    Delete
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="text-center text-gray-500">
        No fee records found
    </div>
    @endforelse
</div>

@endsection