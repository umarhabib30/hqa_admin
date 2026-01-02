@extends('layouts.layout')

@section('content')

<h1 class="text-2xl font-bold mb-6">Apply for Job</h1>

<form method="POST" action="{{ route('jobApp.store') }}"
    enctype="multipart/form-data"
    class="bg-white p-6 rounded-xl shadow grid md:grid-cols-2 gap-6">
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
                          focus:outline-none" name="email" type="email" placeholder="Email" required>
    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" name="phone" placeholder="Phone Number" required>

    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" type="number" name="years_experience"
        placeholder="Years of Experience" min="0" required>

    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" type="file" name="cv" required>

    <textarea class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none"
        name="description"
        placeholder="Short description / cover letter"></textarea>

    <button class="px-6 py-2 bg-[#00285E] text-white rounded-lg md:col-span-2">
        Submit Application
    </button>
</form>

@endsection