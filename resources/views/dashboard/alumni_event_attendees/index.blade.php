@extends('layouts.layout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Alumni Event Attendees</h1>

            <form method="GET" action="{{ route('admin.alumni-event-attendees.index') }}" class="flex gap-2 items-center">
                <label for="event_id" class="text-gray-700 font-medium text-sm">Filter by Event:</label>
                <select name="event_id" id="event_id" onchange="this.form.submit()"
                    class="border p-2 rounded text-sm bg-white shadow-sm">
                    <option value="">All Events</option>
                    @foreach ($events as $event)
                        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                            {{ $event->title }} ({{ $attendeeCounts[$event->id] ?? 0 }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded shadow-sm border-l-4 border-green-500">
                {{ session('success') }}
            </div>
        @endif

        <!-- DESKTOP TABLE -->
        <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto p-4">
                <table id="alumniAttendeesTable" class="display w-full text-left" style="width:100%">
                    <thead>
                        <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider font-bold">
                            <th class="px-4 py-3 border-b border-gray-200">#</th>
                            <th class="px-4 py-3 border-b border-gray-200">Profile</th>
                            <th class="px-4 py-3 border-b border-gray-200">Attendee Details</th>
                            <th class="px-4 py-3 border-b border-gray-200">Contact Info</th>
                            <th class="px-4 py-3 border-b border-gray-200">Guests</th>
                            <th class="px-4 py-3 border-b border-gray-200">Payment Status</th>
                            <th class="px-4 py-3 border-b border-gray-200">Event Name</th>
                            <th class="px-4 py-3 border-b border-gray-200">Registered</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($attendees as $attendee)
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="p-4 text-center text-gray-400">{{ $loop->iteration }}</td>

                            <td class="p-4 text-center">
                                @if ($attendee->profile_pic)
                                    <img src="{{ asset('storage/' . $attendee->profile_pic) }}"
                                        class="w-10 h-10 rounded-full object-cover mx-auto border shadow-sm">
                                @else
                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-[10px] text-gray-400 mx-auto">
                                        N/A
                                    </div>
                                @endif
                            </td>

                            <td class="p-4">
                                <div class="font-bold text-gray-900">{{ $attendee->first_name }} {{ $attendee->last_name }}
                                </div>
                                <div class="text-xs text-gray-500 truncate">{{ $attendee->email }}</div>
                            </td>

                            <td class="p-4 text-gray-600">
                                <div class="flex items-center gap-1">{{ $attendee->phone }}</div>
                            </td>

                            <td class="p-4 text-center">
                                <span
                                    class="bg-gray-100 px-2.5 py-0.5 rounded-full font-medium">{{ $attendee->number_of_guests }}</span>
                            </td>

                            <td class="p-4">
                                <div class="text-green-700 font-bold">${{ number_format($attendee->amount, 2) }}</div>
                                <div class="text-[10px] text-gray-400 font-mono tracking-tighter"
                                    title="{{ $attendee->payment_id }}">
                                    ID: {{ \Illuminate\Support\Str::limit($attendee->payment_id, 15) }}
                                </div>
                            </td>

                            <td class="p-4">
                                <span class="text-blue-900 font-medium">{{ $attendee->event->title ?? 'N/A' }}</span>
                            </td>

                            <td class="p-4 text-center text-xs text-gray-500">
                                {{ $attendee->created_at->format('M d, Y') }}
                            </td>

                            <td class="p-4 text-center">
                                <form action="{{ route('admin.alumni-event-attendees.destroy', $attendee->id) }}"
                                    method="POST" onsubmit="return confirm('Delete this attendee record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-12 text-center text-gray-400 italic">No attendees found in records.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @push('scripts')
        <x-datatable-init table-id="alumniAttendeesTable" />
        @endpush

        <!-- MOBILE CARDS -->
        <div class="lg:hidden space-y-4">
            @forelse($attendees as $attendee)
                <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-[#092962]">
                    <div class="flex justify-between items-start border-b pb-3 mb-3">
                        <div class="flex gap-3 items-center">
                            @if ($attendee->profile_pic)
                                <img src="{{ asset('storage/' . $attendee->profile_pic) }}"
                                    class="w-12 h-12 rounded-full object-cover border">
                            @endif
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $attendee->first_name }} {{ $attendee->last_name }}
                                </h3>
                                <p class="text-xs text-gray-500">Event: {{ $attendee->event->title ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-bold">
                            Paid: ${{ number_format($attendee->amount, 2) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-y-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-400">Email</p>
                            <p class="text-gray-700 truncate pr-2">{{ $attendee->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Phone</p>
                            <p class="text-gray-700">{{ $attendee->phone }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Guests</p>
                            <p class="text-gray-700">{{ $attendee->number_of_guests }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Payment ID</p>
                            <p class="text-gray-700 font-mono text-[10px] break-all">{{ $attendee->payment_id }}</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t flex gap-2">
                        <form action="{{ route('admin.alumni-event-attendees.destroy', $attendee->id) }}" method="POST"
                            class="w-full">
                            @csrf @method('DELETE')
                            <button
                                class="w-full py-2 bg-red-50 text-red-600 rounded-lg text-xs font-bold hover:bg-red-100">
                                DELETE ATTENDEE
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-12 bg-white rounded-xl shadow-sm border border-dashed">
                    No attendees found.
                </div>
            @endforelse
        </div>
    </div>
@endsection
