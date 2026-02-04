@extends('layouts.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Update Alumni Fee</h1>

    <form method="POST" action="{{ route('alumniFee.update', $fee) }}"
        class="bg-white rounded-xl shadow p-6 grid md:grid-cols-2 gap-6">
        @csrf
        @method('PUT')

        {{-- Event Selection --}}
        <div class="md:col-span-2">
            <label class="block mb-1 font-medium">Select Alumni Event</label>
            <select name="event_id" required
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00285E] focus:outline-none">
                @foreach ($events as $event)
                    <option value="{{ $event->id }}" {{ $event->id == $fee->event_id ? 'selected' : '' }}>
                        {{ $event->title }}
                    </option>
                @endforeach
            </select>
            @error('event_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label class="block mb-1 font-medium">Fee Title</label>
            <input type="text" name="title" value="{{ old('title', $fee->title) }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00285E] focus:outline-none"
                required>
            @error('title')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Fee Per Person ($)</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $fee->price) }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00285E] focus:outline-none"
                required>
            @error('price')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Status</label>
            <select name="is_active"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00285E] focus:outline-none">
                <option value="1" {{ $fee->is_active ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !$fee->is_active ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="md:col-span-2 flex gap-4 mt-2">
            <button type="submit" class="px-6 py-2 bg-[#27A55B] text-white rounded-lg hover:bg-green-700 transition">
                Update Fee
            </button>

            <a href="{{ route('alumniFee.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-100 transition">
                Cancel
            </a>
        </div>
    </form>
@endsection
