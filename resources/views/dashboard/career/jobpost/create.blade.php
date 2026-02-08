@extends('layouts.layout')
@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md border border-gray-100">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#00285E]">Create Job Post</h1>
            <p class="text-gray-500 mt-1">Fill in the details below to post a new job opening.</p>
        </div>

        <form action="{{ route('jobPost.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Job Category --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Job Category</label>
                <input type="text" name="job_category" value="{{ old('job_category') }}"
                    placeholder="e.g. Computer Science Teacher"
                    class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent outline-none transition shadow-sm">
                @error('job_category')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Job Location --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                    <input type="text" name="job_location" value="{{ old('job_location') }}"
                        placeholder="e.g. Remote or New York, NY"
                        class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent outline-none transition shadow-sm">
                    @error('job_location')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Job Experience --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Required Experience</label>
                    <input type="text" name="job_experience" value="{{ old('job_experience') }}"
                        placeholder="e.g. 2-3 Years"
                        class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent outline-none transition shadow-sm">
                    @error('job_experience')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Job Education --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Education</label>
                <input type="text" name="job_education" value="{{ old('job_education') }}"
                    placeholder="e.g. Bachelor's in CS"
                    class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent outline-none transition shadow-sm">
                @error('job_education')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            {{-- Job Description --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Job Description</label>
                <textarea name="job_description" placeholder="Describe the roles and responsibilities..."
                    class="w-full border border-gray-300 p-3 rounded-lg h-40 focus:ring-2 focus:ring-[#00285E] focus:border-transparent outline-none transition shadow-sm resize-none">{{ old('job_description') }}</textarea>
                @error('job_description')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between mt-10 pt-6 ">
                <button type="button" onclick="window.history.back()"
                    class="text-gray-600 font-medium hover:text-gray-800 transition px-2">
                    Cancel
                </button>
                <button type="submit"
                    class="px-8 py-3 bg-[#00285E] hover:bg-[#001d45] text-white font-bold rounded-lg shadow-lg transform active:scale-95 transition-all">
                    Publish Job Post
                </button>
            </div>
        </form>
    </div>
@endsection
