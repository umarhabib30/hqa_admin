@extends('layouts.layout')
@section('content')

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-4">Edit Alumni Event</h2>

    <form method="POST" enctype="multipart/form-data"
        action="{{ route('alumniEvent.update',$event->id) }}">
        @csrf
        @method('PUT')

        <input name="title" value="{{ $event->title }}" class="w-full border p-3 rounded mb-3">

        <textarea name="description"
            class="w-full border p-3 rounded mb-3">{{ $event->description }}</textarea>

        <div class="grid grid-cols-2 gap-3 mb-3">
            <input type="date" name="start_date" value="{{ $event->start_date }}" class="border p-3 rounded">
            <input type="date" name="end_date" value="{{ $event->end_date }}" class="border p-3 rounded">
        </div>

        <div class="grid grid-cols-2 gap-3 mb-3">
            <input type="time" name="start_time" value="{{ $event->start_time }}" class="border p-3 rounded">
            <input type="time" name="end_time" value="{{ $event->end_time }}" class="border p-3 rounded">
        </div>

        <input name="location" value="{{ $event->location }}" class="w-full border p-3 rounded mb-3">
        <input name="organizer_name" value="{{ $event->organizer_name }}" class="w-full border p-3 rounded mb-3">

        @if($event->organizer_logo)
        <img src="{{ asset('storage/'.$event->organizer_logo) }}" class="w-16 mb-2">
        @endif
        <input type="file" name="organizer_logo" class="mb-3">

        @if($event->event_image)
        <img src="{{ asset('storage/'.$event->event_image) }}" class="w-24 mb-2">
        @endif
        <input type="file" name="event_image" class="mb-4">

        <button class="w-full border-2 border-[#00285E] text-[#00285E] p-3 rounded
               hover:bg-[#00285E] hover:text-white transition">
            Update Event
        </button>

    </form>
</div>
@endsection