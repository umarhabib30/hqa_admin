@extends('layouts.layout')
@section('content')
    <div>

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
                Alumni Events
            </h1>

            <a href="{{ route('alumniEvent.create') }}"
                class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-[#00285E]
                   text-[#00285E] font-semibold
                   hover:bg-[#00285E] hover:text-white
                   transition-all duration-300
                   active:scale-95">
                Create Event
            </a>
        </div>

        <!-- DESKTOP TABLE -->
        <div class="hidden md:block bg-white rounded-xl shadow overflow-hidden">

            <table class="w-full text-sm">
                <thead class="bg-gray-100 text-gray-600">
                    <tr>
                        <th class="p-4 text-left">Title</th>
                        <th class="p-4 text-center">Date</th>
                        <th class="p-4 text-center">Location</th>
                        <th class="p-4 text-center">Event Image</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-medium">{{ $event->title }}</td>

                            <td class="p-4 text-center">
                                {{ $event->start_date }}
                            </td>

                            <td class="p-4 text-center">
                                {{ $event->location }}
                            </td>

                            <td class="p-4 text-center">
                                @if ($event->event_image)
                                    <img src="{{ asset('storage/' . $event->event_image) }}" alt="Event Image"
                                        class="w-16 h-16 object-cover rounded-lg mx-auto">
                                @else
                                    <span class="text-gray-400 text-sm">No Image</span>
                                @endif
                            </td>

                            <td class="p-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('alumniEvent.edit', $event->id) }}"
                                        class="px-3 py-1 border border-yellow-500 text-yellow-600 rounded hover:bg-yellow-50">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('alumniEvent.destroy', $event->id) }}"
                                        onsubmit="return confirm('Delete this event?')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="px-3 py-1 border border-red-500 text-red-600 rounded
                                           hover:bg-red-500 hover:text-white transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500">
                                No events found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

        <!-- MOBILE CARDS -->
        <div class="md:hidden space-y-4">

            @forelse($events as $event)
                <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">

                    <div class="flex justify-between items-start">
                        <h3 class="font-semibold text-gray-800">
                            {{ $event->title }}
                        </h3>

                        <span class="text-sm text-gray-500">
                            {{ $event->start_date }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-700">
                        <strong>Location:</strong> {{ $event->location }}
                    </div>

                    <div class="pt-2">
                        @if ($event->event_image)
                            <img src="{{ asset('storage/' . $event->event_image) }}" alt="Event Image"
                                class="w-full h-40 object-cover rounded-lg">
                        @else
                            <div class="text-sm text-gray-400 text-center py-6 border rounded-lg">
                                No Image
                            </div>
                        @endif
                    </div>

                    <div class="flex gap-2 pt-2">
                        <a href="{{ route('alumniEvent.edit', $event->id) }}"
                            class="flex-1 text-center px-4 py-2 rounded-lg
                           border border-yellow-500 text-yellow-600
                           hover:bg-yellow-50">
                            Edit
                        </a>

                        <form method="POST" action="{{ route('alumniEvent.destroy', $event->id) }}"
                            onsubmit="return confirm('Delete this event?')" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button
                                class="w-full px-4 py-2 rounded-lg
                               border border-red-500 text-red-600
                               hover:bg-red-500 hover:text-white
                               transition active:scale-95">
                                Delete
                            </button>
                        </form>
                    </div>

                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    No events found.
                </div>
            @endforelse

        </div>

    </div>
@endsection
