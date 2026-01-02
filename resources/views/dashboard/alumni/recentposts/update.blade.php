@extends('layouts.layout')
@section('content')

<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-4">Edit Post</h2>

    <form method="POST" enctype="multipart/form-data"
        action="{{ route('alumniPosts.update',$post->id) }}">
        @csrf
        @method('PUT')

        <input name="title" value="{{ $post->title }}" class="w-full border p-3 rounded mb-3">

        <textarea name="description"
            class="w-full border p-3 rounded mb-3">{{ $post->description }}</textarea>

        <input type="date" name="post_date"
            value="{{ $post->post_date }}"
            class="w-full border p-3 rounded mb-3">

        @if($post->image)
        <img src="{{ asset('storage/'.$post->image) }}"
            class="w-20 mb-3 rounded">
        @endif

        <input type="file" name="image" class="mb-4">

        <button class="w-full border-2 border-[#00285E] text-[#00285E] p-3 rounded
               hover:bg-[#00285E] hover:text-white transition">
            Update Post
        </button>

    </form>
</div>
@endsection