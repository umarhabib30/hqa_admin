@extends('layouts.layout')
@section('content')

<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-4">Create Post</h2>

    <form method="POST" enctype="multipart/form-data"
        action="{{ route('alumniPosts.store') }}">
        @csrf

        <input name="title" class="w-full border p-3 rounded mb-3" placeholder="Post Title">

        <textarea name="description"
            class="w-full border p-3 rounded mb-3"
            placeholder="Post Description"></textarea>

        <input type="date" name="post_date" class="w-full border p-3 rounded mb-3">

        <input type="file" name="image" class="mb-4 border-2 border-[#00285E] p-3">

        <button class="w-full border-2 border-[#00285E] text-[#00285E] p-3 rounded
               hover:bg-[#00285E] hover:text-white transition">
            Save Post
        </button>

    </form>
</div>
@endsection