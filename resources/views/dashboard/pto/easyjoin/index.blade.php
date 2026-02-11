@extends('layouts.layout')

@section('content')

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Easy Join Records</h1>

    <a href="{{ route('easy-joins.create') }}"
        class="px-5 py-2 bg-[#00285E] text-white rounded-lg
               hover:bg-[#001c42] transition w-fit">
        + Add New
    </a>
</div>

{{-- ================= DESKTOP / TABLET ================= --}}
<div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto p-4">
        <table id="easyjoinTable" class="display w-full text-left" style="width:100%">
            <thead>
                <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider font-bold">
                    <th class="px-4 py-3 border-b border-gray-200">Name</th>
                    <th class="px-4 py-3 border-b border-gray-200">Email</th>
                    <th class="px-4 py-3 border-b border-gray-200">Guests</th>
                    <th class="px-4 py-3 border-b border-gray-200">Fee / Person</th>
                    <th class="px-4 py-3 border-b border-gray-200">Total</th>
                    <th class="px-4 py-3 border-b border-gray-200 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($joins as $join)
            <tr class="border-t hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                    {{ $join->first_name }} {{ $join->last_name }}
                </td>

                <td class="px-4 py-3">{{ $join->email }}</td>

                <td class="px-4 py-3 text-center">
                    {{ $join->guest_count }}
                </td>

                <td class="px-4 py-3 text-center">
                    {{ number_format($join->fee_per_person,2) }}
                </td>

                <td class="px-4 py-3 text-center font-semibold">
                    {{ number_format($join->total_fee,2) }}
                </td>

                <td class="px-4 py-3 text-center flex gap-2 justify-center">
                    <a href="{{ route('easy-joins.edit', $join) }}"
                        class="px-3 py-1 border border-blue-500 text-blue-500 rounded
                               hover:bg-blue-500 hover:text-white transition">
                        Edit
                    </a>

                    <form method="POST"
                        action="{{ route('easy-joins.destroy', $join) }}">
                        @csrf @method('DELETE')
                        <button
                            onclick="return confirm('Delete this record?')"
                            class="px-3 py-1 border border-red-500 text-red-500 rounded
                                   hover:bg-red-500 hover:text-white transition">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6"
                    class="px-4 py-6 text-center text-gray-500">
                    No records found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@push('scripts')
<x-datatable-init table-id="easyjoinTable" />
@endpush

{{-- ================= MOBILE CARDS ================= --}}
<div class="md:hidden space-y-4">
    @forelse($joins as $join)
    <div class="bg-white rounded-xl shadow p-4 space-y-3">
        <div>
            <p class="text-xs text-gray-500">Name</p>
            <p class="font-semibold">
                {{ $join->first_name }} {{ $join->last_name }}
            </p>
        </div>

        <div>
            <p class="text-xs text-gray-500">Email</p>
            <p class="break-all">{{ $join->email }}</p>
        </div>

        <div class="flex justify-between">
            <div>
                <p class="text-xs text-gray-500">Guests</p>
                <p>{{ $join->guest_count }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-500">Fee / Person</p>
                <p>{{ number_format($join->fee_per_person,2) }}</p>
            </div>
        </div>

        <div>
            <p class="text-xs text-gray-500">Total</p>
            <p class="font-semibold">
                {{ number_format($join->total_fee,2) }}
            </p>
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('easy-joins.edit', $join) }}"
                class="flex-1 text-center px-3 py-2
                       border border-blue-500 text-blue-500 rounded
                       hover:bg-blue-500 hover:text-white transition">
                Edit
            </a>

            <form method="POST"
                action="{{ route('easy-joins.destroy', $join) }}"
                class="flex-1">
                @csrf @method('DELETE')
                <button
                    onclick="return confirm('Delete this record?')"
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
        No records found
    </div>
    @endforelse
</div>

@endsection