@extends('layouts.layout')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fdfdfd;
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-input:focus {
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(0, 40, 94, 0.1);
        }

        .form-card {
            transition: all 0.5s ease;
        }

        .form-card:hover {
            box-shadow: 0 30px 60px -12px rgba(0, 40, 94, 0.08);
        }

        .animated-gradient {
            background: linear-gradient(-45deg, #00285E, #0a3d7a, #1e40af, #3b82f6);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>

    <div class="min-h-screen py-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>

        <div class="max-w-5xl mx-auto relative z-10">
            <div class="mb-16 flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="animate-fade-in">
                    <span
                        class="inline-block px-4 py-1.5 mb-4 text-xs font-extrabold tracking-widest text-blue-600 uppercase bg-blue-50 rounded-full">
                        Profile Management
                    </span>
                    <h2 class="text-5xl font-extrabold text-gray-900 tracking-tighter">
                        Refine Your Legacy
                    </h2>
                    <p class="mt-4 text-gray-500 font-medium text-lg max-w-xl">
                        Keep your professional footprint current within the global alumni network.
                    </p>
                </div>

                <a href="{{ route('alumniForm.index') }}"
                    class="flex items-center gap-2 text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors group">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Return to Directory
                </a>
            </div>

            <form method="POST" enctype="multipart/form-data" action="{{ route('alumniForm.update', $form->id) }}"
                class="space-y-10">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                    <div class="lg:col-span-4 space-y-8">
                        <div
                            class="form-card bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/40 text-center">
                            <h3 class="text-sm font-extrabold text-gray-400 uppercase tracking-widest mb-8">Profile Image
                            </h3>

                            <div class="relative inline-block group">
                                <div
                                    class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-cyan-400 rounded-full blur opacity-25 group-hover:opacity-60 transition duration-1000">
                                </div>
                                <div
                                    class="relative w-40 h-40 rounded-full overflow-hidden border-4 border-white shadow-lg bg-gray-50">
                                    @if ($form->image)
                                        <img src="{{ asset('storage/' . $form->image) }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div
                                            class="w-full h-full flex items-center justify-center text-4xl text-gray-300 font-bold">
                                            {{ $form->first_name[0] }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-8">
                                <label class="block">
                                    <span class="sr-only">Choose profile photo</span>
                                    <input type="file" name="image"
                                        class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-900 file:text-white hover:file:bg-blue-700 transition-all cursor-pointer" />
                                </label>
                                <p class="mt-3 text-[10px] text-gray-400 font-bold uppercase tracking-tight">JPG, PNG or
                                    WEBP (Max 2MB)</p>
                            </div>
                        </div>

                        <div
                            class="form-card bg-gray-900 p-8 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden group">
                            <div
                                class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all">
                            </div>
                            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-6 relative z-10">
                                Professional Document</h3>

                            @if ($form->document)
                                <div
                                    class="mb-6 flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/10 relative z-10">
                                    <div class="p-3 bg-blue-600 rounded-xl">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-xs font-bold truncate">Current_CV.pdf</p>
                                        <a href="{{ asset('storage/' . $form->document) }}" target="_blank"
                                            class="text-[10px] text-blue-400 font-extrabold uppercase hover:text-blue-300 transition-colors">Review
                                            Current</a>
                                    </div>
                                </div>
                            @endif

                            <label class="relative z-10 block">
                                <input type="file" name="document"
                                    class="block w-full text-xs text-gray-400 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-white/10 file:text-white hover:file:bg-white/20 transition-all cursor-pointer" />
                            </label>
                        </div>
                    </div>

                    <div class="lg:col-span-8 space-y-8">

                        <div
                            class="form-card bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/40">
                            <div class="flex items-center gap-4 mb-10">
                                <div
                                    class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-[#00285E]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-extrabold text-gray-900">Personal Identity</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="relative">
                                    <label
                                        class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">First
                                        Name</label>
                                    <input name="first_name" value="{{ old('first_name', $form->first_name) }}"
                                        class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700">
                                </div>
                                <div class="relative">
                                    <label
                                        class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Last
                                        Name</label>
                                    <input name="last_name" value="{{ old('last_name', $form->last_name) }}"
                                        class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700">
                                </div>
                                <div class="relative">
                                    <label
                                        class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Graduation
                                        Year</label>
                                    <input name="graduation_year" type="number"
                                        value="{{ old('graduation_year', $form->graduation_year) }}"
                                        class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700">
                                </div>
                                <div class="relative">
                                    <label
                                        class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Marital
                                        Status</label>
                                    <select name="status"
                                        class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700 appearance-none">
                                        <option value="single" @selected($form->status == 'single')>Single</option>
                                        <option value="married" @selected($form->status == 'married')>Married</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div
                            class="form-card bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/40">
                            <div class="flex items-center gap-4 mb-10">
                                <div
                                    class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-extrabold text-gray-900">Career & Institution</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="md:col-span-2 relative">
                                    <label
                                        class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">University
                                        / College</label>
                                    <input name="college" value="{{ old('college', $form->college) }}"
                                        class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700">
                                </div>
                                <div class="relative">
                                    <label
                                        class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Degree
                                        Program</label>
                                    <input name="degree" value="{{ old('degree', $form->degree) }}"
                                        class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700">
                                </div>
                                <div class="relative">
                                    <label
                                        class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Current
                                        Job Title</label>
                                    <input name="job_title" value="{{ old('job_title', $form->job_title) }}"
                                        class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700">
                                </div>
                            </div>
                        </div>

                        <div
                            class="form-card bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/40">
                            <div class="flex items-center gap-4 mb-10">
                                <div
                                    class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-extrabold text-gray-900">Reach & Location</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-6 gap-8">
                                <div class="md:col-span-3 relative">
                                    <label
                                        class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Email</label>
                                    <input name="email" value="{{ old('email', $form->email) }}"
                                        class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700">
                                </div>
                                <div class="md:col-span-3 relative">
                                    <label
                                        class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Phone</label>
                                    <input name="phone" value="{{ old('phone', $form->phone) }}"
                                        class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700">
                                </div>
                                <div class="md:col-span-6 relative">
                                    <label
                                        class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Address</label>
                                    <input name="address" value="{{ old('address', $form->address) }}"
                                        class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700">
                                </div>
                            </div>
                        </div>

                        <div class="pt-10">
                            <button type="submit"
                                class="animated-gradient w-full cursor-pointer py-6 rounded-2xl text-white text-xl font-extrabold shadow-2xl shadow-blue-900/40 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 tracking-tight">
                                Publish Changes
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
