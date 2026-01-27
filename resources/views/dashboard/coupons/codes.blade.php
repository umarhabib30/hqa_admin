@extends('layouts.layout')
@section('content')

<div class="max-w-6xl mx-auto">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('coupons.index') }}"
               class="inline-flex items-center gap-2 text-[#00285E] hover:underline mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Coupons
            </a>

            <h1 class="text-[24px] md:text-[32px] font-bold text-gray-800">
                {{ $coupon->coupon_name }}
            </h1>

            <p class="text-gray-600 mt-1">
                @if ($coupon->discount_percentage)
                    <span class="font-semibold text-green-600">{{ $coupon->discount_percentage }}% off</span>
                @endif
                @if ($coupon->discount_price)
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
            @php
                $disabled = $codeItem->is_used || $codeItem->is_copied;
            @endphp

            <div id="code-card-{{ $codeItem->id }}"
                 class="bg-white rounded-xl shadow-sm border-2 p-4 transition-all
                 {{ $codeItem->is_used
                        ? 'border-red-300 bg-red-50'
                        : ($codeItem->is_copied
                            ? 'border-blue-300 bg-blue-50'
                            : 'border-gray-200 hover:border-[#00285E] hover:shadow-md') }}">

                <div class="flex items-start justify-between mb-3">
                    <code class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-mono font-semibold
                         {{ $codeItem->is_used ? 'text-gray-400 line-through' : 'text-gray-800' }}">
                        {{ $codeItem->coupon_code }}
                    </code>

                    <div id="badge-{{ $codeItem->id }}">
                        @if ($codeItem->is_used)
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                Used
                            </span>
                        @elseif ($codeItem->is_copied)
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                Copied
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                Active
                            </span>
                        @endif
                    </div>
                </div>

                @if ($codeItem->is_used)
                    <div class="text-xs text-gray-500 space-y-1">
                        <p><strong>Used on:</strong> {{ $codeItem->used_at?->format('M d, Y h:i A') }}</p>
                        @if ($codeItem->used_by_email)
                            <p><strong>By:</strong> {{ $codeItem->used_by_email }}</p>
                        @endif
                    </div>
                @endif

                <button
                    id="copy-btn-{{ $codeItem->id }}"
                    onclick="{{ $disabled ? '' : "copyCouponCode({$codeItem->id}, '{$codeItem->coupon_code}', this)" }}"
                    class="w-full mt-3 px-4 py-2 rounded-lg font-medium transition
                    {{ $disabled
                        ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                        : 'bg-[#00285E] text-white hover:bg-[#00285E]/90' }}"
                    {{ $disabled ? 'disabled' : '' }}
                    title="{{ $codeItem->is_used ? 'This coupon has been used' : ($codeItem->is_copied ? 'Already copied' : 'Click to copy code') }}">

                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>

                        {{ $codeItem->is_used ? 'Used' : ($codeItem->is_copied ? 'Copied' : 'Copy Code') }}
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
    function copyCouponCode(codeId, code, button) {
        // If somehow clicked, avoid double actions
        if (button.disabled) return;

        // Create a temporary textarea element (fallback compatibility)
        const textarea = document.createElement('textarea');
        textarea.value = code;
        textarea.style.position = 'fixed';
        textarea.style.left = '-999999px';
        textarea.style.top = '-999999px';
        document.body.appendChild(textarea);
        textarea.select();

        const afterCopy = () => {
            showCopyToast();
            // update DB and then update UI (even if DB fails, you’ll see error toast)
            markAsCopied(codeId, button);
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code).then(() => {
                afterCopy();
                document.body.removeChild(textarea);
            }).catch(() => {
                // fallback to execCommand
                fallbackCopy(textarea, afterCopy);
            });
        } else {
            fallbackCopy(textarea, afterCopy);
        }
    }

    function fallbackCopy(textarea, afterCopy) {
        try {
            const successful = document.execCommand('copy');
            document.body.removeChild(textarea);

            if (successful) {
                afterCopy();
            } else {
                showCopyError();
            }
        } catch (err) {
            try { document.body.removeChild(textarea); } catch(e) {}
            showCopyError();
        }
    }

    function markAsCopied(codeId, button) {
        fetch("{{ route('coupon-codes.mark-copied') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({ code_id: codeId })
        })
        .then(async (res) => {
            if (!res.ok) {
                const text = await res.text();
                throw new Error(text);
            }
            return res.json();
        })
        .then(() => {
            applyCopiedUI(codeId, button);
        })
        .catch(error => {
            console.error('Failed to update copied status', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Code copied, but failed to update copied status.',
                timer: 2500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    }

    function applyCopiedUI(codeId, button) {
        // Disable button + change style/text
        button.disabled = true;
        button.classList.remove('bg-[#00285E]', 'hover:bg-[#00285E]/90', 'text-white');
        button.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
        button.title = 'Already copied';

        button.innerHTML = `
            <div class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Copied
            </div>
        `;

        // Update badge
        const badge = document.getElementById(`badge-${codeId}`);
        if (badge) {
            badge.innerHTML = `
                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                    Copied
                </span>
            `;
        }

        // Update card style (optional visual)
        const card = document.getElementById(`code-card-${codeId}`);
        if (card) {
            card.classList.remove('border-gray-200');
            card.classList.add('border-blue-300', 'bg-blue-50');
        }
    }

    function showCopyToast() {
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
