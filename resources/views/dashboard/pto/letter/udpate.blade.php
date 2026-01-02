@extends('layouts.layout')

@section('content')
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-xl shadow">

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">
                Update PTO Download
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Update newsletter or guide file (leave empty to keep existing)
            </p>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('ptoLetterGuide.update', $item->id) }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Newsletter -->
            <div>
                <label class="block mb-2 font-medium text-gray-700">
                    Newsletter File
                </label>

                <div class="flex items-center gap-4 mb-2">
                    <a href="{{ asset('storage/' . $item->newsletter_download) }}" target="_blank"
                        class="text-[#00285E] hover:underline text-sm">
                        View Current File
                    </a>
                </div>

                <input type="file" name="newsletter_download" class="w-full border rounded-lg p-2">
            </div>

            <!-- Guide -->
            <div>
                <label class="block mb-2 font-medium text-gray-700">
                    Guide File
                </label>

                <div class="flex items-center gap-4 mb-2">
                    <a href="{{ asset('storage/' . $item->guide_download) }}" target="_blank"
                        class="text-[#00285E] hover:underline text-sm">
                        View Current File
                    </a>
                </div>

                <input type="file" name="guide_download" class="w-full border rounded-lg p-2">
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('ptoLetterGuide.index') }}"
                    class="px-5 py-2 border rounded-lg hover:bg-gray-100 transition">
                    Cancel
                </a>

                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg
                               hover:bg-blue-700 transition">
                    Update
                </button>
            </div>

        </form>
    </div>
@endsection