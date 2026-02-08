@extends('layouts.layout')
@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">
        <h2 class="text-2xl font-semibold mb-6">Add Social Link</h2>

        <form method="POST" enctype="multipart/form-data" action="{{ route('socials.store') }}" class="space-y-6">
            @csrf

            <input name="title" placeholder="Title (e.g. Facebook)" class="w-full border p-3 rounded" required>

            {{-- <textarea name="desc" placeholder="Description (optional)" class="w-full border p-3 rounded h-24"></textarea> --}}

            <div>
                <label class="block font-medium mb-2">Icon/Image *</label>
                <input type="file" name="image" accept="image/*" class="w-full border-[#00285E] border-2 p-3" required>
            </div>

            <input name="fblink" type="url" placeholder="https://facebook.com/yourpage"
                class="w-full border p-3 rounded">
            {{-- <input name="ytlink" type="url" placeholder="https://Youtube.com/yourpage"
                class="w-full border p-3 rounded">
            <input name="tiktoklink" type="url" placeholder="https://Instagram.com/yourpage"
                class="w-full border p-3 rounded">
            <input name="instalink" type="url" placeholder="https://Tiktok.com/yourpage"
                class="w-full border p-3 rounded"> --}}

            <button class="w-full bg-[#00285E] text-white p-3 rounded hover:bg-blue-700">
                Save Link
            </button>
        </form>
    </div>
@endsection
