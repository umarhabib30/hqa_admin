@extends('layouts.layout')
@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-2xl font-semibold text-gray-800 mb-6">
        Create Alumni Form
    </h2>

    <form method="POST"
        enctype="multipart/form-data"
        action="{{ route('alumniForm.store') }}"
        class="space-y-6">
        @csrf

        <!-- NAME -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                <input name="first_name"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300
                              focus:ring-2 focus:ring-[#00285E] outline-none"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                <input name="last_name"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300
                              focus:ring-2 focus:ring-[#00285E] outline-none"
                    required>
            </div>
        </div>

        <!-- GRADUATION / STATUS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Graduation Year</label>
                <input type="number" name="graduation_year"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300
                              focus:ring-2 focus:ring-[#00285E] outline-none"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Marital Status</label>
                <select name="status"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300
                               focus:ring-2 focus:ring-[#00285E] outline-none"
                    required>
                    <option value="">Select</option>
                    <option value="single">Single</option>
                    <option value="married">Married</option>
                </select>
            </div>
        </div>

        <!-- CONTACT -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                <input type="email" name="email"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300
                              focus:ring-2 focus:ring-[#00285E] outline-none"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Phone</label>
                <input name="phone"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300
                              focus:ring-2 focus:ring-[#00285E] outline-none"
                    required>
            </div>
        </div>

        <!-- ADDRESS -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Address</label>
            <input name="address"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                          focus:ring-2 focus:ring-[#00285E] outline-none"
                required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <input name="city" placeholder="City"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                          focus:ring-2 focus:ring-[#00285E] outline-none" required>

            <input name="state" placeholder="State"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                          focus:ring-2 focus:ring-[#00285E] outline-none" required>

            <input name="zipcode" placeholder="Zip Code"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                          focus:ring-2 focus:ring-[#00285E] outline-none" required>
        </div>

        <!-- EDUCATION -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input name="college" placeholder="College"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                          focus:ring-2 focus:ring-[#00285E] outline-none" required>

            <input name="degree" placeholder="Degree"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                          focus:ring-2 focus:ring-[#00285E] outline-none" required>
        </div>

        <!-- JOB -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input name="company" placeholder="Company"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                          focus:ring-2 focus:ring-[#00285E] outline-none">

            <input name="job_title" placeholder="Job Title"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                          focus:ring-2 focus:ring-[#00285E] outline-none">
        </div>

        <!-- ACHIEVEMENTS -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Achievements</label>
            <textarea name="achievements" rows="3"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                             focus:ring-2 focus:ring-[#00285E] outline-none"></textarea>
        </div>

        <!-- FILES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Profile Image</label>
                <input type="file" name="image"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Document</label>
                <input type="file" name="document"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>

        <!-- ACTION -->
        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('alumniForm.index') }}"
                class="px-6 py-3 rounded-lg border border-gray-300 text-gray-600
                      hover:bg-gray-100 transition">
                Cancel
            </a>

            <button type="submit"
                class="px-8 py-3 rounded-lg border-2 border-[#00285E]
                       text-[#00285E] font-semibold
                       hover:bg-[#00285E] hover:text-white
                       transition-all duration-300 active:scale-95">
                Save Form
            </button>
        </div>

    </form>
</div>

@endsection