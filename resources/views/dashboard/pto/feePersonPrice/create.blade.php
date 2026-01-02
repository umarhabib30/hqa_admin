@extends('layouts.layout')

@section('content')

<h1 class="text-2xl font-bold mb-6">Create Fee</h1>

<form method="POST" action="{{ route('fee.store') }}"
    class="bg-white rounded-xl shadow p-6 grid md:grid-cols-2 gap-6">
    @csrf

    <div class="md:col-span-2">
        <label class="block mb-1 font-medium">Title</label>
        <input type="text" name="title"
            class= "w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none"
            placeholder="Alumni Event 2025"
            required>
    </div>

    <div>
        <label class="block mb-1 font-medium">Fee Per Person ($)</label>
        <input type="number" step="0.01" name="price"
            class= "w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none"
            placeholder="25"
            required>
    </div>

    <div>
        <label class="block mb-1 font-medium">Status</label>
        <select name="is_active" class= "w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
    </div>

    <div class="md:col-span-2 flex gap-4">
        <button
            class="px-6 py-2 bg-[#27A55B] text-white rounded-lg
                   hover:bg-green-700 transition">
            Save Fee
        </button>

        <a href="{{ route('fee.index') }}"
            class="px-6 py-2 border rounded-lg hover:bg-gray-100 transition">
            Cancel
        </a>
    </div>
</form>

@endsection