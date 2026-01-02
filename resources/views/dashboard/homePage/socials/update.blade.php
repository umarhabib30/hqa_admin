@extends('layouts.layout')
@section('content')

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">
        <h2 class="text-2xl font-semibold mb-6">Edit Social Link</h2>

        <form method="POST" enctype="multipart/form-data" action="{{ route('socials.update', $social->id) }}"
            class="space-y-6">
            @csrf
            @method('PUT')

            <input name="title" value="{{ $social->title }}" class="w-full border p-3 rounded" required>

            <textarea name="desc" class="w-full border p-3 rounded h-24">{{ $social->desc }}</textarea>

            <div>
                <label class="block font-medium mb-2">Current Icon</label>
                @if($social->image)
                    <img src="{{ asset('storage/' . $social->image) }}" class="w-32 h-32 object-contain rounded mb-3">
                @endif
                <input type="file" name="image" accept="image/*" class="w-full border-[#00285E] border-2 p-3">
            </div>

            <input name="fblink" type="url" value="{{ $social->fblink }}" class="w-full border p-3 rounded" required>
            <input name="ytlink" type="url" value="{{ $social->ytlink }}" class="w-full border p-3 rounded" required>
            <input name="tiktoklink" type="url" value="{{ $social->tiktoklink }}" class="w-full border p-3 rounded" required>
            <input name="instalink" type="url" value="{{ $social->instalink }}" class="w-full border p-3 rounded" required>

            <button class="w-full bg-[#00285E] text-white p-3 rounded hover:bg-blue-700">
                Update Link
            </button>
        </form>
    </div>

@endsection