@extends('layouts.layout')

@section('content')

<h1 class="text-2xl font-bold mb-6">Create Easy Join</h1>

<form method="POST" action="{{ route('easy-joins.store') }}"
    class="bg-white rounded-xl shadow p-6 grid md:grid-cols-2 gap-6">
    @csrf

    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" name="first_name" placeholder="First Name" required>
    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" name="last_name" placeholder="Last Name" required>

    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" type="email" name="email" placeholder="Email" required>

    <select class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" name="is_attending" required>
        <option value="">Attending?</option>
        <option value="yes">Yes</option>
        <option value="no">No</option>
    </select>

    <select class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" name="guest_count" required>
        @for($i=1;$i<=10;$i++)
            <option value="{{ $i }}">{{ $i }} Guest(s)</option>
            @endfor
    </select>

    {{-- Fee Per Person (READ ONLY) --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-600 mb-1">
            Fee Per Person
        </label>

        <div
            class="w-full px-4 py-3 rounded-lg
               border border-gray-300
               bg-gray-100 text-gray-700 font-semibold">
            {{ number_format($fee->price ?? 0, 2) }}
        </div>
    </div>

    <div class="md:col-span-2 flex gap-4">
        <button class="px-6 py-2 bg-[#00285E] text-white rounded-lg hover:bg-green-700 transition">
            Save
        </button>

        <a href="{{ route('easy-joins.index') }}"
            class="px-6 py-2 border rounded-lg hover:bg-gray-100 transition">
            Cancel
        </a>
    </div>
</form>

@endsection