@extends('layouts.layout')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>


@section('content')
    <div class="max-w-4xl mx-auto px-6 py-12">
        {{-- Breadcrumb & Title --}}
        <div class="mb-10">
            <a href="{{ route('homeModal.index') }}"
                class="inline-flex items-center gap-2 text-gray-400 hover:text-[#00285E] font-bold text-xs uppercase tracking-widest transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Dashboard
            </a>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
                Edit <span class="text-[#00285E]">Modal</span>
            </h1>
            <p class="text-gray-500 mt-2 font-medium">Updating: <span
                    class="text-gray-800 italic">"{{ $homeModal->title }}"</span></p>
        </div>

        <form method="POST" enctype="multipart/form-data" action="{{ route('homeModal.update', $homeModal->id) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Left Column: Content --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 border border-gray-100 p-8">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#00285E] flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h3 class="font-black text-gray-800 uppercase tracking-widest text-sm">Edit Content</h3>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label
                                    class="block text-[10px] text-gray-400 uppercase font-black tracking-[0.2em] mb-2 ml-1">Modal
                                    Title <span class="text-red-500">*</span></label>
                                <input name="title" value="{{ old('title', $homeModal->title) }}" required
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-2xl focus:bg-white focus:ring-4 focus:ring-[#00285E]/5 focus:border-[#00285E] transition-all outline-none font-medium">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] text-gray-400 uppercase font-black tracking-[0.2em] mb-2 ml-1">Description</label>
                                <textarea name="cdesc" rows="5"
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-2xl focus:bg-white focus:ring-4 focus:ring-[#00285E]/5 focus:border-[#00285E] transition-all outline-none font-medium">{{ old('cdesc', $homeModal->cdesc) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 border border-gray-100 p-8">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#00285E] flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="font-black text-gray-800 uppercase tracking-widest text-sm">Buttons & Links</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label
                                    class="block text-[10px] text-gray-400 uppercase font-black tracking-[0.2em] mb-2 ml-1">Button
                                    Label</label>
                                <input name="btn_text" value="{{ old('btn_text', $homeModal->btn_text) }}"
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-2xl focus:bg-white focus:ring-4 focus:ring-[#00285E]/5 focus:border-[#00285E] transition-all outline-none font-medium">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] text-gray-400 uppercase font-black tracking-[0.2em] mb-2 ml-1">Button
                                    URL</label>
                                <input name="btn_link" value="{{ old('btn_link', $homeModal->btn_link) }}"
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-2xl focus:bg-white focus:ring-4 focus:ring-[#00285E]/5 focus:border-[#00285E] transition-all outline-none font-medium">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] text-gray-400 uppercase font-black tracking-[0.2em] mb-2 ml-1">General
                                    Link</label>
                                <input name="general_link" value="{{ old('general_link', $homeModal->general_link) }}"
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-2xl focus:bg-white focus:ring-4 focus:ring-[#00285E]/5 focus:border-[#00285E] transition-all outline-none font-medium">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Media Preview & Actions --}}
                <div class="space-y-6">
                    <div
                        class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 border border-gray-100 p-8 text-center">
                        <h3 class="font-black text-gray-800 uppercase tracking-widest text-xs mb-6">Current Visual</h3>

                        @if ($homeModal->image)
                            <div class="relative mb-6 group">
                                <img src="{{ asset('storage/' . $homeModal->image) }}"
                                    class="w-full aspect-square object-cover rounded-3xl shadow-lg">
                                <div
                                    class="absolute inset-0 bg-black/40 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="text-white text-xs font-bold uppercase tracking-widest">Replace
                                        Below</span>
                                </div>
                            </div>
                        @endif

                        <div class="relative">
                            <div
                                class="w-full py-6 rounded-2xl bg-gray-50 border-2 border-dashed border-gray-200 flex flex-col items-center justify-center p-4">
                                <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Upload New</p>
                                <input type="file" name="image" class="absolute inset-0 opacity-0 cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#00285E] rounded-[2rem] shadow-xl shadow-blue-900/30 p-8 text-white">
                        <h3 class="font-bold text-sm uppercase tracking-widest mb-4 opacity-80">Update Settings</h3>
                        <p class="text-xs leading-relaxed opacity-60 mb-8">Saving these changes will instantly reflect on
                            the live website.</p>

                        <div class="space-y-3">
                            <button type="submit"
                                class="w-full py-4 bg-white text-[#00285E] font-black rounded-2xl hover:bg-blue-50 transition-all active:scale-95 shadow-lg">
                                Save Changes
                            </button>
                            <a href="{{ route('homeModal.index') }}"
                                class="block w-full py-4 bg-transparent text-center border border-white/20 text-white font-bold rounded-2xl hover:bg-white/10 transition-all">
                                Cancel Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
