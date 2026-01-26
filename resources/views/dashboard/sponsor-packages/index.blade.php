@extends('layouts.layout')
@section('content')
    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
        <style>
            /* Make DataTables spacing look good with Tailwind layouts */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                margin-bottom: 0.75rem;
            }

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                margin-top: 0.75rem;
            }

            /* Slightly smaller controls on mobile */
            @media (max-width: 768px) {

                .dataTables_wrapper .dataTables_length,
                .dataTables_wrapper .dataTables_filter,
                .dataTables_wrapper .dataTables_info,
                .dataTables_wrapper .dataTables_paginate {
                    font-size: 12px;
                }
            }
        </style>
    @endpush

    <div>

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
                Sponsor Packages
            </h1>

            <a href="{{ route('sponsor-packages.create') }}"
                class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-[#00285E]
                   text-[#00285E] font-semibold
                   hover:bg-[#00285E] hover:text-white
                   transition active:scale-95">
                + Add Package
            </a>
        </div>

        <!-- SUCCESS MESSAGE -->
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- DESKTOP TABLE -->
        <div class="hidden md:block bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 text-sm text-gray-600">
                    <tr>
                        <th class="p-4 text-left">Title</th>
                        <th class="p-4 text-right">Price/Year</th>
                        <th class="p-4 text-center">Benefits Count</th>
                        <th class="p-4 text-center">Created At</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($packages as $package)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-medium">{{ $package->title }}</td>
                            <td class="p-4 text-right font-semibold text-[#00285E]">
                                ${{ number_format($package->price_per_year, 2) }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    {{ count($package->benefits ?? []) }} benefit(s)
                                </span>
                            </td>
                            <td class="p-4 text-center text-sm text-gray-600">
                                {{ $package->created_at->format('M d, Y') }}
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('sponsor-packages.show', $package->id) }}"
                                        class="px-3 py-1 rounded border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition">
                                        View
                                    </a>
                                    <a href="{{ route('sponsor-packages.edit', $package->id) }}"
                                        class="px-3 py-1 rounded border border-[#00285E] text-[#00285E] hover:bg-[#00285E] hover:text-white transition">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('sponsor-packages.destroy', $package->id) }}"
                                        class="delete-form inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1 rounded border border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition delete-btn"
                                            data-id="{{ $package->id }}" data-name="{{ $package->title }}">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500">
                                No sponsor packages found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MOBILE CARDS -->
        <div class="md:hidden space-y-4">
            @forelse($packages as $package)
                <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $package->title }}</h3>
                            <p class="text-lg font-bold text-[#00285E] mt-1">
                                ${{ number_format($package->price_per_year, 2) }}/year
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            {{ count($package->benefits ?? []) }} benefit(s)
                        </span>
                    </div>

                    <div class="text-sm text-gray-700">
                        <p><strong>Created:</strong> {{ $package->created_at->format('M d, Y') }}</p>
                    </div>

                    <div class="flex flex-col gap-2 pt-2">
                        <a href="{{ route('sponsor-packages.show', $package->id) }}"
                            class="w-full text-center px-4 py-2 rounded-lg border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition">
                            View Details
                        </a>
                        <a href="{{ route('sponsor-packages.edit', $package->id) }}"
                            class="w-full text-center px-4 py-2 rounded-lg border border-[#00285E] text-[#00285E] hover:bg-[#00285E] hover:text-white transition">
                            Edit
                        </a>

                        <form method="POST" action="{{ route('sponsor-packages.destroy', $package->id) }}"
                            class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full px-4 py-2 rounded-lg border border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition delete-btn"
                                data-id="{{ $package->id }}" data-name="{{ $package->title }}">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    No sponsor packages found.
                </div>
            @endforelse
        </div>

        <!-- SUBSCRIBERS (BELOW PACKAGES) -->
        <div class="mt-5 bg-white rounded-xl shadow-sm p-4" style="margin-top: 20px;">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <div class="text-sm text-gray-500">Subscribers</div>
                    <div class="text-lg font-semibold text-gray-800">
                        {{ $activePackageId === 'all' ? 'All Packages' : $packages->firstWhere('id', (int) $activePackageId)?->title ?? 'Selected Package' }}
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('sponsor-packages.index', ['package_id' => 'all']) }}"
                        class="px-4 py-2 rounded-lg border text-sm font-semibold transition
                    {{ ($activePackageId ?? 'all') === 'all' ? 'bg-[#00285E] text-white border-[#00285E]' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                        All
                    </a>

                    @foreach ($packages as $package)
                        <a href="{{ route('sponsor-packages.index', ['package_id' => $package->id]) }}"
                            class="px-4 py-2 rounded-lg border text-sm font-semibold transition flex items-center gap-2
                        {{ (string) ($activePackageId ?? 'all') === (string) $package->id ? 'bg-[#00285E] text-white border-[#00285E]' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                            <span>{{ $package->title }}</span>
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-bold
                            {{ (string) ($activePackageId ?? 'all') === (string) $package->id ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">
                                {{ $package->subscribers_count ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table id="subscribersTable" class="w-full min-w-[1100px] text-left border-collapse">
                    <thead class="bg-gray-100 text-sm text-gray-600">
                        <tr>
                            <th class="p-4">Image</th>
                            <th class="p-4">Package</th>
                            <th class="p-4">Name</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Phone</th>
                            <th class="p-4 text-right">Amount</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Payment ID</th>
                            <th class="p-4">Date</th>
                            <th class="p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y bg-white">
                        @forelse($subscribers as $sub)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4">
                                    @if ($sub->image)
                                        <img src="{{ asset('storage/' . $sub->image) }}" alt="Subscriber"
                                            class="w-10 h-10 rounded-full object-cover border" />
                                    @else
                                        <div
                                            class="w-10 h-10 rounded-full border bg-gray-50 flex items-center justify-center text-[10px] text-gray-400">
                                            N/A
                                        </div>
                                    @endif
                                </td>
                                <td class="p-4 font-medium">{{ $sub->package?->title ?? ($sub->sponsor_type ?: '-') }}</td>
                                <td class="p-4">{{ $sub->user_name ?: '-' }}</td>
                                <td class="p-4 text-sm text-gray-700">{{ $sub->user_email ?: '-' }}</td>
                                <td class="p-4 text-sm text-gray-700">{{ $sub->user_phone ?: '-' }}</td>
                                <td class="p-4 text-right font-semibold text-[#00285E]"
                                    data-order="{{ (float) ($sub->amount ?? 0) }}">
                                    ${{ number_format((float) ($sub->amount ?? 0), 2) }}
                                </td>
                                <td class="p-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        {{ $sub->status ?? '-' }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-600">
                                        {{ $sub->payment_id ?: '-' }}
                                    </code>
                                </td>
                                <td class="p-4 text-sm text-gray-600"
                                    data-order="{{ $sub->created_at?->timestamp ?? 0 }}">
                                    {{ $sub->created_at?->format('M d, Y') ?? '-' }}
                                </td>
                                <td class="p-4">
                                    <a href="{{ route('sponsor-package-subscribers.show', $sub->id) }}"
                                        class="px-3 py-1 rounded border border-[#00285E] text-[#00285E] hover:bg-[#00285E] hover:text-white transition text-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="p-6 text-gray-500">No subscribers found.</td>
                                <td class="p-6"></td>
                                <td class="p-6"></td>
                                <td class="p-6"></td>
                                <td class="p-6"></td>
                                <td class="p-6"></td>
                                <td class="p-6"></td>
                                <td class="p-6"></td>
                                <td class="p-6"></td>
                                <td class="p-6"></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const table = document.getElementById('subscribersTable');
                if (!table || !window.jQuery || !jQuery.fn.DataTable) return;

                jQuery('#subscribersTable').DataTable({
                    pageLength: 25,
                    order: [
                        [8, 'desc']
                    ],
                    scrollX: true,
                    autoWidth: false,
                });
            });
        </script>
    @endpush
@endsection
