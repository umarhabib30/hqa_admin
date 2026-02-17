@extends('layouts.layout')

@section('content')
<div class="p-4 md:p-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
            PTO Letter Guide Download
        </h1>

        <a href="{{ route('ptoLetterGuide.create') }}"
            class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-[#00285E]
                   text-[#00285E] font-semibold
                   hover:bg-[#00285E] hover:text-white
                   transition active:scale-95">
            + Add PTO Download
        </a>
    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
    <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-700">
        {{ session('success') }}
    </div>
    @endif

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto p-4">
            <table id="ptoLetterTable" class="display w-full text-left" style="width:100%">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider font-bold">
                        <th class="px-4 py-3 border-b border-gray-200">#</th>
                        <th class="px-4 py-3 border-b border-gray-200">Newsletter</th>
                        <th class="px-4 py-3 border-b border-gray-200">Guide</th>
                        <th class="px-4 py-3 border-b border-gray-200 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($items as $key => $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">{{ $key + 1 }}</td>

                    <td class="px-6 py-4">
                        <a href="{{ asset('storage/'.$item->newsletter_download) }}" target="_blank"
                            class="text-[#00285E] hover:underline font-medium">
                            Download Newsletter
                        </a>
                    </td>

                    <td class="px-6 py-4">
                        <a href="{{ asset('storage/'.$item->guide_download) }}" target="_blank"
                            class="text-[#00285E] hover:underline font-medium">
                            Download Guide
                        </a>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-3">
                            <a href="{{ route('ptoLetterGuide.edit', $item->id) }}"
                                class="px-4 py-2 rounded-lg
                                       bg-yellow-100 text-yellow-700
                                       hover:bg-yellow-200 transition">
                                Edit
                            </a>

                            <form action="{{ route('ptoLetterGuide.destroy', $item->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this record?')">
                                @csrf
                                @method('DELETE')
                                <button
                                    class="px-4 py-2 rounded-lg
                                           bg-red-100 text-red-700
                                           hover:bg-red-200 transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                        No PTO downloads found.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <x-datatable-init table-id="ptoLetterTable" />
    @endpush

    <!-- MOBILE CARDS -->
    <div class="md:hidden space-y-4">

        @forelse($items as $key => $item)
        <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">

            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-500">
                    #{{ $key + 1 }}
                </span>
            </div>

            <div class="space-y-2 text-sm">
                <a href="{{ asset('storage/'.$item->newsletter_download) }}" target="_blank"
                    class="block text-[#00285E] font-medium underline">
                    Download Newsletter
                </a>

                <a href="{{ asset('storage/'.$item->guide_download) }}" target="_blank"
                    class="block text-[#00285E] font-medium underline">
                    Download Guide
                </a>
            </div>

            <div class="flex gap-2 pt-2">
                <a href="{{ route('ptoLetterGuide.edit', $item->id) }}"
                    class="flex-1 text-center px-4 py-2 rounded-lg
                           bg-yellow-100 text-yellow-700
                           hover:bg-yellow-200 transition">
                    Edit
                </a>

                <form action="{{ route('ptoLetterGuide.destroy', $item->id) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this record?')"
                    class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button
                        class="w-full px-4 py-2 rounded-lg
                               bg-red-100 text-red-700
                               hover:bg-red-200 transition active:scale-95">
                        Delete
                    </button>
                </form>
            </div>

        </div>
        @empty
        <div class="text-center text-gray-500 py-8">
            No PTO downloads found.
        </div>
        @endforelse

    </div>

</div>
@endsection