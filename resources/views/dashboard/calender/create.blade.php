@extends('layouts.layout')
@section('content')

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-6">Create Calendar Event</h2>

    <form method="POST" action="{{ route('calender.store') }}" class="space-y-6">
        @csrf

        <input name="title" placeholder="Event Title"
            class="w-full px-4 py-3 border rounded-lg">

        <textarea name="description" placeholder="Description"
            class="w-full px-4 py-3 border rounded-lg"></textarea>

        <input name="category" placeholder="Category"
            class="w-full px-4 py-3 border rounded-lg">

        <div class="grid grid-cols-2 gap-4">
                <div class="grid grid-cols-1 gap-4">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" class="border p-3 rounded">
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" class="border p-3 rounded">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid grid-cols-1 gap-4">
                    <label for="start_time">Start Time</label>
                    <input type="time" name="start_time" class="border p-3 rounded">
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <label for="end_time">End Time</label>
                    <input type="time" name="end_time" class="border p-3 rounded">
                </div>
            </div>

        <input name="location" placeholder="Location"
            class="w-full px-4 py-3 border rounded-lg">

        <input name="link" placeholder="Link"
            class="w-full px-4 py-3 border rounded-lg">

            

        <div class="flex justify-end gap-4">
            <a href="{{ route('calender.index') }}"
                class="px-6 py-3 border rounded-lg">Cancel</a>

            <button class="px-8 py-3 border-2 border-[#00285E] text-[#00285E]
                           rounded-lg hover:bg-[#00285E] hover:text-white">
                Save
            </button>
        </div>
    </form>
</div>

@endsection