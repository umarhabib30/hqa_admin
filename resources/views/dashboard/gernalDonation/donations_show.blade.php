@extends('layouts.layout')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-8">
        {{-- Header & Breadcrumb --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <nav class="flex text-xs font-bold text-[#00285E] uppercase tracking-widest mb-2" aria-label="Breadcrumb">
                    <a href="{{ route('admin.donations.index') }}" class="hover:opacity-70 transition">Donations</a>
                    <span class="mx-2 text-gray-300">/</span>
                    <span class="text-gray-500">Details</span>
                </nav>
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">
                    Donation <span class="text-[#00285E]">#{{ $donation->id }}</span>
                </h1>
            </div>

            <a href="{{ route('admin.donations.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-600 font-bold rounded-xl shadow-sm hover:bg-gray-50 hover:text-[#00285E] transition-all group">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 transform group-hover:-translate-x-1 transition-transform" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Detailed Information --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Donor & Purpose Card --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gray-50/50 px-8 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="font-bold text-gray-800 uppercase tracking-wider text-xs">Primary Information</h2>
                        <span
                            class="px-3 py-1 bg-blue-50 text-[#00285E] text-[10px] font-black uppercase rounded-lg border border-blue-100">Verified
                            Entry</span>
                    </div>

                    <div class="p-8 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-1">
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Full Name</p>
                                <p class="text-lg font-bold text-gray-900 leading-tight">
                                    {{ $donation->name ?? 'Anonymous Donor' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Email Address</p>
                                <p class="text-lg font-bold text-gray-900 break-all leading-tight">
                                    {{ $donation->email ?? 'Not Provided' }}</p>
                            </div>
                        </div>

                        <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                            <p class="text-[10px] text-blue-400 uppercase font-black tracking-widest mb-1">Purpose of
                                Donation</p>
                            <p class="text-[#00285E] font-bold text-lg">{{ $donation->donation_for }}</p>
                            @if($donation->donation_for === 'Other' && !empty($donation->other_purpose))
                                <p class="text-sm text-gray-600 font-semibold mt-1">{{ $donation->other_purpose }}</p>
                            @endif
                            @if(!empty($donation->honor_type) && !empty($donation->honor_name))
                                <p class="text-sm text-gray-600 font-semibold mt-1">
                                    {{ $donation->honor_type === 'memory' ? 'In the memory of' : 'In the honor of' }}
                                    {{ $donation->honor_name }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Structure Address Card --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gray-50/50 px-8 py-4 border-b border-gray-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <h2 class="font-bold text-gray-800 uppercase tracking-wider text-xs">Address Details</h2>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                            {{-- Street Address --}}
                            <div class="md:col-span-2 space-y-1">
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Street Address</p>
                                <p class="text-gray-800 font-semibold">
                                    {{ $donation->address1 }}
                                    @if ($donation->address2)
                                        <span class="block text-gray-500 font-medium">{{ $donation->address2 }}</span>
                                    @endif
                                </p>
                            </div>

                            {{-- City --}}
                            <div class="space-y-1">
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">City</p>
                                <p class="text-gray-800 font-semibold">{{ $donation->city }}</p>
                            </div>

                            {{-- State --}}
                            <div class="space-y-1">
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">State / Province
                                </p>
                                <p class="text-gray-800 font-semibold">{{ $donation->state }}</p>
                            </div>

                            {{-- Country/Zip --}}
                            <div class="space-y-1">
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Country</p>
                                <p class="text-gray-800 font-semibold">{{ $donation->country }}</p>
                            </div>

                            {{-- Zip Code (Assuming you might have it, if not it just stays clean) --}}
                            {{-- <div class="space-y-1">
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Postal Code</p>
                                <p class="text-gray-800 font-semibold">{{ $donation->zip ?? '—' }}</p>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Financial Card & Actions --}}
            <div class="space-y-6">
                <div class="relative bg-[#00285E] rounded-3xl p-8 shadow-2xl shadow-blue-900/20 overflow-hidden text-white">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>

                    <p class="text-blue-200/70 text-xs font-bold uppercase tracking-widest">Contribution</p>
                    <div class="flex items-baseline gap-1 mt-2">
                        <span class="text-2xl font-medium text-blue-200">$</span>
                        <h3 class="text-5xl font-black tracking-tighter">{{ number_format($donation->amount, 2) }}</h3>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/10 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-blue-200/60 text-xs font-bold uppercase">Payment Mode</span>
                            <span
                                class="px-2 py-0.5 bg-white/10 rounded text-xs font-bold">
                                @if ($donation->donation_mode === 'paid_now')
                                    Cash
                                @elseif ($donation->donation_mode === 'pledged')
                                    Pledged
                                @elseif ($donation->donation_mode === 'stripe')
                                    Stripe
                                @elseif ($donation->donation_mode === 'paypal')
                                    PayPal
                                @else
                                    Unknown
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-blue-200/60 text-xs font-bold uppercase">Transaction Date</span>
                            <span class="text-sm font-bold">{{ $donation->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex flex-col gap-1 mt-2">
                            <span class="text-blue-200/60 text-[10px] font-bold uppercase">Reference ID</span>
                            <span
                                class="font-mono text-[11px] bg-black/20 p-2 rounded-lg break-all">{{ $donation->payment_id ?? 'MANUAL_ENTRY_REF' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="grid grid-cols-1 gap-4">
                    <a href="{{ route('admin.donations.edit', $donation->id) }}"
                        class="group flex items-center justify-center gap-2 w-full bg-white text-gray-800 py-4 rounded-2xl font-bold shadow-sm border border-gray-200 hover:border-[#00285E] hover:text-[#00285E] transition-all">
                        <svg class="w-5 h-5 opacity-50 group-hover:opacity-100" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Details
                    </a>

                    <form action="{{ route('admin.donations.destroy', $donation->id) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to permanently delete this record?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="group flex items-center justify-center gap-2 w-full bg-red-50 text-red-600 py-4 rounded-2xl font-bold border border-red-100 hover:bg-red-600 hover:text-white transition-all shadow-sm shadow-red-100">
                            <svg class="w-5 h-5 opacity-50 group-hover:opacity-100" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Record
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
