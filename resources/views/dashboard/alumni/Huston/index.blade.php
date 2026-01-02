@extends('layouts.layout')
@section('content')

<div>

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
            Alumni Houston
        </h1>

        <a href="{{ route('alumniHuston.create') }}"
            class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-[#00285E]
                   text-[#00285E] font-semibold
                   hover:bg-[#00285E] hover:text-white
                   transition active:scale-95">
            + Add Alumni
        </a>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block bg-white rounded-xl shadow overflow-hidden">

        <table class="w-full border-collapse">
            <thead class="bg-gray-100 text-sm text-gray-700">
                <tr>
                    <th class="p-4 text-center">Image</th>
                    <th class="p-4 text-left">Name</th>
                    <th class="p-4 text-left">Profession</th>
                    <th class="p-4 text-center">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($alumni as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 text-center">
                        @if($item->image)
                        <img
                            src="{{ asset('storage/'.$item->image) }}"
                            class="w-12 h-12 rounded-full object-cover mx-auto border">
                        @else
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500 mx-auto">
                            N/A
                        </div>
                        @endif
                    </td>

                    <td class="p-4 font-medium">
                        {{ $item->name }}
                    </td>

                    <td class="p-4">
                        {{ $item->profession }}
                    </td>

                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('alumniHuston.edit',$item->id) }}"
                                class="px-3 py-1 rounded
                                       border border-yellow-500 text-yellow-600
                                       hover:bg-yellow-500 hover:text-white transition">
                                Edit
                            </a>

                            <form method="POST"
                                action="{{ route('alumniHuston.destroy',$item->id) }}">
                                @csrf
                                @method('DELETE')
                                <button
                                    class="px-3 py-1 rounded
                                           border border-red-500 text-red-600
                                           hover:bg-red-500 hover:text-white transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-6 text-center text-gray-500">
                        No alumni found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <!-- MOBILE CARDS -->
    <div class="md:hidden space-y-4">

        @forelse($alumni as $item)
        <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">

            <div class="flex gap-3 items-center">
                @if($item->image)
                <img
                    src="{{ asset('storage/'.$item->image) }}"
                    class="w-14 h-14 rounded-full object-cover border">
                @else
                <div class="w-14 h-14 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500">
                    N/A
                </div>
                @endif

                <div>
                    <h3 class="font-semibold text-gray-800">
                        {{ $item->name }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ $item->profession }}
                    </p>
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <a href="{{ route('alumniHuston.edit',$item->id) }}"
                    class="flex-1 text-center px-4 py-2 rounded-lg
                           border border-yellow-500 text-yellow-600
                           hover:bg-yellow-500 hover:text-white transition">
                    Edit
                </a>

                <form method="POST"
                    action="{{ route('alumniHuston.destroy',$item->id) }}"
                    class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button
                        class="w-full px-4 py-2 rounded-lg
                               border border-red-500 text-red-600
                               hover:bg-red-500 hover:text-white
                               transition active:scale-95">
                        Delete
                    </button>
                </form>
            </div>

        </div>
        @empty
        <div class="text-center text-gray-500 py-8">
            No alumni found.
        </div>
        @endforelse

    </div>

</div>
@endsection