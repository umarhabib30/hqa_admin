@extends('layouts.layout')

@section('content')

<h1 class="text-2xl font-bold mb-6">Edit Job Application</h1>

<form method="POST"
    action="{{ route('jobApp.update', $jobApplication) }}"
    enctype="multipart/form-data"
    class="bg-white p-6 rounded-xl shadow grid md:grid-cols-2 gap-6">
    @csrf
    @method('PUT')

    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" name="first_name"
        value="{{ $jobApplication->first_name }}" required>

    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" name="last_name"
        value="{{ $jobApplication->last_name }}" required>

    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" type="email" name="email"
        value="{{ $jobApplication->email }}" required>

    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" name="phone"
        value="{{ $jobApplication->phone }}" required>

    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" type="number" name="years_experience"
        value="{{ $jobApplication->years_experience }}" min="0" required>

    {{-- CURRENT CV --}}
    <div class="  md:col-span-2 text-sm">
        Current CV:
        <a href="{{ asset('storage/'.$jobApplication->cv_path) }}"
            target="_blank"
            class="text-blue-600 underline">
            View CV
        </a>
    </div>

    {{-- OPTIONAL NEW CV --}}
    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none md:col-span-2" type="file" name="cv">

    <textarea class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none md:col-span-2"
        name="description"
        placeholder="Description">{{ $jobApplication->description }}</textarea>

    <div class="md:col-span-2 flex gap-4">
        <button class="px-6 py-2 bg-[#00285E] text-white rounded-lg">
            Update
        </button>

        <a href="{{ route('jobApp.index') }}"
            class="px-6 py-2 border rounded-lg">
            Cancel
        </a>
    </div>
</form>

@endsection