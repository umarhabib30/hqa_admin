@extends('layouts.layout')
@section('content')
    <div class="max-w-6xl mx-auto">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#00285E]">Teacher Job Posts</h1>
                <p class="text-gray-500 text-sm">Manage and monitor all active job listings.</p>
            </div>

            <a href="{{ route('jobPost.create') }}"
                class="inline-flex items-center justify-center px-5 py-2.5 bg-[#00285E] text-white font-medium rounded-lg hover:bg-[#001d45] transition-all shadow-md hover:shadow-lg transform active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="20 20 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create New Post
            </a>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="flex items-center p-4 mb-6 text-green-800 rounded-lg bg-green-50 border border-green-200 animate-fade-in"
                role="alert">
                <svg class=" w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Table Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-600">Category</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-600">Location</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-600">Experience</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-600">Education</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-600">Description</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-600 text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($jobPosts as $post)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-[#00285E]">
                                        {{ $post->job_category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $post->job_location }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600">{{ $post->job_experience }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600">{{ $post->job_education }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-500 max-w-xs truncate" title="{{ $post->job_description }}">
                                        {{ Str::limit($post->job_description, 60) }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-3">
                                        <a href="{{ route('jobPost.edit', $post->id) }}"
                                            class="text-sm font-semibold text-[#00285E] hover:underline underline-offset-4">
                                            Edit
                                        </a>

                                        <form action="{{ route('jobPost.destroy', $post->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this post?')">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="text-sm font-semibold text-red-500 hover:text-red-700 transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">
                                    No job posts found. Create your first one above!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
