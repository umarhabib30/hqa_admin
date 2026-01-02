@extends('layouts.layout')

@section('content')
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-xl shadow">

        <h2 class="text-xl font-semibold mb-6">Add PTO Download</h2>

        <form action="{{ route('ptoLetterGuide.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block mb-2 font-medium text-gray-700">
                    Newsletter File
                </label>
                <input type="file" name="newsletter_download" class="w-full border rounded-lg p-2">
            </div>

            <div>
                <label class="block mb-2 font-medium text-gray-700">
                    Guide File
                </label>
                <input type="file" name="guide_download" class="w-full border rounded-lg p-2">
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('ptoLetterGuide.index') }}" class="px-5 py-2 border rounded-lg hover:bg-gray-100">
                    Cancel
                </a>

                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Save
                </button>
            </div>
        </form>

    </div>
@endsection