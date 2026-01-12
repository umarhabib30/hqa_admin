@extends('layouts.layout')
@section('content')

<div class="max-w-4xl mx-auto">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('sponsor-packages.index') }}" 
               class="inline-flex items-center gap-2 text-[#00285E] hover:underline mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Packages
            </a>
            <h1 class="text-[24px] md:text-[32px] font-bold text-gray-800">
                Package Details
            </h1>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('sponsor-packages.edit', $package->id) }}"
                class="px-6 py-3 rounded-xl
                       border-2 border-[#00285E]
                       text-[#00285E] font-semibold
                       hover:bg-[#00285E] hover:text-white
                       transition active:scale-95">
                Edit Package
            </a>
        </div>
    </div>

    <!-- PACKAGE CARD -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        
        <!-- CARD HEADER -->
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-8 border-b border-gray-200">
            <div class="flex items-start gap-4">
                <!-- Icon -->
                <div class="bg-white rounded-xl p-4 shadow-sm">
                    <svg class="w-8 h-8 text-[#00285E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>

                <div class="flex-1">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ $package->title }}</h2>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-bold text-[#00285E]">
                            ${{ number_format($package->price_per_year, 2) }}
                        </span>
                        <span class="text-gray-600 text-lg">/ year</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD BODY -->
        <div class="p-8">
            <!-- BENEFITS SECTION -->
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#00285E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Benefits Include:
                </h3>

                @if($package->benefits && count($package->benefits) > 0)
                <div class="space-y-3">
                    @foreach($package->benefits as $index => $benefit)
                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-6 h-6 rounded-full bg-[#00285E] flex items-center justify-center">
                                <span class="text-white text-xs font-bold">{{ $index + 1 }}</span>
                            </div>
                        </div>
                        <p class="text-gray-700 flex-1">{{ $benefit }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <p>No benefits added to this package yet.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- CARD FOOTER -->
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="text-sm text-gray-600">
                    <p><strong>Created:</strong> {{ $package->created_at->format('F d, Y \a\t g:i A') }}</p>
                    @if($package->updated_at != $package->created_at)
                    <p><strong>Last Updated:</strong> {{ $package->updated_at->format('F d, Y \a\t g:i A') }}</p>
                    @endif
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('sponsor-packages.edit', $package->id) }}"
                        class="px-6 py-2 rounded-lg
                               border-2 border-[#00285E]
                               text-[#00285E] font-semibold
                               hover:bg-[#00285E] hover:text-white
                               transition">
                        Edit Package
                    </a>

                    <form method="POST" action="{{ route('sponsor-packages.destroy', $package->id) }}" class="delete-form inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-6 py-2 rounded-lg
                                   border-2 border-red-600
                                   text-red-600 font-semibold
                                   hover:bg-red-600 hover:text-white
                                   transition delete-btn"
                            data-id="{{ $package->id }}"
                            data-name="{{ $package->title }}">
                            Delete Package
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

