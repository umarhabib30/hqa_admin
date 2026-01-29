@extends('layouts.layout')
@section('content')

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">
    <h2 class="text-2xl font-semibold mb-6">Edit Home Memory</h2>

    <form method="POST" enctype="multipart/form-data"
        action="{{ route('memories.update',$memory->id) }}"
        class="space-y-4">
        @csrf
        @method('PUT')

        {{-- <input name="title" value="{{ $memory->title }}"
            class="w-full border p-3 rounded">

        <textarea name="desc"
            class="w-full border p-3 rounded">{{ $memory->desc }}</textarea> --}}

        <textarea name="quote"
            class="w-full border p-3 rounded italic">{{ $memory->quote }}</textarea>

        <input name="name" value="{{ $memory->name }}"
            class="w-full border p-3 rounded">

        <input name="graduated" value="{{ $memory->graduated }}"
            class="w-full border p-3 rounded">

        <!-- EXISTING IMAGE -->
        @if($memory->image)
        <img src="{{ Storage::url($memory->image) }}"
            class="w-24 h-24 object-cover rounded-lg mb-2">
        @endif

        <input type="file" name="image"
            class="w-full border p-2 rounded">

        <button class="w-full border-2 border-[#00285E] text-[#00285E] p-3 rounded
                       hover:bg-[#00285E] hover:text-white transition">
            Update Memory
        </button>
    </form>
</div>

@endsection