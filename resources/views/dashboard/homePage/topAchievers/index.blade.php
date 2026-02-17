@extends('layouts.layout')
@section('content')

<div>

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
            Home Top Achievers
        </h1>

        <a href="{{ route('topAchievers.create') }}"
            class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-blue-600
                   text-blue-600 font-semibold
                   hover:bg-blue-600 hover:text-white
                   transition active:scale-95">
            + Add Achiever
        </a>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto p-4">
            <table id="topAchieversTable" class="display w-full text-left" style="width:100%">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider font-bold">
                        <th class="px-4 py-3 border-b border-gray-200">Main Image</th>
                        <th class="px-4 py-3 border-b border-gray-200">Name</th>
                        <th class="px-4 py-3 border-b border-gray-200">Class</th>
                        <th class="px-4 py-3 border-b border-gray-200">Meta Entries</th>
                        <th class="px-4 py-3 border-b border-gray-200 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($achievers as $achiever)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4">
                        @if($achiever->image)
                        <img
                            src="{{ Storage::url($achiever->image) }}"
                            class="w-16 h-16 object-cover rounded-lg border"
                            alt="Achiever Image">
                        @else
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-500">
                            N/A
                        </div>
                        @endif
                    </td>

                    {{-- <td class="p-4 font-medium">{{ $achiever->title }}</td> --}}
                    <td class="p-4">{{ $achiever->achiever_name }}</td>
                    <td class="p-4">{{ $achiever->class_achiever }}</td>

                    <td class="p-4 space-y-1">
                        @foreach($achiever->meta_data ?? [] as $meta)
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 text-xs bg-gray-100 rounded">
                                {{ $meta['title'] }}
                            </span>
                            @if(!empty($meta['image']))
                            <img
                                src="{{ Storage::url($meta['image']) }}"
                                class="w-10 h-10 object-cover rounded border">
                            @endif
                        </div>
                        @endforeach
                    </td>

                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('topAchievers.edit', $achiever->id) }}"
                                class="px-3 py-1 rounded
                                       border border-[00285E] text-[00285E]
                                       hover:bg-[00285E] hover:text-white transition">
                                Edit
                            </a>

                            <form action="{{ route('topAchievers.destroy', $achiever->id) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <button
                                    onclick="return confirm('Are you sure?')"
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
                        No achievers found.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <x-datatable-init table-id="topAchieversTable" />
    @endpush

    <!-- MOBILE CARDS -->
    <div class="md:hidden space-y-4">

        @forelse($achievers as $achiever)
        <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">

            <div class="flex gap-3 items-center">
                @if($achiever->image)
                <img
                    src="{{ Storage::url($achiever->image) }}"
                    class="w-16 h-16 rounded-lg object-cover border">
                @else
                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-500">
                    N/A
                </div>
                @endif

                <div>
                    <h3 class="font-semibold text-gray-800">
                        {{ $achiever->title }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ $achiever->achiever_name }} · {{ $achiever->class_achiever }}
                    </p>
                </div>
            </div>

            @if(!empty($achiever->meta_data))
            <div class="space-y-2">
                @foreach($achiever->meta_data as $meta)
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 text-xs bg-gray-100 rounded">
                        {{ $meta['title'] }}
                    </span>
                    @if(!empty($meta['image']))
                    <img
                        src="{{ Storage::url($meta['image']) }}"
                        class="w-10 h-10 rounded object-cover border">
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            <div class="flex gap-2 pt-2">
                <a href="{{ route('topAchievers.edit', $achiever->id) }}"
                    class="flex-1 text-center px-4 py-2 rounded-lg
                           border border-[00285E] text-[00285E]
                           hover:bg-[00285E] hover:text-white transition">
                    Edit
                </a>

                <form action="{{ route('topAchievers.destroy', $achiever->id) }}"
                    method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button
                        onclick="return confirm('Are you sure?')"
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
            No achievers found.
        </div>
        @endforelse

    </div>

</div>

@endsection