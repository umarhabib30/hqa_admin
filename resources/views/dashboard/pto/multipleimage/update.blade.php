@extends('layouts.layout')
@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-4">Edit donation Gallery</h2>

    <form method="POST"
        enctype="multipart/form-data"
        action="{{ route('ptoImages.update',$gallery->id) }}">
        @csrf
        @method('PUT')

        <!-- EXISTING IMAGES -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            @foreach($gallery->images as $img)
            <label class="relative block">
                <img src="{{ asset('storage/'.$img) }}"
                    class="w-full h-40 object-cover rounded-lg">

                <input type="checkbox"
                    name="remove_images[]"
                    value="{{ $img }}"
                    class="absolute top-2 right-2">
            </label>
            @endforeach
        </div>

        <p class="text-sm text-gray-500 mb-4">
            ✔ Select images to remove
        </p>

        <!-- ADD NEW IMAGES -->
        <input type="file"
            name="images[]"
            multiple
            class="mb-4 mb-4 border-[#00285E] border-2 p-3">

        <button
            class="w-full border-2 border-[#00285E] text-[#00285E] p-3 rounded
                   hover:bg-[#00285E] hover:text-white transition">
            Update Gallery
        </button>

    </form>
</div>

@endsection