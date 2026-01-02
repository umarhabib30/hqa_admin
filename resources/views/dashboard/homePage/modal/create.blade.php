@extends('layouts.layout')
@section('content')

<div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-6">Create Home Modal</h2>

    <form method="POST"
        enctype="multipart/form-data"
        action="{{ route('homeModal.store') }}"
        class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input name="title"
                class="w-full px-4 py-3 border rounded-lg"
                required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Content Description</label>
            <textarea name="cdesc" rows="3"
                class="w-full px-4 py-3 border rounded-lg"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Image</label>
            <input type="file" name="image"
                class="w-full px-3 py-2 border rounded-lg">
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('homeModal.index') }}"
                class="px-6 py-3 border rounded-lg">Cancel</a>

            <button
                class="px-8 py-3 border-2 border-[#00285E] text-[#00285E] rounded-lg
                       hover:bg-[#00285E] hover:text-white transition">
                Save
            </button>
        </div>
    </form>
</div>

@endsection