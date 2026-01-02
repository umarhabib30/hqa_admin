@extends('layouts.layout')
@section('content')

<div>

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
            Videos Section
        </h1>

        <a href="{{ route('videos.create') }}"
            class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-blue-600
                   text-blue-600 font-semibold
                   hover:bg-blue-600 hover:text-white
                   transition active:scale-95">
            + Add Video
        </a>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block bg-white rounded-xl shadow overflow-hidden">

        <table class="w-full">
            <thead class="bg-gray-100 text-sm text-gray-700">
                <tr>
                    <th class="p-4 text-left">Thumbnail</th>
                    <th class="p-4 text-left">Title</th>
                    <th class="p-4 text-left">Video Link</th>
                    <th class="p-4 text-center">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($videos as $video)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4">
                        @if($video->image)
                        <img
                            src="{{ asset('storage/' . $video->image) }}"
                            class="w-24 h-16 object-cover rounded-lg border">
                        @else
                        <div class="w-24 h-16 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-500">
                            No Image
                        </div>
                        @endif
                    </td>

                    <td class="p-4 font-medium">
                        {{ $video->title }}
                    </td>

                    <td class="p-4">
                        <a href="{{ $video->video_link }}" target="_blank"
                            class="text-[#00285E] underline text-sm break-all">
                            {{ Str::limit($video->video_link, 50) }}
                        </a>
                    </td>

                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('videos.edit', $video->id) }}"
                                class="px-3 py-1 rounded
                                       border border-[00285E] text-[00285E]
                                       hover:bg-[00285E] hover:text-white transition">
                                Edit
                            </a>

                            <form action="{{ route('videos.destroy', $video->id) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <button
                                    onclick="return confirm('Delete this video?')"
                                    class="px-3 py-1 rounded
                                           border border-red-500 text-red-500
                                           hover:bg-red-500 hover:text-white transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-gray-500">
                        No videos added yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <!-- MOBILE CARDS -->
    <div class="md:hidden space-y-4">

        @forelse($videos as $video)
        <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">

            <div class="flex gap-3 items-center">
                @if($video->image)
                <img
                    src="{{ asset('storage/' . $video->image) }}"
                    class="w-24 h-16 rounded-lg object-cover border">
                @else
                <div class="w-24 h-16 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-500">
                    No Image
                </div>
                @endif

                <div>
                    <h3 class="font-semibold text-gray-800">
                        {{ $video->title }}
                    </h3>
                </div>
            </div>

            <a href="{{ $video->video_link }}" target="_blank"
                class="block text-sm text-[#00285E] underline break-all">
                {{ Str::limit($video->video_link, 80) }}
            </a>

            <div class="flex gap-2 pt-2">
                <a href="{{ route('videos.edit', $video->id) }}"
                    class="flex-1 text-center px-4 py-2 rounded-lg
                           border border-[00285E] text-[00285E]
                           hover:bg-[00285E] hover:text-white transition">
                    Edit
                </a>

                <form action="{{ route('videos.destroy', $video->id) }}"
                    method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button
                        onclick="return confirm('Delete this video?')"
                        class="w-full px-4 py-2 rounded-lg
                               border border-red-500 text-red-500
                               hover:bg-red-500 hover:text-white
                               transition active:scale-95">
                        Delete
                    </button>
                </form>
            </div>

        </div>
        @empty
        <div class="text-center text-gray-500 py-8">
            No videos added yet.
        </div>
        @endforelse

    </div>

</div>

@endsection