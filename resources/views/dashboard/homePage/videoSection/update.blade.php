@extends('layouts.layout')
@section('content')

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">
        <h2 class="text-2xl font-semibold mb-6">Edit Video</h2>

        <form method="POST" enctype="multipart/form-data" action="{{ route('videos.update', $video->id) }}"
            class="space-y-6">
            @csrf
            @method('PUT')

            <input name="title" value="{{ $video->title }}" class="w-full border p-3 rounded" required>

            <textarea name="desc" class="w-full border p-3 rounded h-32">{{ $video->desc }}</textarea>

            <div>
                <label class="block font-medium mb-2">Current Thumbnail</label>
                @if($video->image)
                    <img src="{{ asset('storage/' . $video->image) }}" class="w-48 h-32 object-cover rounded mb-3">
                @endif
                <input type="file" name="image" class="w-full" accept="image/*">
            </div>

            <input name="video_link" type="url" value="{{ $video->video_link }}" class="w-full border p-3 rounded" required>

            <button class="w-full bg-blue-600 text-white p-3 rounded hover:bg-blue-700">
                Update Video
            </button>
        </form>
    </div>

@endsection