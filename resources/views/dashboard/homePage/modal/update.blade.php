@extends('layouts.layout')
@section('content')

<div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-6">Edit Home Modal</h2>

    <form method="POST"
        enctype="multipart/form-data"
        action="{{ route('homeModal.update',$modal->id) }}"
        class="space-y-6">
        @csrf
        @method('PUT')

        <input name="title"
            value="{{ $modal->title }}"
            class="w-full px-4 py-3 border rounded-lg">

        <textarea name="cdesc" rows="3"
            class="w-full px-4 py-3 border rounded-lg">{{ $modal->cdesc }}</textarea>

        <input type="file" name="image"
            class="w-full px-3 py-2 border rounded-lg">

        @if($modal->image)
        <img src="{{ asset('storage/'.$modal->image) }}"
            class="w-32 mt-3 rounded">
        @endif

        <div class="flex justify-end gap-4">
            <a href="{{ route('homeModal.index') }}"
                class="px-6 py-3 border rounded-lg">Back</a>

            <button
                class="px-8 py-3 border-2 border-[#00285E] text-[#00285E] rounded-lg
                       hover:bg-[#00285E] hover:text-white transition">
                Update
            </button>
        </div>
    </form>
</div>

@endsection