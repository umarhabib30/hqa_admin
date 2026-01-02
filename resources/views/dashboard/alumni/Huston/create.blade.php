@extends('layouts.layout')
@section('content')

<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-4">Add Alumni</h2>

    <form method="POST" enctype="multipart/form-data"
        action="{{ route('alumniHuston.store') }}">
        @csrf

        <input name="name" class="w-full border p-3 rounded mb-3" placeholder="Name">
        <input name="profession" class="w-full border p-3 rounded mb-3" placeholder="Profession">

        <textarea name="description"
            class="w-full border p-3 rounded mb-3"
            placeholder="Description"></textarea>

        <input type="file" name="image" class="mb-4 border-[#00285E] p-3 border-2">

        <button class="w-full border-2 border-[#00285E] text-[#00285E] p-3 rounded
               hover:bg-[#00285E] hover:text-white transition">
            Save
        </button>

    </form>
</div>
@endsection