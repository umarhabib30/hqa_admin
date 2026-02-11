@extends('layouts.layout')

@section('content')

<div>

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
                Achievements
            </h1>

            <div class="text-sm text-gray-500 mt-1">
                Dashboard / Achievements
            </div>
        </div>

        <a href="{{ route('achievements.create') }}"
            class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-[#00285E]
                   text-[#00285E] font-semibold text-lg
                   hover:bg-[#00285E] hover:text-white
                   transition-all duration-300
                   active:scale-95">
            Create
        </a>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto p-4">
            <table id="achievementsTable" class="display w-full text-left" style="width:100%">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider font-bold">
                        <th class="px-4 py-3 border-b border-gray-200">ID</th>
                        <th class="px-4 py-3 border-b border-gray-200">Card Title</th>
                        <th class="px-4 py-3 border-b border-gray-200">Price</th>
                        <th class="px-4 py-3 border-b border-gray-200">Description</th>
                        <th class="px-4 py-3 border-b border-gray-200">Percentage</th>
                        <th class="px-4 py-3 border-b border-gray-200 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($achievements as $achievement)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">{{ $achievement->id }}</td>
                    <td class="px-6 py-4 font-medium">{{ $achievement->card_title }}</td>
                    <td class="px-6 py-4">Rs {{ number_format($achievement->card_price) }}</td>
                    <td class="px-6 py-4 max-w-xs">
                        @if(is_array($achievement->card_desc) && count($achievement->card_desc))
                        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                            @foreach($achievement->card_desc as $point)
                            <li>{{ $point }}</li>
                            @endforeach
                        </ul>
                        @else
                        <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full bg-blue-100 text-[#00285E] font-semibold">
                            {{ $achievement->card_percentage }}%
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('achievements.edit', $achievement->id) }}"
                                class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100">
                                Edit
                            </a>

                            <form action="{{ route('achievements.destroy', $achievement->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-4 py-2 rounded-lg border border-red-500 text-red-500
                                           hover:bg-red-500 hover:text-white transition active:scale-95">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No achievements found.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <x-datatable-init table-id="achievementsTable" />
    @endpush

    <!-- MOBILE CARDS -->
    <div class="md:hidden space-y-4">

        @forelse($achievements as $achievement)
        <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">

            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">
                    {{ $achievement->card_title }}
                </h3>

                <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-[#00285E] font-semibold">
                    {{ $achievement->card_percentage }}%
                </span>
            </div>

            <td class="px-6 py-4 max-w-xs">
                @if(is_array($achievement->card_desc) && count($achievement->card_desc))
                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                    @foreach($achievement->card_desc as $point)
                    <li>{{ $point }}</li>
                    @endforeach
                </ul>
                @else
                <span class="text-gray-400">—</span>
                @endif
            </td>


            <div class="flex justify-between text-sm text-gray-700">
                <span><strong>ID:</strong> {{ $achievement->id }}</span>
                <span><strong>Price:</strong> Rs {{ number_format($achievement->card_price) }}</span>
            </div>

            <div class="flex gap-2 pt-2">
                <a href="{{ route('achievements.edit', $achievement->id) }}"
                    class="flex-1 text-center px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100">
                    Edit
                </a>

                <form action="{{ route('achievements.destroy', $achievement->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full px-4 py-2 rounded-lg border border-red-500 text-red-500
                               hover:bg-red-500 hover:text-white transition active:scale-95">
                        Delete
                    </button>
                </form>
            </div>

        </div>
        @empty
        <div class="text-center text-gray-500 py-8">
            No achievements found.
        </div>
        @endforelse

    </div>

</div>

@endsection