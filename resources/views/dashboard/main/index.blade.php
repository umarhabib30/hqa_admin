@extends('layouts.layout')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <div class="space-y-12">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900">Dashboard</h1>
            </div>
        </div>

        <!-- DONATIONS (TOP) -->
        <div class="bg-white rounded-2xl shadow p-6" style="margin-top: 20px;">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm uppercase tracking-wide text-gray-500">Donations</h2>
                        <a href="{{ route('admin.donations.index') }}" class="text-sm text-[#00285E] font-medium hover:underline">View all</a>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <p class="text-3xl font-bold text-gray-900">
                            ${{ number_format($donationStats['total_amount'], 2) }}
                        </p>
                        <p class="text-sm text-gray-500">
                            ({{ number_format($donationStats['total_count']) }} total)
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3 w-full sm:w-auto">
                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-xs text-gray-500">Today</p>
                        <p class="text-lg font-semibold text-gray-900">
                            ${{ number_format($donationStats['today_amount'], 2) }}
                        </p>
                    </div>
                
                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-xs text-gray-500">Paid</p>
                        <p class="text-lg font-semibold text-emerald-700">
                            ${{ number_format($donationStats['paid_now_amount'], 2) }}
                        </p>
                    </div>
                
                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-xs text-gray-500">Pledged</p>
                        <p class="text-lg font-semibold text-amber-700">
                            ${{ number_format($donationStats['pledged_amount'], 2) }}
                        </p>
                    </div>
                </div>
                
            </div>

            <div class="mt-10 grid grid-cols-1 lg:grid-cols-2 gap-8" style="margin-top: 20px;">
                <!-- RECENT DONATIONS (ONLY 2) -->
                <div class="rounded-2xl border border-gray-100">
                    <div class="p-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">Recent donations</h3>
                        <a href="{{ route('admin.donations.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse(collect($latestDonations)->take(2) as $donation)
                            <div class="p-4 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $donation->name ?: 'Anonymous' }}
                                    </p>
                                    <p class="text-xs text-gray-500 truncate">
                                        {{ $donation->email ?: '—' }} • {{ ucfirst(str_replace('_', ' ', $donation->donation_mode ?? 'paid_now')) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">${{ number_format((float)($donation->amount ?? 0), 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ optional($donation->created_at)->format('d M, Y') }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-sm text-gray-500">No donations yet.</div>
                        @endforelse
                    </div>
                </div>

            
            </div>
        </div>

        <!-- CHARTS ROW (side by side) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" style="margin-top: 20px;">
            <!-- Donations by purpose (with date range) -->
            <div class="bg-white rounded-2xl shadow p-6 flex flex-col">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Donations by purpose</h3>
                <form method="get" action="{{ route('dashboard.index') }}" class="flex flex-wrap items-end gap-3 mb-4">
                    <input type="hidden" name="sponsor_date_from" value="{{ $sponsorChartDateFrom ?? '' }}">
                    <input type="hidden" name="sponsor_date_to" value="{{ $sponsorChartDateTo ?? '' }}">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">From</label>
                        <input type="date" name="date_from" value="{{ $donationsChartDateFrom }}"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#00285E] focus:border-[#00285E]">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">To</label>
                        <input type="date" name="date_to" value="{{ $donationsChartDateTo }}"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#00285E] focus:border-[#00285E]">
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-[#00285E] text-white text-sm font-medium hover:bg-[#00285E]/90">
                        Apply
                    </button>
                </form>
                <div class="w-full flex-1 min-h-0" style="min-height: 260px;">
                    <div class="w-full h-64 relative">
                        <canvas id="donationsByPurposeChart"></canvas>
                    </div>
                </div>
                @if($donationsByPurpose->isEmpty())
                    <p class="text-sm text-gray-500 mt-2 text-center">No donations in this date range.</p>
                @endif
            </div>

            <!-- Sponsor package subscribers count -->
            <div class="bg-white rounded-2xl shadow p-6 flex flex-col">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Sponsor package subscribers</h3>
                <form method="get" action="{{ route('dashboard.index') }}" class="flex flex-wrap items-end gap-3 mb-4">
                    <input type="hidden" name="date_from" value="{{ $donationsChartDateFrom ?? '' }}">
                    <input type="hidden" name="date_to" value="{{ $donationsChartDateTo ?? '' }}">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">From</label>
                        <input type="date" name="sponsor_date_from" value="{{ $sponsorChartDateFrom ?? $donationsChartDateFrom }}"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#00285E] focus:border-[#00285E]">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">To</label>
                        <input type="date" name="sponsor_date_to" value="{{ $sponsorChartDateTo ?? $donationsChartDateTo }}"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#00285E] focus:border-[#00285E]">
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-[#00285E] text-white text-sm font-medium hover:bg-[#00285E]/90">
                        Apply
                    </button>
                </form>
                <div class="w-full flex-1 min-h-0" style="min-height: 260px;">
                    <div class="w-full h-64 relative">
                        <canvas id="sponsorSubscribersChart"></canvas>
                    </div>
                </div>
                @if($sponsorSubscribersChartData->isEmpty())
                    <p class="text-sm text-gray-500 mt-2 text-center">No sponsor subscribers yet.</p>
                @endif
            </div>
        </div>

        <!-- CURRENT EVENTS: Donation Booking | PTO | Alumni -->
        <div class="bg-white rounded-2xl shadow p-6" style="margin-top: 20px;">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- CURRENT DONATION BOOKING EVENT -->
                <div class="rounded-2xl border border-gray-100">
                    <div class="p-4">
                        <h3 class="text-sm font-semibold text-gray-900">Current running event</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Donation booking</p>

                        @if($currentDonationEvent)
                            <p class="mt-1 text-sm text-gray-600">
                                <span class="font-medium text-gray-900">{{ $currentDonationEvent->event_title }}</span>
                                @if($currentDonationEventIsUpcoming)
                                    <span class="ml-2 inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">Upcoming</span>
                                @else
                                    <span class="ml-2 inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Running</span>
                                @endif
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($currentDonationEvent->event_start_date)->format('d M, Y') }}
                                - {{ \Carbon\Carbon::parse($currentDonationEvent->event_end_date)->format('d M, Y') }}
                                • {{ $currentDonationEvent->event_location }}
                            </p>

                            @php
                                $capacity = (int) ($currentDonationEvent->total_seats ?? 0);
                                $booked = (int) ($currentDonationEventSeatsBooked ?? 0);
                                $remaining = max($capacity - $booked, 0);
                                $pct = $capacity > 0 ? min((int) round(($booked / $capacity) * 100), 100) : 0;
                            @endphp

                            <!-- SUMMARY COUNTS -->
                            <div class="mt-6 grid grid-cols-3 gap-3">
                                <div class="rounded-xl bg-gray-50 p-4">
                                    <p class="text-xs text-gray-500">Booked seats</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ number_format($booked) }}</p>
                                </div>
                                <div class="rounded-xl bg-gray-50 p-4">
                                    <p class="text-xs text-gray-500">Capacity</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ number_format($capacity) }}</p>
                                </div>
                                <div class="rounded-xl bg-gray-50 p-4">
                                    <p class="text-xs text-gray-500">Remaining</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ number_format($remaining) }}</p>
                                </div>
                            </div>

                            <!-- PROGRESS -->
                            <div class="mt-6">
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>Progress</span>
                                    <span>{{ $pct }}%</span>
                                </div>
                                <div class="mt-2 h-2 w-full rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>

                            <a href="{{ url('donationBooking') }}" class="mt-4 inline-block text-sm text-[#00285E] font-medium hover:underline">View details →</a>
                        @else
                            <p class="mt-2 text-sm text-gray-500">No running or upcoming donation event found.</p>
                        @endif
                    </div>
                </div>

                <!-- CURRENT PTO EVENT -->
                <div class="rounded-2xl border border-gray-100">
                    <div class="p-4">
                        <h3 class="text-sm font-semibold text-gray-900">Current PTO event</h3>
                        <p class="text-xs text-gray-500 mt-0.5">PTO events</p>

                        @if($currentPtoEvent ?? null)
                            <p class="mt-1 text-sm text-gray-600">
                                <span class="font-medium text-gray-900">{{ $currentPtoEvent->title }}</span>
                                @if($currentPtoEventIsUpcoming ?? false)
                                    <span class="ml-2 inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">Upcoming</span>
                                @else
                                    <span class="ml-2 inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Running</span>
                                @endif
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($currentPtoEvent->start_date)->format('d M, Y') }}
                                @if(!empty($currentPtoEvent->end_date))
                                    – {{ \Carbon\Carbon::parse($currentPtoEvent->end_date)->format('d M, Y') }}
                                @endif
                                @if(!empty($currentPtoEvent->location))
                                    • {{ $currentPtoEvent->location }}
                                @endif
                            </p>
                            <div class="mt-4 rounded-xl bg-gray-50 p-4">
                                <p class="text-xs text-gray-500">Attendees registered</p>
                                <p class="text-lg font-semibold text-gray-900">{{ number_format($currentPtoEventAttendeeCount ?? 0) }}</p>
                            </div>
                            <a href="{{ route('admin.pto-event-attendees.index') }}?event_id={{ $currentPtoEvent->id }}" class="mt-3 inline-block text-sm text-[#00285E] font-medium hover:underline">View attendees →</a>
                        @else
                            <p class="mt-2 text-sm text-gray-500">No running or upcoming PTO event.</p>
                        @endif
                    </div>
                </div>

                <!-- CURRENT ALUMNI EVENT -->
                <div class="rounded-2xl border border-gray-100">
                    <div class="p-4">
                        <h3 class="text-sm font-semibold text-gray-900">Current Alumni event</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Alumni events</p>

                        @if($currentAlumniEvent ?? null)
                            <p class="mt-1 text-sm text-gray-600">
                                <span class="font-medium text-gray-900">{{ $currentAlumniEvent->title }}</span>
                                @if($currentAlumniEventIsUpcoming ?? false)
                                    <span class="ml-2 inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">Upcoming</span>
                                @else
                                    <span class="ml-2 inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Running</span>
                                @endif
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($currentAlumniEvent->start_date)->format('d M, Y') }}
                                @if(!empty($currentAlumniEvent->end_date))
                                    – {{ \Carbon\Carbon::parse($currentAlumniEvent->end_date)->format('d M, Y') }}
                                @endif
                                @if(!empty($currentAlumniEvent->location))
                                    • {{ $currentAlumniEvent->location }}
                                @endif
                            </p>
                            <div class="mt-4 rounded-xl bg-gray-50 p-4">
                                <p class="text-xs text-gray-500">Attendees registered</p>
                                <p class="text-lg font-semibold text-gray-900">{{ number_format($currentAlumniEventAttendeeCount ?? 0) }}</p>
                            </div>
                            <a href="{{ route('admin.alumni-event-attendees.index') }}?event_id={{ $currentAlumniEvent->id }}" class="mt-3 inline-block text-sm text-[#00285E] font-medium hover:underline">View attendees →</a>
                        @else
                            <p class="mt-2 text-sm text-gray-500">No running or upcoming Alumni event.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- OTHER OVERVIEW CARDS (each links to related page) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6" style="margin-top: 20px;">
            <a href="{{ route('sponsor-packages.index') }}" class="bg-white rounded-2xl p-5 shadow hover:shadow-md transition-shadow block group">
                <p class="text-xs uppercase tracking-wide text-gray-500">Sponsor package subscribers</p>
                <p class="mt-2 text-2xl font-bold text-gray-900 group-hover:text-[#00285E]">{{ number_format($sponsorSubscriberCount) }}</p>
                <div class="mt-4 space-y-2">
                    @forelse($sponsorSubscribersByType->take(3) as $row)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 truncate">{{ $row->sponsor_type ?: 'Unknown' }}</span>
                            <span class="font-medium text-gray-900">{{ number_format($row->total) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No subscribers yet.</p>
                    @endforelse
                </div>
            </a>

            <a href="{{ route('donationBooking.index') }}" class="bg-white rounded-2xl p-5 shadow hover:shadow-md transition-shadow block group">
                <p class="text-xs uppercase tracking-wide text-gray-500">Seats booked (all events)</p>
                <p class="mt-2 text-2xl font-bold text-emerald-700 group-hover:text-[#00285E]">{{ number_format($stats['bookings']) }}</p>
                <p class="mt-2 text-sm text-gray-500">Today: <span class="font-medium text-gray-900">{{ number_format($todaySeats) }}</span></p>
            </a>

            <a href="{{ route('alumniForm.index') }}" class="bg-white rounded-2xl p-5 shadow hover:shadow-md transition-shadow block group">
                <p class="text-xs uppercase tracking-wide text-gray-500">Alumni form submissions</p>
                <p class="mt-2 text-2xl font-bold text-gray-900 group-hover:text-[#00285E]">{{ number_format($stats['alumni_forms']) }}</p>
            </a>

            <div class="bg-white rounded-2xl p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-gray-500">Portal activity</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('jobPost.index') }}" class="flex items-center justify-between text-sm hover:bg-gray-50 -mx-2 px-2 py-1 rounded group">
                        <span class="text-gray-600 group-hover:text-[#00285E]">Teacher jobs</span>
                        <span class="font-semibold text-gray-900">{{ number_format($stats['job_posts']) }}</span>
                    </a>
                    <a href="{{ route('ptoSubscribemails.index') }}" class="flex items-center justify-between text-sm hover:bg-gray-50 -mx-2 px-2 py-1 rounded group">
                        <span class="text-gray-600 group-hover:text-[#00285E]">PTO subscribers</span>
                        <span class="font-semibold text-gray-900">{{ number_format($ptoSubscribersCount) }}</span>
                    </a>
                    <a href="{{ route('contact-sponser.index') }}" class="flex items-center justify-between text-sm hover:bg-gray-50 -mx-2 px-2 py-1 rounded group">
                        <span class="text-gray-600 group-hover:text-[#00285E]">Sponsor contact requests</span>
                        <span class="font-semibold text-gray-900">{{ number_format($contactSponsorCount) }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartColors = ['#00285E', '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4'];

            // Donations by purpose (donut chart)
            const donationsCtx = document.getElementById('donationsByPurposeChart');
            if (donationsCtx) {
                const donationsData = @json($donationsByPurpose);
                if (donationsData.length) {
                    new Chart(donationsCtx, {
                        type: 'doughnut',
                        data: {
                            labels: donationsData.map(d => d.purpose),
                            datasets: [{
                                data: donationsData.map(d => d.total),
                                backgroundColor: chartColors.slice(0, donationsData.length),
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%',
                            layout: { padding: { right: 24, left: 8 } },
                            plugins: {
                                legend: { position: 'right', align: 'middle' },
                                tooltip: { callbacks: { label: ctx => ctx.label + ': $' + ctx.raw.toLocaleString() } }
                            }
                        }
                    });
                }
            }

            // Sponsor package subscribers (donut chart)
            const sponsorCtx = document.getElementById('sponsorSubscribersChart');
            if (sponsorCtx) {
                const sponsorData = @json($sponsorSubscribersChartData);
                if (sponsorData.length) {
                    new Chart(sponsorCtx, {
                        type: 'doughnut',
                        data: {
                            labels: sponsorData.map(d => d.sponsor_type || 'Unknown'),
                            datasets: [{
                                data: sponsorData.map(d => Number(d.total)),
                                backgroundColor: chartColors.slice(0, sponsorData.length),
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%',
                            layout: { padding: { right: 24, left: 8 } },
                            plugins: {
                                legend: { position: 'right', align: 'middle' },
                                tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.raw + ' subscriber(s)' } }
                            }
                        }
                    });
                }
            }
        });
    </script>
@endsection
