@extends('layouts.layout')
@section('content')

<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Contact Sponsor Details</h1>
            <p class="text-sm text-gray-500">View submitted sponsor contact information</p>
        </div>

        <a href="{{ route('contact-sponser.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
            ← Back
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow p-6 space-y-6">

        <!-- Top Info -->
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $contact->full_name ?? '-' }}</h2>
                <p class="text-gray-600 mt-1">{{ $contact->company_name ?? '-' }}</p>

                <div class="mt-4 space-y-2 text-sm text-gray-700">
                    <p><strong>Email:</strong> {{ $contact->email ?? '-' }}</p>
                    <p><strong>Phone:</strong> {{ $contact->phone ?? '-' }}</p>
                </div>
            </div>

            <!-- Sponsor Type Badge -->
            @php
                $type = strtolower($contact->sponsor_type ?? '');
            @endphp

            <div class="shrink-0">
                @if($type === 'gold')
                    <span class="px-4 py-2 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                        Gold Sponsor
                    </span>
                @elseif($type === 'silver')
                    <span class="px-4 py-2 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                        Silver Sponsor
                    </span>
                @elseif($type === 'platinum')
                    <span class="px-4 py-2 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        Platinum Sponsor
                    </span>
                @else
                    <span class="px-4 py-2 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        {{ $contact->sponsor_type ?? 'N/A' }}
                    </span>
                @endif
            </div>
        </div>

        <hr>

        <!-- Message -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Message</h3>

            <div class="bg-gray-50 rounded-lg p-4 text-gray-700 leading-relaxed">
                {{ $contact->message ?? 'No message provided.' }}
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            @if($contact->email)
                <a href="mailto:{{ $contact->email }}"
                   class="w-full sm:w-auto text-center px-4 py-2 rounded-lg bg-[#00285E] text-white hover:opacity-90 transition">
                    Email
                </a>
            @endif

            @if($contact->phone)
                <a href="tel:{{ $contact->phone }}"
                   class="w-full sm:w-auto text-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                    Call
                </a>
            @endif
        </div>

    </div>

</div>

@endsection
