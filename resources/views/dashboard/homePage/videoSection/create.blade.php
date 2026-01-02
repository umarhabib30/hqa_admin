@extends('layouts.layout')
@section('content')

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">
        <h2 class="text-2xl font-semibold mb-6">Add New Video</h2>

        <form method="POST" enctype="multipart/form-data" action="{{ route('videos.store') }}" class="space-y-6">
            @csrf

            <input name="title" placeholder="Video Title" class="w-full border p-3 rounded" required>

            <textarea name="desc" placeholder="Description (optional)" class="w-full border p-3 rounded h-32"></textarea>

            <div>
                <label class="block font-medium mb-2">Thumbnail Image *</label>
                <input type="file" name="image" class="w-full border-[#00285E] border-2 p-3" required accept="image/*">
            </div>

            <input name="video_link" type="url" placeholder="https://youtube.com/watch?v=..."
                class="w-full border p-3 rounded" required>

            <button class="w-full bg-[#00285E] text-white p-3 rounded hover:bg-blue-700">
                Save Video
            </button>
        </form>
    </div>

@endsection