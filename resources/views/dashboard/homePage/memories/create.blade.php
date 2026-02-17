@extends('layouts.layout')
@section('content')

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">
    <h2 class="text-2xl font-semibold mb-6">Add Home Alumni</h2>

    <form method="POST" enctype="multipart/form-data"
        action="{{ route('memories.store') }}"
        class="space-y-4">
        @csrf

        {{-- <input name="title" placeholder="Title"
            class="w-full border p-3 rounded">

        <textarea name="desc" placeholder="Description"
            class="w-full border p-3 rounded"></textarea> --}}

        <textarea name="quote" placeholder="Quote"
            class="w-full border p-3 rounded italic"></textarea>

        <input name="name" placeholder="Student / Alumni Name"
            class="w-full border p-3 rounded">

        <input name="graduated" placeholder="Graduated (e.g Class of 2022)"
            class="w-full border p-3 rounded">

        <!-- IMAGE -->
        <input type="file" name="image"
            class="w-full border p-2 rounded">

        <button class="w-full border-2 border-[#00285E] text-[#00285E] p-3 rounded
                       hover:bg-[#00285E] hover:text-white transition">
            Save Memory
        </button>
    </form>
</div>

@endsection