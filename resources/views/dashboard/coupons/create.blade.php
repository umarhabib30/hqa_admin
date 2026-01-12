@extends('layouts.layout')
@section('content')

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-6">Create New Coupon</h2>

    <form method="POST" action="{{ route('coupons.store') }}" class="space-y-6">
        @csrf

        <!-- Coupon Name -->
        <div>
            <label for="coupon_name" class="block text-sm font-medium text-gray-700 mb-2">Coupon Name <span class="text-red-500">*</span></label>
            <input type="text" 
                   id="coupon_name" 
                   name="coupon_name" 
                   value="{{ old('coupon_name') }}"
                   placeholder="e.g., Summer Sale 2024, New Year Discount"
                   required
                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('coupon_name') border-red-500 @enderror">
            @error('coupon_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Discount Options -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Discount Price -->
            <div>
                <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-2">Discount Price (Optional)</label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-500">$</span>
                    <input type="number" 
                           id="discount_price" 
                           name="discount_price" 
                           value="{{ old('discount_price') }}"
                           placeholder="0.00"
                           step="0.01"
                           min="0"
                           class="w-full pl-8 pr-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('discount_price') border-red-500 @enderror">
                </div>
                @error('discount_price')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Discount Percentage -->
            <div>
                <label for="discount_percentage" class="block text-sm font-medium text-gray-700 mb-2">Discount Percentage (Optional)</label>
                <div class="relative">
                    <input type="number" 
                           id="discount_percentage" 
                           name="discount_percentage" 
                           value="{{ old('discount_percentage') }}"
                           placeholder="0"
                           step="0.01"
                           min="0"
                           max="100"
                           class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('discount_percentage') border-red-500 @enderror">
                    <span class="absolute right-4 top-3 text-gray-500">%</span>
                </div>
                @error('discount_percentage')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Provide either discount price or percentage (or both)</p>
            </div>
        </div>

        <!-- Quantity -->
        <div>
            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Number of Coupons <span class="text-red-500">*</span></label>
            <input type="number" 
                   id="quantity" 
                   name="quantity" 
                   value="{{ old('quantity', 1) }}"
                   placeholder="30"
                   min="1"
                   max="1000"
                   required
                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('quantity') border-red-500 @enderror">
            <p class="mt-1 text-xs text-gray-500">Number of unique coupon codes to generate (1-1000)</p>
            @error('quantity')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t">
            <a href="{{ route('coupons.index') }}"
                class="px-6 py-3 border rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>

            <button type="submit"
                class="px-8 py-3 border-2 border-[#00285E] text-[#00285E] rounded-lg hover:bg-[#00285E] hover:text-white transition">
                Create Coupon
            </button>
        </div>
    </form>
</div>

@endsection
