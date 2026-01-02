@extends('layouts.layout')
@section('content')

<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-4">Upload Alumni Images</h2>

    <form method="POST"
        enctype="multipart/form-data"
        action="{{ route('alumniImages.store') }}">
        @csrf

        <input type="file"
            name="images[]"
            multiple
            class="mb-4 border-[#00285E] border-2 p-3">

        <button class="w-full border-2 border-[#00285E] text-[#00285E] p-3 rounded
               hover:bg-[#00285E] hover:text-white transition">
            Upload Images
        </button>

    </form>
</div>
@endsection