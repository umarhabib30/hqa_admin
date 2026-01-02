@extends('layouts.layout')
@section('content')

<div>
    <div class="flex justify-between mb-6 md:flex-row flex-col">
        <h1 class="text-2xl font-semibold">Home Modals</h1>

        <a href="{{ route('homeModal.create') }}"
            class="px-6 py-3 border-2 border-[#00285E] text-[#00285E] rounded-xl
                  hover:bg-[#00285E] hover:text-white transition">
            + Add Modal
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100 text-sm">
                <tr>
                    <th class="p-3 text-left">Title</th>
                    <th class="p-3">Image</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($modals as $modal)
                <tr class="border-t">
                    <td class="p-3">{{ $modal->title }}</td>

                    <td class="p-3 text-center">
                        @if($modal->image)
                        <img src="{{ asset('storage/'.$modal->image) }}"
                            class="w-16 h-16 object-cover rounded mx-auto">
                        @else
                        —
                        @endif
                    </td>

                    <td class="p-3 flex gap-2 justify-center">
                        <a href="{{ route('homeModal.edit',$modal->id) }}"
                            class="px-3 py-1 border border-[#00285E] text-[#00285E] rounded">
                            Edit
                        </a>

                        <form method="POST"
                            action="{{ route('homeModal.destroy',$modal->id) }}">
                            @csrf @method('DELETE')
                            <button
                                onclick="return confirm('Delete modal?')"
                                class="px-3 py-1 border border-red-600 text-red-600 rounded">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection