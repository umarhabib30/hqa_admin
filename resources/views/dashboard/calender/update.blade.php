@extends('layouts.layout')
@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

        <h2 class="text-xl font-semibold mb-6">Edit Calendar Event</h2>

        <form method="POST" action="{{ route('calender.update', $event->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <input name="title" value="{{ $event->title }}" class="w-full px-4 py-3 border rounded-lg">

            <textarea name="description" class="w-full px-4 py-3 border rounded-lg">{{ $event->description }}</textarea>

            <input name="category" value="{{ $event->category }}" class="w-full px-4 py-3 border rounded-lg">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="date" name="start_date" value="{{ $event->start_date }}"
                    class="w-full px-4 py-3 border rounded-lg">

                <input type="date" name="end_date" value="{{ $event->end_date }}"
                    class="w-full px-4 py-3 border rounded-lg">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="time" name="start_time" value="{{ $event->start_time }}"
                    class="w-full px-4 py-3 border rounded-lg">

                <input type="time" name="end_time" value="{{ $event->end_time }}"
                    class="w-full px-4 py-3 border rounded-lg">
            </div>

            <input name="location" value="{{ $event->location }}" class="w-full px-4 py-3 border rounded-lg">

            <input name="link" value="{{ $event->link }}" class="w-full px-4 py-3 border rounded-lg">

            <div class="flex justify-end gap-4">
                <a href="{{ route('calender.index') }}" class="px-6 py-3 border rounded-lg">Back</a>

                <button
                    class="px-8 py-3 border-2 border-[#00285E] text-[#00285E]
                           rounded-lg hover:bg-[#00285E] hover:text-white">
                    Update
                </button>
            </div>
        </form>
    </div>
@endsection
