@extends('layouts.layout')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Add Media</h1>

            <a href="{{ route('news.index') }}" class="px-4 py-2 border border-gray-400 rounded-lg hover:bg-gray-100">
                Back
            </a>
        </div>

        <!-- FORM CARD -->
        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('news.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Title -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" name="title"
                        class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200" placeholder="Enter title">
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea name="description" rows="4"
                        class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200"
                        placeholder="Enter description"></textarea>
                </div>

                <!-- Image -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Image</label>
                    <input type="file" name="image" class="w-full border rounded-lg px-4 py-2">
                </div>

                <!-- Video Link -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Video Link</label>
                    <input type="text" name="video_link" class="w-full border rounded-lg px-4 py-2"
                        placeholder="https://youtube.com/...">
                </div>

                <!-- Social Links -->
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Social Links</label>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input type="text" name="social_links[facebook]" class="border rounded-lg px-4 py-2"
                            placeholder="Facebook">

                        <input type="text" name="social_links[instagram]" class="border rounded-lg px-4 py-2"
                            placeholder="Instagram">

                        <input type="text" name="social_links[youtube]" class="border rounded-lg px-4 py-2"
                            placeholder="YouTube">
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Save Media
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection