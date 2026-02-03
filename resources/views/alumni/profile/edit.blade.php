@extends('layouts.alumni')

@section('title', 'Edit Profile')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fdfdfd; }
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
        .form-card { transition: all 0.5s ease; }
        .form-card:hover { box-shadow: 0 30px 60px -12px rgba(0, 40, 94, 0.08); }
        .animated-gradient {
            background: linear-gradient(-45deg, #00285E, #0a3d7a, #1e40af, #3b82f6);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>

    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 relative overflow-hidden -mt-8">
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>

        <div class="max-w-5xl mx-auto relative z-10">
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Edit Profile</h2>
                <div class="flex items-center gap-3">
                    <a href="{{ route('alumni.dashboard') }}" class="flex items-center gap-2 text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors group">
                        <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data" action="{{ route('alumni.profile.update') }}" id="profile-form" class="space-y-10">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                    <div class="lg:col-span-4 space-y-8">
                        <div class="form-card bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/40 text-center">
                            <h3 class="text-sm font-extrabold text-gray-400 uppercase tracking-widest mb-8">Profile Image</h3>
                            <div class="relative inline-block group">
                                <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-cyan-400 rounded-full blur opacity-25 group-hover:opacity-60 transition duration-1000"></div>
                                <div id="profile-image-frame" class="relative w-40 h-40 rounded-full overflow-hidden border-4 border-white shadow-lg bg-gray-50 mx-auto">
                                    <img id="profile-image-preview" src="{{ $alumni->image ? asset('storage/' . $alumni->image) : '' }}" alt="Profile" class="w-full h-full object-cover {{ $alumni->image ? '' : 'hidden' }}" data-initial-src="{{ $alumni->image ? asset('storage/' . $alumni->image) : '' }}">
                                    <div id="profile-image-initial" class="absolute inset-0 flex items-center justify-center text-4xl text-gray-300 font-bold {{ $alumni->image ? 'hidden' : '' }}">{{ $alumni->first_name[0] }}</div>
                                </div>
                            </div>
                            <div class="mt-8">
                                <label class="block">
                                    <span class="sr-only">Choose profile photo</span>
                                    <input type="file" name="image" id="profile-image-input" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-900 file:text-white hover:file:bg-blue-700 transition-all cursor-pointer" />
                                </label>
                                <p class="mt-3 text-[10px] text-gray-400 font-bold uppercase tracking-tight">JPG, PNG or WEBP (Max 2MB)</p>
                            </div>
                        </div>

                        <div class="form-card bg-gray-900 p-8 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden group">
                            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all"></div>
                            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-6 relative z-10">Professional Document</h3>
                            @if ($alumni->document)
                                <div class="mb-6 flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/10 relative z-10">
                                    <div class="p-3 bg-blue-600 rounded-xl">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-xs font-bold truncate">Current document</p>
                                        <a href="{{ asset('storage/' . $alumni->document) }}" target="_blank" class="text-[10px] text-blue-400 font-extrabold uppercase hover:text-blue-300 transition-colors">Review Current</a>
                                    </div>
                                </div>
                            @endif
                            <label class="relative z-10 block">
                                <input type="file" name="document" class="block w-full text-xs text-gray-400 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-white/10 file:text-white hover:file:bg-white/20 transition-all cursor-pointer" />
                            </label>
                        </div>
                    </div>

                    <div class="lg:col-span-8 space-y-8">
                        <div class="form-card bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/40">
                            <div class="flex items-center gap-4 mb-10">
                                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-[#00285E]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <h3 class="text-xl font-extrabold text-gray-900">Personal Identity</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="relative">
                                    <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">First Name</label>
                                    <input name="first_name" value="{{ old('first_name', $alumni->first_name) }}" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700" required>
                                </div>
                                <div class="relative">
                                    <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Last Name</label>
                                    <input name="last_name" value="{{ old('last_name', $alumni->last_name) }}" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700" required>
                                </div>
                                <div class="relative">
                                    <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Graduation Year</label>
                                    <input name="graduation_year" type="number" value="{{ old('graduation_year', $alumni->graduation_year) }}" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700" required>
                                </div>
                                <div class="relative">
                                    <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Marital Status</label>
                                    <select name="status" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700 appearance-none" required>
                                        <option value="single" @selected($alumni->status == 'single')>Single</option>
                                        <option value="married" @selected($alumni->status == 'married')>Married</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-card bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/40">
                            <div class="flex items-center gap-4 mb-10">
                                <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <h3 class="text-xl font-extrabold text-gray-900">Career & Institution</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="md:col-span-2 relative">
                                    <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">University / College</label>
                                    <input name="college" value="{{ old('college', $alumni->college) }}" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700" required>
                                </div>
                                <div class="relative">
                                    <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Degree Program</label>
                                    <input name="degree" value="{{ old('degree', $alumni->degree) }}" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700" required>
                                </div>
                                <div class="relative">
                                    <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Current Company</label>
                                    <input name="company" value="{{ old('company', $alumni->company) }}" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700">
                                </div>
                                <div class="relative">
                                    <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Current Job Title</label>
                                    <input name="job_title" value="{{ old('job_title', $alumni->job_title) }}" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Reach & Location and Achievements: full-width row, side by side --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-10">
                    <div class="form-card bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/40">
                        <div class="flex items-center gap-4 mb-10">
                            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <h3 class="text-xl font-extrabold text-gray-900">Reach & Location</h3>
                        </div>
                        <div class="grid grid-cols-1 gap-6">
                            <div class="relative">
                                <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Email</label>
                                <input name="email" type="email" value="{{ old('email', $alumni->email) }}" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700" required>
                            </div>
                            <div class="relative">
                                <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Phone</label>
                                <input name="phone" value="{{ old('phone', $alumni->phone) }}" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700" required>
                            </div>
                            <div class="relative">
                                <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Address</label>
                                <input name="address" value="{{ old('address', $alumni->address) }}" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700" required>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="relative">
                                    <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">City</label>
                                    <input name="city" value="{{ old('city', $alumni->city) }}" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700" required>
                                </div>
                                <div class="relative">
                                    <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">State</label>
                                    <input name="state" value="{{ old('state', $alumni->state) }}" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700" required>
                                </div>
                                <div class="relative">
                                    <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Zipcode</label>
                                    <input name="zipcode" value="{{ old('zipcode', $alumni->zipcode) }}" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-card bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/40">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                            </div>
                            <h3 class="text-xl font-extrabold text-gray-900">Achievements</h3>
                        </div>
                        <textarea name="achievements" rows="8" class="glass-input w-full px-6 py-4 rounded-2xl border border-gray-100 outline-none font-bold text-gray-700" placeholder="List your achievements...">{{ old('achievements', $alumni->achievements) }}</textarea>
                    </div>
                </div>

                {{-- Publish Changes below Reach & Location and Achievements --}}
                <div class="flex justify-end mt-10">
                    <button type="submit" class="animated-gradient cursor-pointer px-8 py-3 rounded-xl text-white text-base font-bold shadow-lg shadow-blue-900/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 tracking-tight">
                        Publish Changes
                    </button>
                </div>
            </form>

            {{-- Separate form: Change Password only --}}
            <div class="mt-10 form-card bg-white p-10 rounded-[2.5rem] border-2 border-amber-200 shadow-xl shadow-amber-100/30">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-gray-900">Change Password</h3>
                </div>
                <p class="text-sm text-gray-600 mb-6">Both fields are required. Password must be at least 8 characters and must match.</p>
                <div id="password-validation-msg" class="hidden mb-4 p-4 rounded-xl text-sm font-medium"></div>
                <form method="POST" action="{{ route('alumni.profile.update-password') }}" id="password-form">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="relative">
                            <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">New Password</label>
                            <div class="relative">
                                <input type="password" name="password" id="password-field" autocomplete="new-password" class="glass-input w-full px-6 py-4 pr-12 rounded-2xl border border-amber-200 outline-none font-bold text-gray-700" placeholder="Min 8 characters" required>
                                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none p-1" id="toggle-password" title="Show password" aria-label="Show password">
                                    <svg id="eye-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg id="eye-slash-password" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.584 10.587a2 2 0 002.828 2.826M9.363 5.365A9.466 9.466 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-1.362 2.235m-2.181 2.182A9.966 9.966 0 0112 19c-4.478 0-8.268-2.943-9.543-7"></path></svg>
                                </button>
                            </div>
                        </div>
                        <div class="relative">
                            <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest absolute -top-2 left-4 px-2 bg-white z-10">Confirm Password</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password-confirm-field" autocomplete="new-password" class="glass-input w-full px-6 py-4 pr-12 rounded-2xl border border-amber-200 outline-none font-bold text-gray-700" placeholder="Repeat password" required>
                                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none p-1" id="toggle-password-confirm" title="Show password" aria-label="Show password">
                                    <svg id="eye-password-confirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg id="eye-slash-password-confirm" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.584 10.587a2 2 0 002.828 2.826M9.363 5.365A9.466 9.466 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-1.362 2.235m-2.181 2.182A9.966 9.966 0 0112 19c-4.478 0-8.268-2.943-9.543-7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="w-full py-4 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white text-lg font-extrabold shadow-lg shadow-amber-200/50 hover:scale-[1.01] active:scale-[0.99] transition-all duration-300 tracking-tight">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.getElementById('profile-image-input');
            var preview = document.getElementById('profile-image-preview');
            var initial = document.getElementById('profile-image-initial');
            var initialSrc = preview.getAttribute('data-initial-src') || '';

            if (input) {
                input.addEventListener('change', function() {
                    var file = this.files && this.files[0];
                    if (file && file.type.indexOf('image/') === 0) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                            if (initial) initial.classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        preview.src = initialSrc;
                        if (initialSrc) {
                            preview.classList.remove('hidden');
                            if (initial) initial.classList.add('hidden');
                        } else {
                            preview.classList.add('hidden');
                            if (initial) initial.classList.remove('hidden');
                        }
                    }
                });
            }

            // Show/hide password toggles
            function setupPasswordToggle(btnId, fieldId, eyeId, eyeSlashId) {
                var btn = document.getElementById(btnId);
                var field = document.getElementById(fieldId);
                var eye = document.getElementById(eyeId);
                var eyeSlash = document.getElementById(eyeSlashId);
                if (!btn || !field) return;
                btn.addEventListener('click', function() {
                    var isPassword = field.type === 'password';
                    field.type = isPassword ? 'text' : 'password';
                    if (eye) eye.classList.toggle('hidden', isPassword);
                    if (eyeSlash) eyeSlash.classList.toggle('hidden', !isPassword);
                    btn.setAttribute('title', isPassword ? 'Hide password' : 'Show password');
                });
            }
            setupPasswordToggle('toggle-password', 'password-field', 'eye-password', 'eye-slash-password');
            setupPasswordToggle('toggle-password-confirm', 'password-confirm-field', 'eye-password-confirm', 'eye-slash-password-confirm');

            // Client-side password validation on password form submit
            var passwordForm = document.getElementById('password-form');
            var msgEl = document.getElementById('password-validation-msg');
            if (passwordForm && msgEl) {
                passwordForm.addEventListener('submit', function(e) {
                    var pwd = (document.getElementById('password-field') || {}).value || '';
                    var confirm = (document.getElementById('password-confirm-field') || {}).value || '';
                    msgEl.classList.add('hidden');
                    msgEl.classList.remove('bg-red-100', 'text-red-700', 'border-red-200');

                    var err = [];
                    if (pwd.length < 8) err.push('Password must be at least 8 characters.');
                    if (pwd !== confirm) err.push('Password and Confirm password do not match.');
                    if (err.length) {
                        msgEl.textContent = err.join(' ');
                        msgEl.classList.remove('hidden');
                        msgEl.classList.add('bg-red-100', 'text-red-700', 'border', 'border-red-200');
                        e.preventDefault();
                        msgEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return false;
                    }
                });
            }
        });
    </script>
@endsection
