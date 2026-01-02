@extends('layouts.layout')
@section('content')

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-4">Create Alumni Event</h2>

    <form method="POST" enctype="multipart/form-data"
        action="{{ route('alumniEvent.store') }}">
        @csrf

        <input name="title" class="w-full border p-3 rounded mb-3" placeholder="Event Title">

        <textarea name="description"
            class="w-full border p-3 rounded mb-3"
            placeholder="Event Description"></textarea>

        <div class="grid grid-cols-2 gap-3 mb-3">
            <input type="date" name="start_date" class="border p-3 rounded">
            <input type="date" name="end_date" class="border p-3 rounded">
        </div>

        <div class="grid grid-cols-2 gap-3 mb-3">
            <input type="time" name="start_time" class="border p-3 rounded">
            <input type="time" name="end_time" class="border p-3 rounded">
        </div>

        <input name="location" class="w-full border p-3 rounded mb-3" placeholder="Location">
        <input name="organizer_name" class="w-full border p-3 rounded mb-3" placeholder="Organizer Name">

        <label class="block text-sm mb-1">Organizer Logo</label>
        <input type="file" name="organizer_logo" class="mb-3 border-[#00285E] border-2 p-3">

        <label class="block text-sm mb-1">Event Image</label>
        <input type="file" name="event_image" class="mb-4 border-[#00285E] border-2 p-3">

        <button class="w-full border-2 border-[#00285E] text-[#00285E] p-3 rounded
               hover:bg-[#00285E] hover:text-white transition">
            Save Event
        </button>

    </form>
</div>
@endsection