@extends('layouts.layout')
@section('content')

<div class="max-w-6xl mx-auto">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('coupons.index') }}" 
               class="inline-flex items-center gap-2 text-[#00285E] hover:underline mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Coupons
            </a>
            <h1 class="text-[24px] md:text-[32px] font-bold text-gray-800">
                {{ $coupon->coupon_name }}
            </h1>
            <p class="text-gray-600 mt-1">
                @if($coupon->discount_percentage)
                    <span class="font-semibold text-green-600">{{ $coupon->discount_percentage }}% off</span>
                @endif
                @if($coupon->discount_price)
                    <span class="font-semibold text-green-600">${{ number_format($coupon->discount_price, 2) }} off</span>
                @endif
            </p>
        </div>

        <div class="text-right">
            <p class="text-sm text-gray-600">Total Codes: <span class="font-semibold">{{ $codes->count() }}</span></p>
            <p class="text-sm text-gray-600">Used: <span class="font-semibold text-red-600">{{ $codes->where('is_used', true)->count() }}</span></p>
            <p class="text-sm text-gray-600">Available: <span class="font-semibold text-green-600">{{ $codes->where('is_used', false)->count() }}</span></p>
        </div>
    </div>

    <!-- COUPON CODES GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($codes as $codeItem)
        <div class="bg-white rounded-xl shadow-sm border-2 p-4 transition-all
                    {{ $codeItem->is_used ? 'border-red-300 bg-red-50' : 'border-gray-200 hover:border-[#00285E] hover:shadow-md' }}">
            
            <div class="flex items-start justify-between mb-3">
                <code class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-mono font-semibold 
                             {{ $codeItem->is_used ? 'text-gray-400 line-through' : 'text-gray-800' }}">
                    {{ $codeItem->coupon_code }}
                </code>
                
                @if($codeItem->is_used)
                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                        Used
                    </span>
                @else
                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        Active
                    </span>
                @endif
            </div>

            @if($codeItem->is_used)
                <div class="text-xs text-gray-500 space-y-1">
                    <p><strong>Used on:</strong> {{ $codeItem->used_at->format('M d, Y h:i A') }}</p>
                    @if($codeItem->used_by_email)
                        <p><strong>By:</strong> {{ $codeItem->used_by_email }}</p>
                    @endif
                </div>
            @endif

            <button 
                onclick="{{ $codeItem->is_used ? '' : "copyCouponCode('{$codeItem->coupon_code}', this)" }}"
                class="w-full mt-3 px-4 py-2 rounded-lg font-medium transition
                       {{ $codeItem->is_used 
                           ? 'bg-gray-200 text-gray-400 cursor-not-allowed' 
                           : 'bg-[#00285E] text-white hover:bg-[#00285E]/90' }}"
                {{ $codeItem->is_used ? 'disabled' : '' }}
                title="{{ $codeItem->is_used ? 'This coupon has been used' : 'Click to copy code' }}">
                <div class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    {{ $codeItem->is_used ? 'Used' : 'Copy Code' }}
                </div>
            </button>
        </div>
        @empty
        <div class="col-span-full text-center text-gray-500 py-12">
            <p class="text-lg">No coupon codes found for this coupon.</p>
        </div>
        @endforelse
    </div>

</div>

<script>
function copyCouponCode(code, button) {
    // Create a temporary textarea element
    const textarea = document.createElement('textarea');
    textarea.value = code;
    textarea.style.position = 'fixed';
    textarea.style.left = '-999999px';
    textarea.style.top = '-999999px';
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    
    try {
        // Try using the modern clipboard API
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code).then(function() {
                showCopySuccess(button);
                document.body.removeChild(textarea);
            }).catch(function() {
                fallbackCopy(code, button, textarea);
            });
        } else {
            fallbackCopy(code, button, textarea);
        }
    } catch (err) {
        fallbackCopy(code, button, textarea);
    }
}

function fallbackCopy(code, button, textarea) {
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess(button);
        } else {
            showCopyError();
        }
    } catch (err) {
        showCopyError();
    } finally {
        document.body.removeChild(textarea);
    }
}

function showCopySuccess(button) {
    const originalHTML = button.innerHTML;
    button.innerHTML = `
        <div class="flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Copied!
        </div>
    `;
    button.classList.remove('bg-[#00285E]', 'hover:bg-[#00285E]/90');
    button.classList.add('bg-green-600');
    
    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('bg-green-600');
        button.classList.add('bg-[#00285E]', 'hover:bg-[#00285E]/90');
    }, 2000);
    
    Swal.fire({
        icon: 'success',
        title: 'Copied!',
        text: 'Coupon code copied to clipboard',
        timer: 2000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

function showCopyError() {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Failed to copy coupon code. Please try again.',
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}
</script>

@endsection

