@extends('layouts.layout')
@section('content')

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-6">Edit Coupon</h2>

    @if(isset($existingCodes) && $existingCodes > 0)
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-sm text-blue-800">
            <strong>Note:</strong> This coupon has {{ $existingCodes }} generated code(s). You can only edit the name and discount values. Quantity cannot be changed.
        </p>
    </div>
    @endif

    <form method="POST" action="{{ route('coupons.update', $coupon->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Coupon Name -->
        <div>
            <label for="coupon_name" class="block text-sm font-medium text-gray-700 mb-2">Coupon Name <span class="text-red-500">*</span></label>
            <input type="text" 
                   id="coupon_name" 
                   name="coupon_name" 
                   value="{{ old('coupon_name', $coupon->coupon_name) }}"
                   placeholder="e.g., Summer Sale 2024, New Year Discount"
                   required
                   class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('coupon_name') border-red-500 @enderror">
            @error('coupon_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Coupon Type -->
        <div>
            <label for="coupon_type" class="block text-sm font-medium text-gray-700 mb-2">Coupon Type <span class="text-red-500">*</span></label>
            <select id="coupon_type" name="coupon_type" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('coupon_type') border-red-500 @enderror">
                <option value="percentage" {{ old('coupon_type', $coupon->coupon_type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage (Default)</option>
                <option value="amount" {{ old('coupon_type', $coupon->coupon_type ?? 'percentage') === 'amount' ? 'selected' : '' }}>Amount (Fixed Value)</option>
            </select>
            @error('coupon_type')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Discount Options -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Discount Price -->
            <div class="coupon-amount-field">
                <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-2">Discount Price (Optional)</label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-500">$</span>
                    <input type="number" 
                           id="discount_price" 
                           name="discount_price" 
                           value="{{ old('discount_price', $coupon->discount_price) }}"
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
            <div class="coupon-percentage-field">
                <label for="discount_percentage" class="block text-sm font-medium text-gray-700 mb-2">Discount Percentage (Optional)</label>
                <div class="relative">
                    <input type="number" 
                           id="discount_percentage" 
                           name="discount_percentage" 
                           value="{{ old('discount_percentage', $coupon->discount_percentage) }}"
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
                <p class="mt-1 text-xs text-gray-500">Provide the value based on the selected type</p>
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t">
            <a href="{{ route('coupons.index') }}"
                class="px-6 py-3 border rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>

            <button type="submit"
                class="px-8 py-3 border-2 border-[#00285E] text-[#00285E] rounded-lg hover:bg-[#00285E] hover:text-white transition">
                Update Coupon
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    (function () {
        const typeSelect = document.getElementById('coupon_type');
        const amountField = document.querySelector('.coupon-amount-field');
        const percentField = document.querySelector('.coupon-percentage-field');
        const priceInput = document.getElementById('discount_price');
        const percentInput = document.getElementById('discount_percentage');

        const toggleFields = () => {
            const isPercentage = typeSelect.value === 'percentage';
            if (isPercentage) {
                amountField.style.display = 'none';
                priceInput.value = '';
                priceInput.disabled = true;

                percentField.style.display = '';
                percentInput.disabled = false;
            } else {
                percentField.style.display = 'none';
                percentInput.value = '';
                percentInput.disabled = true;

                amountField.style.display = '';
                priceInput.disabled = false;
            }
        };

        typeSelect.addEventListener('change', toggleFields);
        toggleFields();
    })();
</script>
@endpush

@endsection
