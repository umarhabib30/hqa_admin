@extends('layouts.layout')

@section('content')
    <div>

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
                PTO Event Attendees
            </h1>
        </div>

        <!-- DESKTOP TABLE -->
        <div class="hidden md:block bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full border-collapse">
                <thead class="bg-gray-100 text-sm text-gray-700">
                    <tr>
                        <th class="p-4 text-center">#</th>
                        <th class="p-4 text-center">Profile</th>
                        <th class="p-4 text-left">Name</th>
                        <th class="p-4 text-left">Email</th>
                        <th class="p-4 text-left">Phone</th>
                        <th class="p-4 text-center">Will Attend</th>
                        <th class="p-4 text-center">Guests</th>
                        <th class="p-4 text-center">Created</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($attendees as $attendee)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-center">{{ $loop->iteration }}</td>

                            <td class="p-4 text-center">
                                @if ($attendee->profile_pic)
                                    <img src="{{ asset('storage/' . $attendee->profile_pic) }}"
                                        class="w-12 h-12 rounded-full object-cover mx-auto border">
                                @else
                                    <div
                                        class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500 mx-auto">
                                        N/A
                                    </div>
                                @endif
                            </td>

                            <td class="p-4 font-medium">{{ $attendee->first_name }} {{ $attendee->last_name }}</td>
                            <td class="p-4">{{ $attendee->email }}</td>
                            <td class="p-4">{{ $attendee->phone }}</td>

                            <td class="p-4 text-center">
                                @if ($attendee->will_attend)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Yes</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">No</span>
                                @endif
                            </td>

                            <td class="p-4 text-center">{{ $attendee->number_of_guests }}</td>
                            <td class="p-4 text-center">{{ $attendee->created_at->format('d M Y') }}</td>

                            <td class="p-4 text-center">
                                <form action="{{ route('admin.pto-event-attendees.destroy', $attendee->id) }}"
                                    method="POST" class="flex justify-center gap-2"
                                    onsubmit="return confirm('Delete this attendee?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1 rounded border border-red-500 text-red-600 hover:bg-red-500 hover:text-white transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-6 text-center text-gray-500">No attendees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MOBILE CARDS -->
        <div class="md:hidden space-y-4">
            @forelse($attendees as $attendee)
                <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">
                    <div class="flex gap-3 items-center">
                        @if ($attendee->profile_pic)
                            <img src="{{ asset('storage/' . $attendee->profile_pic) }}"
                                class="w-14 h-14 rounded-full object-cover border">
                        @else
                            <div
                                class="w-14 h-14 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500">
                                N/A
                            </div>
                        @endif

                        <div>
                            <h3 class="font-semibold text-gray-800">
                                {{ $attendee->first_name }} {{ $attendee->last_name }}
                            </h3>
                            <p class="text-sm text-gray-600">{{ $attendee->email }}</p>
                            <p class="text-sm text-gray-600">{{ $attendee->phone }}</p>
                            {{-- <p class="text-sm">
                                Will Attend:
                                @if ($attendee->will_attend)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Yes</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">No</span>
                                @endif
                            </p> --}}
                            <p class="text-sm">Guests: {{ $attendee->number_of_guests }}</p>
                            <p class="text-sm text-gray-500">Created: {{ $attendee->created_at->format('d M Y') }}</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.pto-event-attendees.destroy', $attendee->id) }}" method="POST"
                        class="flex gap-2" onsubmit="return confirm('Delete this attendee?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="flex-1 w-full px-4 py-2 rounded-lg border border-red-500 text-red-600 hover:bg-red-500 hover:text-white transition active:scale-95">
                            Delete
                        </button>
                    </form>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    No attendees found.
                </div>
            @endforelse
        </div>

    </div>
@endsection
