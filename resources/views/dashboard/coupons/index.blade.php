@extends('layouts.layout')
@section('content')

<div>

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
            Coupons
        </h1>

        <a href="{{ route('coupons.create') }}"
            class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-[#00285E]
                   text-[#00285E] font-semibold
                   hover:bg-[#00285E] hover:text-white
                   transition active:scale-95">
            + Create Coupon
        </a>
    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto p-4">
            <table id="couponsTable" class="display w-full text-left" style="width:100%">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider font-bold">
                        <th class="px-4 py-3 border-b border-gray-200">Coupon Name</th>
                        <th class="px-4 py-3 border-b border-gray-200">Type</th>
                        <th class="px-4 py-3 border-b border-gray-200">Discount Price</th>
                        <th class="px-4 py-3 border-b border-gray-200">Discount %</th>
                        <th class="px-4 py-3 border-b border-gray-200">Total Codes</th>
                        <th class="px-4 py-3 border-b border-gray-200">Seats Allowed</th>
                        <th class="px-4 py-3 border-b border-gray-200">Used</th>
                        <th class="px-4 py-3 border-b border-gray-200">Available</th>
                        <th class="px-4 py-3 border-b border-gray-200">Status</th>
                        <th class="px-4 py-3 border-b border-gray-200 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($coupons as $coupon)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-medium">{{ $coupon->coupon_name }}</td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                            {{ ucfirst($coupon->coupon_type ?? 'amount') }}
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        @if($coupon->discount_price)
                            <span class="font-semibold text-green-600">${{ number_format($coupon->discount_price, 2) }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        @if($coupon->discount_percentage)
                            <span class="font-semibold text-green-600">{{ $coupon->discount_percentage }}%</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">{{ $coupon->total_codes ?? 0 }}</td>
                    <td class="p-4 text-center">{{ (int) ($coupon->seats_allowed ?? 1) }}</td>
                    <td class="p-4 text-center">{{ $coupon->used_codes ?? 0 }}</td>
                    <td class="p-4 text-center">{{ ($coupon->total_codes ?? 0) - ($coupon->used_codes ?? 0) }}</td>
                    <td class="p-4 text-center">
                        @php
                            $usedCount = $coupon->used_codes ?? 0;
                            $totalCount = $coupon->total_codes ?? 0;
                        @endphp
                        @if($usedCount >= $totalCount && $totalCount > 0)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                Fully Used
                            </span>
                        @elseif($usedCount > 0)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                Partially Used
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                Available
                            </span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('coupons.codes', $coupon->id) }}"
                                class="px-3 py-1 rounded border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition">
                                View Codes
                            </a>
                            <a href="{{ route('coupons.edit', $coupon->id) }}"
                                class="px-3 py-1 rounded border border-[#00285E] text-[#00285E] hover:bg-[#00285E] hover:text-white transition">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('coupons.destroy', $coupon->id) }}" class="delete-form inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1 rounded border border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition delete-btn"
                                    data-id="{{ $coupon->id }}"
                                    data-name="{{ $coupon->coupon_name }}">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-10 text-center text-gray-500">No coupons found.</td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <x-datatable-init table-id="couponsTable" />
    @endpush

    <!-- MOBILE CARDS -->
    <div class="md:hidden space-y-4">
        @forelse($coupons as $coupon)
        <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $coupon->coupon_name }}</h3>
                    <div class="text-sm text-gray-600 mt-2 space-y-1">
                        <p><strong>Type:</strong> <span class="font-semibold">{{ ucfirst($coupon->coupon_type ?? 'amount') }}</span></p>
                        @if($coupon->discount_price)
                            <p><strong>Discount Price:</strong> <span class="text-green-600 font-semibold">${{ number_format($coupon->discount_price, 2) }}</span></p>
                        @endif
                        @if($coupon->discount_percentage)
                            <p><strong>Discount %:</strong> <span class="text-green-600 font-semibold">{{ $coupon->discount_percentage }}%</span></p>
                        @endif
                        <p><strong>Seats Allowed:</strong> <span class="font-semibold">{{ (int) ($coupon->seats_allowed ?? 1) }}</span></p>
                    </div>
                </div>
                @php
                    $usedCount = $coupon->used_codes ?? 0;
                    $totalCount = $coupon->total_codes ?? 0;
                @endphp
                @if($usedCount >= $totalCount && $totalCount > 0)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                        Fully Used
                    </span>
                @elseif($usedCount > 0)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                        Partially Used
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        Available
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Total</p>
                    <p class="font-semibold">{{ $coupon->total_codes ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Used</p>
                    <p class="font-semibold">{{ $coupon->used_codes ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Available</p>
                    <p class="font-semibold">{{ ($coupon->total_codes ?? 0) - ($coupon->used_codes ?? 0) }}</p>
                </div>
            </div>

            <div class="flex flex-col gap-2 pt-2">
                <a href="{{ route('coupons.codes', $coupon->id) }}"
                    class="w-full text-center px-4 py-2 rounded-lg border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition">
                    View Codes
                </a>
                <a href="{{ route('coupons.edit', $coupon->id) }}"
                    class="w-full text-center px-4 py-2 rounded-lg border border-[#00285E] text-[#00285E] hover:bg-[#00285E] hover:text-white transition">
                    Edit
                </a>

                <form method="POST" action="{{ route('coupons.destroy', $coupon->id) }}" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full px-4 py-2 rounded-lg border border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition delete-btn"
                        data-id="{{ $coupon->id }}"
                        data-name="{{ $coupon->coupon_name }}">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-500 py-8">
            No coupons found.
        </div>
        @endforelse
    </div>

</div>

@endsection
