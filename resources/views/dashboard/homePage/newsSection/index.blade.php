@extends('layouts.layout')

@section('content')

    <div>
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Media Section</h1>

            <a href="{{ route('news.create') }}" class="px-6 py-3 border-2 border-[#00285E] text-[#00285E]
                       rounded-xl hover:bg-[#00285E] hover:text-white transition">
                + Add Media
            </a>
        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 text-sm text-gray-700">
                    <tr>
                        <th class="p-4 text-left">Image</th>
                        <th class="p-4 text-left">Title</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($news as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- Image -->
                            <td class="p-4">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" class="w-14 h-14 object-cover rounded-lg">
                                @else
                                    <span class="text-gray-400 text-sm">No Image</span>
                                @endif
                            </td>

                            <!-- Title -->
                            <td class="p-4 font-medium text-gray-800">
                                {{ $item->title }}
                            </td>

                            <!-- Video -->
                           

                            <!-- Actions -->
                            <td class="p-4 text-center">
                                <a href="{{ route('news.edit', $item->id) }}"
                                    class="px-3 py-1 border border-[00285E] text-[00285E] rounded mr-2 hover:bg-blue-50">
                                    Edit
                                </a>

                                <form action="{{ route('news.destroy', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Are you sure?')"
                                        class="px-3 py-1 border border-red-500 text-red-500 rounded hover:bg-red-50">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500">
                                No media found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection