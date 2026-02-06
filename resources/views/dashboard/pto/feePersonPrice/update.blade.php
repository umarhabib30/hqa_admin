@extends('layouts.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Edit Fee</h1>

    <form method="POST" action="{{ route('fee.update', $fee) }}"
        class="bg-white rounded-xl shadow p-6 grid md:grid-cols-2 gap-6">
        @csrf
        @method('PUT')

        {{-- Event Selection --}}
        <div class="md:col-span-2">
            <label class="block mb-1 font-medium">Select Event</label>
            <select name="event_id" required
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                       focus:ring-2 focus:ring-[#00285E] focus:outline-none">
                <option value="" disabled>-- Choose an Event --</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}" @selected($fee->event_id == $event->id)>
                        {{ $event->title }}
                    </option>
                @endforeach
            </select>
            @error('event_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- Fee Price --}}
        <div>
            <label class="block mb-1 font-medium">Fee Per Person ($)</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $fee->price) }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                       focus:ring-2 focus:ring-[#00285E] focus:outline-none"
                placeholder="25.00" required>
            @error('price')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- Status --}}
        <div>
            <label class="block mb-1 font-medium">Status</label>
            <select name="is_active"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                       focus:ring-2 focus:ring-[#00285E] focus:outline-none">
                <option value="1" @selected($fee->is_active == 1)>Active</option>
                <option value="0" @selected($fee->is_active == 0)>Inactive</option>
            </select>
        </div>

        {{-- Actions --}}
        <div class="md:col-span-2 flex gap-4 mt-2">
            <button type="submit"
                class="px-6 py-2 bg-[#27A55B] text-white rounded-lg
                       hover:bg-green-700 transition">
                Update Fee
            </button>

            <a href="{{ route('fee.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-100 transition">
                Cancel
            </a>
        </div>
    </form>
@endsection
