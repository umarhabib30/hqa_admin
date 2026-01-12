@extends('layouts.layout')
@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-6">Edit Sponsor Package</h2>

    <form method="POST" action="{{ route('sponsor-packages.update', $package->id) }}" class="space-y-6" 
          x-data="{ benefits: {{ json_encode(old('benefits', $package->benefits ?? [''])) }} }">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Package Title <span class="text-red-500">*</span></label>
            <input type="text" 
                   id="title" 
                   name="title" 
                   value="{{ old('title', $package->title) }}"
                   placeholder="e.g., Platinum Sponsor, Gold Sponsor"
                   required
                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('title') border-red-500 @enderror">
            @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Price Per Year -->
        <div>
            <label for="price_per_year" class="block text-sm font-medium text-gray-700 mb-2">Price Per Year <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="absolute left-4 top-3 text-gray-500">$</span>
                <input type="number" 
                       id="price_per_year" 
                       name="price_per_year" 
                       value="{{ old('price_per_year', $package->price_per_year) }}"
                       placeholder="25000"
                       step="0.01"
                       min="0"
                       required
                       class="w-full pl-8 pr-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('price_per_year') border-red-500 @enderror">
            </div>
            @error('price_per_year')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Benefits -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Benefits <span class="text-red-500">*</span></label>
            <p class="text-xs text-gray-500 mb-3">Add the benefits included in this package. Click "+ Add Benefit" to add more.</p>
            
            <div class="space-y-3" x-ref="benefitsContainer">
                <template x-for="(benefit, index) in benefits" :key="index">
                    <div class="flex gap-2">
                        <input type="text" 
                               :name="`benefits[${index}]`"
                               x-model="benefits[index]"
                               placeholder="e.g., 3-minute speaking engagement"
                               required
                               class="flex-1 px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('benefits.*') border-red-500 @enderror">
                        <button type="button" 
                                @click="benefits.splice(index, 1)"
                                x-show="benefits.length > 1"
                                class="px-4 py-3 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            <button type="button" 
                    @click="benefits.push('')"
                    class="mt-3 px-4 py-2 bg-blue-100 text-[#00285E] rounded-lg hover:bg-blue-200 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Benefit
            </button>

            @error('benefits')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('benefits.*')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t">
            <a href="{{ route('sponsor-packages.index') }}"
                class="px-6 py-3 border rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>

            <button type="submit"
                class="px-8 py-3 border-2 border-[#00285E] text-[#00285E] rounded-lg hover:bg-[#00285E] hover:text-white transition">
                Update Package
            </button>
        </div>
    </form>
</div>

@endsection

