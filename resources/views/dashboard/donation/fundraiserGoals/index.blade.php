@extends('layouts.layout')
@section('content')

<div>

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
                FundRaise Goals
            </h1>

            <div class="text-sm text-gray-500 mt-1">
                Dashboard / FundRaise Goals
            </div>
        </div>

        <a href="{{ route('fundRaise.create') }}"
            class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-[#00285E]
                   text-[#00285E] font-semibold text-lg
                   hover:bg-[#00285E] hover:text-white
                   transition-all duration-300
                   active:scale-95">
            Create
        </a>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block bg-white rounded-xl shadow-sm overflow-hidden">

        <table class="w-full">
            <thead class="bg-gray-100 text-sm text-gray-600">
                <tr>
                    <th class="px-6 py-4 text-left">ID</th>
                    <th class="px-6 py-4 text-left">Goal Name</th>
                    <th class="px-6 py-4 text-left">Start Date</th>
                    <th class="px-6 py-4 text-left">End Date</th>
                    <th class="px-6 py-4 text-left">Starting Goal</th>
                    <th class="px-6 py-4 text-left">Ending Goal</th>
                    <th class="px-6 py-4 text-left">Total Collected</th>
                    <th class="px-6 py-4 text-left">Total Donors</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($fundRaises as $fund)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">{{ $fund->id }}</td>
                    <td class="px-6 py-4">{{ $fund->goal_name ?: '—' }}</td>
                    <td class="px-6 py-4">{{ $fund->start_date ? $fund->start_date->format('M d, Y') : '—' }}</td>
                    <td class="px-6 py-4">{{ $fund->end_date ? $fund->end_date->format('M d, Y') : '—' }}</td>
                    <td class="px-6 py-4">{{ $fund->starting_goal !== null ? ('Rs ' . number_format($fund->starting_goal)) : '—' }}</td>
                    <td class="px-6 py-4">{{ $fund->ending_goal !== null ? ('Rs ' . number_format($fund->ending_goal)) : '—' }}</td>
                    <td class="px-6 py-4">
                        {{ 'Rs ' . number_format((float) ($fund->collected_amount ?? 0), 2) }}
                    </td>
                    <td class="px-6 py-4">{{ $fund->donors_count ?? 0 }}</td>

                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('fundRaise.edit', $fund->id) }}"
                                class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('fundRaise.destroy', $fund->id) }}" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="delete-btn px-4 py-2 rounded-lg
                                           border border-red-500 text-red-500
                                           hover:bg-red-500 hover:text-white
                                           transition active:scale-95"
                                    data-name="{{ $fund->goal_name ?: ('Fund #' . $fund->id) }}">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                        No fund raise goals found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <!-- MOBILE CARDS -->
    <div class="md:hidden space-y-4">

        @forelse($fundRaises as $fund)
        <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">

            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">
                    Fund #{{ $fund->id }}
                </h3>

                <div class="text-right">
                    <div class="text-sm font-medium text-gray-600">
                        Donors: {{ $fund->donors_count ?? 0 }}
                    </div>
                    <div class="text-xs text-gray-500">
                        Collected: {{ 'Rs ' . number_format((float) ($fund->collected_amount ?? 0), 2) }}
                    </div>
                </div>
            </div>

            <div class="text-sm text-gray-700 space-y-1">
                <p><strong>Goal Name:</strong> {{ $fund->goal_name ?: '—' }}</p>
                <p><strong>Start Date:</strong> {{ $fund->start_date ? $fund->start_date->format('M d, Y') : '—' }}</p>
                <p><strong>End Date:</strong> {{ $fund->end_date ? $fund->end_date->format('M d, Y') : '—' }}</p>
                <p><strong>Starting:</strong> {{ $fund->starting_goal !== null ? ('Rs ' . number_format($fund->starting_goal)) : '—' }}</p>
                <p><strong>Ending:</strong> {{ $fund->ending_goal !== null ? ('Rs ' . number_format($fund->ending_goal)) : '—' }}</p>
            </div>

            <div class="flex gap-2 pt-2">
                <a href="{{ route('fundRaise.edit', $fund->id) }}"
                    class="flex-1 text-center px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100">
                    Edit
                </a>

                <form method="POST" action="{{ route('fundRaise.destroy', $fund->id) }}" class="delete-form flex-1">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="delete-btn w-full px-4 py-2 rounded-lg
                               border border-red-500 text-red-500
                               hover:bg-red-500 hover:text-white
                               transition active:scale-95"
                        data-name="{{ $fund->goal_name ?: ('Fund #' . $fund->id) }}">
                        Delete
                    </button>
                </form>
            </div>

        </div>
        @empty
        <div class="text-center text-gray-500 py-8">
            No fund raise goals found.
        </div>
        @endforelse

    </div>

</div>

@endsection