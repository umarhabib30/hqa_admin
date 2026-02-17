@extends('layouts.layout')
@section('content')

<div>

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
            Home Alumni
        </h1>

        <a href="{{ route('memories.create') }}"
            class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-[#00285E]
                   text-[#00285E] font-semibold
                   hover:bg-[#00285E] hover:text-white
                   transition active:scale-95">
            + Add Memory
        </a>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto p-4">
            <table id="memoriesTable" class="display w-full text-left" style="width:100%">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider font-bold">
                       
                        <th class="px-4 py-3 border-b border-gray-200">Image</th>
                        <th class="px-4 py-3 border-b border-gray-200">Name</th>
                        <th class="px-4 py-3 border-b border-gray-200">Graduated</th>
                        <th class="px-4 py-3 border-b border-gray-200">Quote</th>
                        <th class="px-4 py-3 border-b border-gray-200 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($memories as $memory)
                <tr class="hover:bg-gray-50 transition">
                  

                    <td class="p-4">
                        @if($memory->image)
                        <img
                            src="{{ Storage::url($memory->image) }}"
                            class="w-12 h-12 rounded-lg object-cover border"
                            alt="Memory Image">
                        @else
                        <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center text-xs text-gray-500">
                            N/A
                        </div>
                        @endif
                    </td>

                    <td class="p-4">{{ $memory->name }}</td>
                    <td class="p-4">{{ $memory->graduated }}</td>

                    <td class="p-4 text-sm italic text-gray-600 max-w-xs truncate">
                        “{{ Str::limit($memory->quote, 50) }}”
                    </td>

                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('memories.edit',$memory->id) }}"
                                class="px-3 py-1 rounded
                                       border border-[00285E] text-[00285E]
                                       hover:bg-[00285E] hover:text-white transition">
                                Edit
                            </a>

                            <form action="{{ route('memories.destroy',$memory->id) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <button
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
                    <td colspan="6" class="p-6 text-center text-gray-500">
                        No memories found.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <x-datatable-init table-id="memoriesTable" />
    @endpush

    <!-- MOBILE CARDS -->
    <div class="md:hidden space-y-4">

        @forelse($memories as $memory)
        <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">

            <div class="flex gap-3 items-center">
                @if($memory->image)
                <img
                    src="{{ Storage::url($memory->image) }}"
                    class="w-14 h-14 rounded-lg object-cover border"
                    alt="Memory Image">
                @else
                <div class="w-14 h-14 rounded-lg bg-gray-200 flex items-center justify-center text-xs text-gray-500">
                    N/A
                </div>
                @endif

                <div>
                    <h3 class="font-semibold text-gray-800">
                        {{ $memory->title }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ $memory->name }} · {{ $memory->graduated }}
                    </p>
                </div>
            </div>

            <div class="text-sm italic text-gray-600">
                “{{ Str::limit($memory->quote, 120) }}”
            </div>

            <div class="flex gap-2 pt-2">
                <a href="{{ route('memories.edit',$memory->id) }}"
                    class="flex-1 text-center px-4 py-2 rounded-lg
                           border border-[00285E] text-[00285E]
                           hover:bg-[00285E] hover:text-white transition">
                    Edit
                </a>

                <form action="{{ route('memories.destroy',$memory->id) }}"
                    method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button
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
            No memories found.
        </div>
        @endforelse

    </div>

</div>

@endsection