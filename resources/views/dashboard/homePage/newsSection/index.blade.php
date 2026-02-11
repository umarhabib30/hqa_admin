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
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto p-4">
                <table id="newsSectionTable" class="display w-full text-left" style="width:100%">
                    <thead>
                        <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider font-bold">
                            <th class="px-4 py-3 border-b border-gray-200">Image</th>
                            <th class="px-4 py-3 border-b border-gray-200">Title</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
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

    @push('scripts')
    <x-datatable-init table-id="newsSectionTable" />
    @endpush

@endsection