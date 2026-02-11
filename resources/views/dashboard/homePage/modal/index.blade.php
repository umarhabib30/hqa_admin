@extends('layouts.layout')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>


@section('content')
    <div class="w-full px-6 py-10">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight leading-none">
                    Home <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-[#00285E] to-blue-600">PopUps</span>
                </h1>
                <p class="text-gray-500 mt-2 font-medium">Elevate your user engagement with custom alerts.</p>
            </div>

            <a href="{{ route('homeModal.create') }}"
                class="group relative inline-flex items-center justify-center gap-3 px-8 py-4 bg-[#00285E] text-white font-bold rounded-2xl shadow-xl shadow-blue-900/30 hover:bg-[#001d44] transition-all active:scale-95 overflow-hidden">
                <div
                    class="absolute inset-0 w-full h-full bg-white/10 group-hover:left-full -left-full transition-all duration-500 skew-x-12">
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add New Modal</span>
            </a>
        </div>

        {{-- Main Content Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto p-4">
                <table id="homeModalTable" class="display w-full text-left" style="width:100%">
                    <thead>
                        <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider font-bold">
                            <th class="px-4 py-3 border-b border-gray-200">Visual</th>
                            <th class="px-4 py-3 border-b border-gray-200">Details</th>
                            <th class="px-4 py-3 border-b border-gray-200">Button Link</th>
                            <th class="px-4 py-3 border-b border-gray-200">General Link</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-right">Settings</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($modals as $modal)
                            <tr class="group hover:bg-[#00285E]/[0.02] transition-all duration-300">
                                {{-- Visual Preview --}}
                                <td class="px-6 py-6">
                                    <div class="relative w-16 h-16 group-hover:scale-110 transition-transform duration-300">
                                        @if ($modal->image)
                                            <img src="{{ asset('storage/' . $modal->image) }}" alt="{{ $modal->title }}"
                                                class="w-full h-full object-cover rounded-xl shadow-sm border border-gray-100">
                                        @else
                                            <div
                                                class="w-full h-full rounded-xl bg-gray-50 flex items-center justify-center text-gray-300 border border-dashed border-gray-200">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Details --}}
                                <td class="px-6 py-6">
                                    <div class="max-w-xs">
                                        <h3 class="text-sm font-bold text-gray-900 line-clamp-1">{{ $modal->title }}</h3>
                                        <p class="text-xs text-gray-400 mt-1 line-clamp-1 italic">
                                            {{ $modal->cdesc ?? 'No description' }}</p>
                                    </div>
                                </td>

                                {{-- Column 3: Button Link --}}
                                <td class="px-6 py-6">
                                    @if ($modal->btn_text)
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-[10px] font-black text-[#00285E] uppercase tracking-tighter">{{ $modal->btn_text }}</span>
                                            <a href="{{ $modal->btn_link ?? '#' }}" target="_blank"
                                                class="text-[11px] text-blue-500 hover:underline truncate max-w-[120px]">
                                                {{ $modal->btn_link ?? 'No Link' }}
                                            </a>
                                        </div>
                                    @else
                                        <span
                                            class="text-gray-300 text-[10px] uppercase font-bold tracking-widest">N/A</span>
                                    @endif
                                </td>

                                {{-- Column 4: General Link --}}
                                <td class="px-6 py-6">
                                    @if ($modal->general_link)
                                        <a href="{{ $modal->general_link }}" target="_blank"
                                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-lg border border-emerald-100 hover:bg-emerald-100 transition-colors">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Visit Link
                                        </a>
                                    @else
                                        <span class="text-gray-300 text-[10px] uppercase font-bold tracking-widest">No
                                            Link</span>
                                    @endif
                                </td>

                                {{-- Settings --}}
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('homeModal.edit', $modal->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 text-sm font-bold text-[#00285E] bg-white border-2 border-[#00285E] rounded-lg hover:bg-[#00285E] hover:text-white transition-all">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('homeModal.destroy', $modal->id) }}"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Archive this modal?')"
                                                class="inline-flex items-center px-3 py-1.5 text-sm font-bold text-red-600 bg-white border-2 border-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-24 text-center">
                                    <div class="max-w-xs mx-auto text-gray-400">
                                        <h3 class="text-lg font-bold text-gray-900">Queue is empty</h3>
                                        <p class="text-sm mt-1">Start by clicking the 'Add New Modal' button.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <x-datatable-init table-id="homeModalTable" />
    @endpush
@endsection
