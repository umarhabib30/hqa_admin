@extends('layouts.layout')
@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

        <h2 class="text-2xl font-semibold mb-6">Update Alumni Event</h2>

        <form method="POST" action="{{ route('alumniEvent.update', $event->id) }}" enctype="multipart/form-data"
            class="space-y-5">
            @csrf
            @method('PUT')

            <input name="title" value="{{ old('title', $event->title) }}" placeholder="Event Title"
                class="w-full border p-3 rounded">

            <textarea name="description" rows="3" placeholder="Event Description" class="w-full border p-3 rounded">{{ old('description', $event->description) }}</textarea>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid grid-cols-1 gap-4">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $event->start_date) }}"
                        class="border p-3 rounded">
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $event->end_date) }}"
                        class="border p-3 rounded">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid grid-cols-1 gap-4">
                    <label for="start_time">Start Time</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $event->start_time) }}"
                        class="border p-3 rounded">
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <label for="end_time">End Time</label>
                    <input type="time" name="end_time" value="{{ old('end_time', $event->end_time) }}"
                        class="border p-3 rounded">
                </div>
            </div>

            <input name="location" value="{{ old('location', $event->location) }}" placeholder="Location"
                class="border p-3 rounded">
            <input name="organizer_name" value="{{ old('organizer_name', $event->organizer_name) }}"
                placeholder="Organizer Name" class="border p-3 rounded">

            <div class="flex flex-col gap-4">
                <label class="text-sm text-gray-600">Organizer Logo</label>
                <input type="file" name="organizer_logo" class="border-[#00285E] border-2 p-3">
                @if ($event->organizer_logo)
                    <img src="{{ asset('storage/' . $event->organizer_logo) }}" alt="Organizer Logo"
                        class="w-24 h-24 object-cover rounded mt-2">
                @endif
            </div>

            <div class="flex flex-col gap-4">
                <label class="text-sm text-gray-600">Event Image</label>
                <input type="file" name="event_image" class="border-[#00285E] border-2 p-3">
                @if ($event->event_image)
                    <img src="{{ asset('storage/' . $event->event_image) }}" alt="Event Image"
                        class="w-48 h-48 object-cover rounded mt-2">
                @endif
            </div>

            <button
                class="px-8 py-3 border-2 border-[#00285E] text-[#00285E] rounded
                   hover:bg-[#00285E] hover:text-white">
                Update Event
            </button>

        </form>
    </div>
@endsection
