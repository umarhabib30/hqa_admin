@extends('layouts.layout')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">

            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 animate-fade-in-down">
                <div>
                    <a href="{{ route('alumniForm.index') }}"
                        class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-[#00285E] transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Directory
                    </a>
                    <h1 class="text-3xl font-extrabold text-gray-900 mt-2">Alumni Profile</h1>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('alumniForm.edit', $form->id) }}"
                        class="flex items-center justify-center px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold shadow-sm hover:bg-gray-50 transition-all duration-200">
                        Edit Profile
                    </a>

                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-1 space-y-6 animate-fade-in-left">
                    <div
                        class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden text-center p-8">
                        <div class="relative inline-block">
                            <div class="w-40 h-40 mx-auto rounded-full p-1 bg-gradient-to-tr from-[#00285E] to-blue-400">
                                @if ($form->image)
                                    <img src="{{ asset('storage/' . $form->image) }}"
                                        class="w-full h-full object-cover rounded-full border-4 border-white shadow-inner">
                                @else
                                    <div
                                        class="w-full h-full bg-gray-100 rounded-full border-4 border-white flex items-center justify-center">
                                        <span
                                            class="text-4xl font-bold text-gray-400">{{ substr($form->first_name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <span
                                class="absolute bottom-2 right-2 w-6 h-6 bg-green-500 border-4 border-white rounded-full"></span>
                        </div>

                        <h2 class="mt-6 text-2xl font-bold text-gray-900">{{ $form->first_name }} {{ $form->last_name }}
                        </h2>
                        <p class="text-[#00285E] font-medium">{{ $form->job_title ?? 'Alumni Member' }}</p>
                        <div
                            class="mt-4 inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-[#00285E] text-xs font-bold uppercase tracking-wider">
                            Class of {{ $form->graduation_year }}
                        </div>

                        <hr class="my-8 border-gray-100">

                        <div class="space-y-4 text-left">
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span class="text-sm truncate">{{ $form->email }}</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                                <span class="text-sm">{{ $form->phone }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#00285E] rounded-3xl p-6 text-white shadow-lg shadow-blue-900/30 group">
                        <h4 class="font-bold mb-2">Professional CV</h4>
                        <p class="text-blue-200 text-xs mb-4">Last updated: {{ $form->updated_at->format('M d, Y') }}</p>
                        @if ($form->document)
                            <a href="{{ asset('storage/' . $form->document) }}" target="_blank"
                                class="flex items-center justify-center w-full py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl transition-all font-bold text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                DOWNLOAD CV
                            </a>
                        @else
                            <p class="text-sm italic opacity-60">No document uploaded</p>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-8 animate-fade-in-right">

                    <section
                        class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                        <div class="bg-gray-50/50 px-8 py-5 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-5 h-5 text-[#00285E]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="font-bold text-gray-800">Professional Experience</h3>
                            </div>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Current Company
                                </p>
                                <p class="text-lg font-semibold text-gray-900">{{ $form->company ?? 'Not Specified' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Job Title</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $form->job_title ?? 'Not Specified' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">University /
                                    College</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $form->college }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Degree Program</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $form->degree }}</p>
                            </div>
                        </div>
                    </section>

                    <section
                        class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                        <div class="bg-gray-50/50 px-8 py-5 border-b border-gray-100 flex items-center gap-3">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-800">Contact & Location</h3>
                        </div>
                        <div class="p-8">
                            <div class="mb-6">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Street Address</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $form->address }}</p>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">City</p>
                                    <p class="font-semibold text-gray-900">{{ $form->city }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">State</p>
                                    <p class="font-semibold text-gray-900">{{ $form->state }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Zipcode</p>
                                    <p class="font-semibold text-gray-900">{{ $form->zipcode }}</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div
                        class="flex items-center justify-between p-6 bg-white rounded-3xl border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-4">
                            <p class="text-sm font-bold text-gray-500 uppercase tracking-widest">Marital Status</p>
                            <span
                                class="px-4 py-1.5 rounded-full text-sm font-bold {{ $form->status == 'single' ? 'bg-green-100 text-green-700' : 'bg-pink-100 text-pink-700' }}">
                                {{ ucfirst($form->status) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 italic font-medium">Profile verified Alumni Network</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.6s ease-out forwards;
        }

        .animate-fade-in-left {
            animation: fadeInLeft 0.8s ease-out forwards;
        }

        .animate-fade-in-right {
            animation: fadeInRight 0.8s ease-out forwards;
        }
    </style>
@endsection
