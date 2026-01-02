@extends('layouts.layout')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">
            Update PTO Event
        </h2>

        <a href="{{ route('ptoEvents.index') }}"
            class="px-5 py-2 rounded-lg
                  border border-gray-300
                  text-gray-600
                  hover:bg-gray-100 transition">
            Back
        </a>
    </div>

    <form method="POST"
        action="{{ route('ptoEvents.update', $event->id) }}"
        enctype="multipart/form-data"
        class="space-y-6">
        @csrf
        @method('PUT')

        <!-- TITLE -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Event Title
            </label>
            <input type="text"
                name="title"
                value="{{ old('title', $event->title) }}"
                class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none">
        </div>

        <!-- DESCRIPTION -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Event Description
            </label>
            <textarea name="description"
                rows="4"
                class="w-full px-4 py-3 rounded-lg
                             border border-gray-300
                             focus:ring-2 focus:ring-[#00285E]
                             focus:outline-none">{{ old('description', $event->description) }}</textarea>
        </div>

        <!-- DATES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Start Date
                </label>
                <input type="date"
                    name="start_date"
                    value="{{ old('start_date', $event->start_date) }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    End Date
                </label>
                <input type="date"
                    name="end_date"
                    value="{{ old('end_date', $event->end_date) }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300">
            </div>
        </div>

        <!-- TIMES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Start Time
                </label>
                <input type="time"
                    name="start_time"
                    value="{{ old('start_time', $event->start_time) }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    End Time
                </label>
                <input type="time"
                    name="end_time"
                    value="{{ old('end_time', $event->end_time) }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300">
            </div>
        </div>

        <!-- LOCATION -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Location
            </label>
            <input type="text"
                name="location"
                value="{{ old('location', $event->location) }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300">
        </div>

        <!-- ORGANIZER -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Organizer Name
            </label>
            <input type="text"
                name="organizer_name"
                value="{{ old('organizer_name', $event->organizer_name) }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300">
        </div>

        <!-- ORGANIZER LOGO -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-2">
                Organizer Logo
            </label>

            @if($event->organizer_logo)
            <img src="{{ asset('storage/'.$event->organizer_logo) }}"
                class="w-16 h-16 object-contain mb-2 rounded">
            @endif

            <input type="file" name="organizer_logo">
            <p class="text-xs text-gray-400 mt-1">
                Leave empty to keep existing logo
            </p>
        </div>

        <!-- EVENT IMAGE -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-2">
                Event Image
            </label>

            @if($event->event_image)
            <img src="{{ asset('storage/'.$event->event_image) }}"
                class="w-24 h-24 object-cover rounded mb-2">
            @endif

            <input type="file" name="event_image">
            <p class="text-xs text-gray-400 mt-1">
                Leave empty to keep existing image
            </p>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex justify-end gap-4 pt-4">

            <a href="{{ route('ptoEvents.index') }}"
                class="px-6 py-3 rounded-lg
                      border border-gray-300
                      text-gray-600
                      hover:bg-gray-100 transition">
                Cancel
            </a>

            <button type="submit"
                class="px-8 py-3 rounded-lg
                       border-2 border-[#00285E]
                       text-[#00285E] font-semibold
                       hover:bg-[#00285E] hover:text-white
                       transition-all active:scale-95">
                Update Event
            </button>

        </div>

    </form>

</div>

@endsection