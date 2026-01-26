@extends('layouts.layout')
@section('content')

<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">Subscriber Details</h1>
            <div class="text-sm text-gray-500 mt-1">Dashboard / Sponsor Packages / Subscriber</div>
        </div>

        <a href="{{ route('sponsor-packages.index') }}"
            class="px-6 py-3 border rounded-lg hover:bg-gray-50 transition">
            Back
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="shrink-0">
                @if($subscriber->image)
                    <img
                        src="{{ asset('storage/' . $subscriber->image) }}"
                        alt="Subscriber image"
                        class="w-32 h-32 rounded-xl object-cover border"
                    />
                @else
                    <div class="w-32 h-32 rounded-xl border bg-gray-50 flex items-center justify-center text-gray-400">
                        No Image
                    </div>
                @endif
            </div>

            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-xl border bg-gray-50">
                    <div class="text-xs text-gray-500">Package</div>
                    <div class="font-semibold text-gray-800">
                        {{ $subscriber->package?->title ?? ($subscriber->sponsor_type ?: '-') }}
                    </div>
                </div>

                <div class="p-4 rounded-xl border bg-gray-50">
                    <div class="text-xs text-gray-500">Status</div>
                    <div class="font-semibold text-gray-800">{{ $subscriber->status ?? '-' }}</div>
                </div>

                <div class="p-4 rounded-xl border bg-gray-50">
                    <div class="text-xs text-gray-500">Name</div>
                    <div class="font-semibold text-gray-800">{{ $subscriber->user_name ?? '-' }}</div>
                </div>

                <div class="p-4 rounded-xl border bg-gray-50">
                    <div class="text-xs text-gray-500">Phone</div>
                    <div class="font-semibold text-gray-800">{{ $subscriber->user_phone ?? '-' }}</div>
                </div>

                <div class="p-4 rounded-xl border bg-gray-50">
                    <div class="text-xs text-gray-500">Email</div>
                    <div class="font-semibold text-gray-800">{{ $subscriber->user_email ?? '-' }}</div>
                </div>

                <div class="p-4 rounded-xl border bg-gray-50">
                    <div class="text-xs text-gray-500">Amount</div>
                    <div class="font-semibold text-gray-800">${{ number_format((float) ($subscriber->amount ?? 0), 2) }}</div>
                </div>

                <div class="p-4 rounded-xl border bg-gray-50 md:col-span-2">
                    <div class="text-xs text-gray-500">Payment ID</div>
                    <div class="font-mono text-sm text-gray-800 break-all">{{ $subscriber->payment_id ?: '-' }}</div>
                </div>

                <div class="p-4 rounded-xl border bg-gray-50 md:col-span-2">
                    <div class="text-xs text-gray-500">Created</div>
                    <div class="font-semibold text-gray-800">
                        {{ $subscriber->created_at?->format('M d, Y h:i A') ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

