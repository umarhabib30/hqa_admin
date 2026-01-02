@extends('layouts.layout')
@section('content')

<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-4">Edit Alumni</h2>

    <form method="POST" enctype="multipart/form-data"
        action="{{ route('alumniHuston.update',$alumni->id) }}">
        @csrf
        @method('PUT')

        <input name="name" value="{{ $alumni->name }}" class="w-full border p-3 rounded mb-3">
        <input name="profession" value="{{ $alumni->profession }}" class="w-full border p-3 rounded mb-3">

        <textarea name="description"
            class="w-full border p-3 rounded mb-3">{{ $alumni->description }}</textarea>

        @if($alumni->image)
        <img src="{{ asset('storage/'.$alumni->image) }}"
            class="w-16 h-16 rounded-full mb-3">
        @endif

        <input type="file" name="image" class="mb-4">

        <button class="w-full border-2 border-[#00285E] text-[#00285E] p-3 rounded
               hover:bg-[#00285E] hover:text-white transition">
            Update
        </button>

    </form>
</div>
@endsection