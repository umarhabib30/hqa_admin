@extends('layouts.layout')
@section('content')

<div>

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
            Calendar Events
        </h1>

        <a href="{{ route('calender.create') }}"
            class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-[#00285E]
                   text-[#00285E] font-semibold
                   hover:bg-[#00285E] hover:text-white
                   transition active:scale-95">
            + Add Event
        </a>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block bg-white rounded-xl shadow overflow-hidden">

        <table class="w-full">
            <thead class="bg-gray-100 text-sm text-gray-600">
                <tr>
                    <th class="p-4 text-left">Title</th>
                    <th class="p-4 text-center">Category</th>
                    <th class="p-4 text-center">Date</th>
                    <th class="p-4 text-center">Location</th>
                    <th class="p-4 text-center">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($events as $event)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-medium">{{ $event->title }}</td>

                    <td class="p-4 text-center">
                        {{ $event->category }}
                    </td>

                    <td class="p-4 text-center">
                        {{ $event->start_date }}
                        @if($event->end_date)
                        - {{ $event->end_date }}
                        @endif
                    </td>

                    <td class="p-4 text-center">
                        {{ $event->location }}
                    </td>

                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('calender.edit',$event->id) }}"
                                class="px-3 py-1 rounded
                                       border border-[#00285E]
                                       text-[#00285E]
                                       hover:bg-[#00285E] hover:text-white transition">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('calender.destroy',$event->id) }}">
                                @csrf
                                @method('DELETE')
                                <button
                                    onclick="return confirm('Delete event?')"
                                    class="px-3 py-1 rounded
                                           border border-red-600
                                           text-red-600
                                           hover:bg-red-600 hover:text-white transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-500">
                        No calendar events found.
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
                    {{ $event->category }}
                </span>
            </div>

            <div class="text-sm text-gray-700 space-y-1">
                <p>
                    <strong>Date:</strong>
                    {{ $event->start_date }}
                    @if($event->end_date)
                    - {{ $event->end_date }}
                    @endif
                </p>

                <p>
                    <strong>Location:</strong> {{ $event->location }}
                </p>
            </div>

            <div class="flex gap-2 pt-2">
                <a href="{{ route('calender.edit',$event->id) }}"
                    class="flex-1 text-center px-4 py-2 rounded-lg
                           border border-[#00285E]
                           text-[#00285E]
                           hover:bg-[#00285E] hover:text-white transition">
                    Edit
                </a>

                <form method="POST"
                    action="{{ route('calender.destroy',$event->id) }}"
                    class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button
                        onclick="return confirm('Delete event?')"
                        class="w-full px-4 py-2 rounded-lg
                               border border-red-600
                               text-red-600
                               hover:bg-red-600 hover:text-white
                               transition active:scale-95">
                        Delete
                    </button>
                </form>
            </div>

        </div>
        @empty
        <div class="text-center text-gray-500 py-8">
            No calendar events found.
        </div>
        @endforelse

    </div>

</div>

@endsection