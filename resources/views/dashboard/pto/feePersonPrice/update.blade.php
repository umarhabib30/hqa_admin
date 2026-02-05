@extends('layouts.layout')

@section('content')

<h1 class="text-2xl font-bold mb-6">Edit Fee</h1>

<form method="POST"
    action="{{ route('fee.update', $fee) }}"
    class="bg-white rounded-xl shadow p-6 grid md:grid-cols-2 gap-6">
    @csrf
    @method('PUT')

    {{-- <div class="md:col-span-2">
        <label class="block mb-1 font-medium">Title</label>
        <input type="text" name="title"
            value="{{ $fee->title }}"
            class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none"
            required>
    </div> --}}

    <div>
        <label class="block mb-1 font-medium">Fee Per Person ($)</label>
        <input type="number" step="0.01" name="price"
            value="{{ $fee->price }}"
            class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none"
            required>
    </div>

    <div>
        <label class="block mb-1 font-medium">Status</label>
        <select name="is_active" class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none">
            <option value="1" @selected($fee->is_active)>Active</option>
            <option value="0" @selected(!$fee->is_active)>Inactive</option>
        </select>
    </div>

    <div class="md:col-span-2 flex gap-4">
        <button
            class="px-6 py-2 bg-[#27A55B] text-white rounded-lg
                   hover:bg-green-700 transition">
            Update Fee
        </button>

        <a href="{{ route('fee.index') }}"
            class="px-6 py-2 border rounded-lg hover:bg-gray-100 transition">
            Cancel
        </a>
    </div>
</form>

@endsection