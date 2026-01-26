@extends('layouts.layout')

@section('content')
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
                    <h2 class="text-sm uppercase tracking-wide text-gray-500">Donations</h2>
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
        <div class="bg-white rounded-2xl shadow p-6" style="margin-top: 20px;">
                <!-- CURRENT EVENT -->
                <div class="rounded-2xl border border-gray-100">
                    <div class="p-4">
                        <h3 class="text-sm font-semibold text-gray-900">Current running event</h3>

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

                                // Expect this from controller:
                                // $currentDonationEventSeatBreakdown = [
                                //   'Adult' => 10,
                                //   'Baby' => 3,
                                //   'Sitting' => 2,
                                // ];
                                $seatBreakdown = collect($currentDonationEventSeatBreakdown ?? []);
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

                            <!-- SEAT BREAKDOWN -->
                            <div class="mt-8">
                                <h4 class="text-sm font-semibold text-gray-900">Seat details</h4>

                                @if($seatBreakdown->count())
                                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
                                        @foreach($seatBreakdown as $type => $count)
                                            <div class="rounded-xl bg-gray-50 p-4">
                                                <p class="text-xs text-gray-500 truncate">{{ $type }}</p>
                                                <p class="text-lg font-semibold text-gray-900">{{ number_format((int)$count) }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="mt-2 text-sm text-gray-500">No seat breakdown available for this event.</p>
                                @endif
                            </div>
                        @else
                            <p class="mt-2 text-sm text-gray-500">No running or upcoming donation event found.</p>
                        @endif
                    </div>
                </div>

        </div>

        <!-- OTHER OVERVIEW CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 " style="margin-top: 20px;">
            <div class="bg-white rounded-2xl p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-gray-500">Sponsor package subscribers</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($sponsorSubscriberCount) }}</p>
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
            </div>

            <!-- (kept as-is, since you didn't ask to remove it) -->
            <div class="bg-white rounded-2xl p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-gray-500">Seats booked (all events)</p>
                <p class="mt-2 text-2xl font-bold text-emerald-700">{{ number_format($stats['bookings']) }}</p>
                <p class="mt-2 text-sm text-gray-500">Today: <span class="font-medium text-gray-900">{{ number_format($todaySeats) }}</span></p>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-gray-500">Alumni form submissions</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['alumni_forms']) }}</p>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-gray-500">Portal activity</p>
                <div class="mt-4 space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Teacher jobs</span>
                        <span class="font-semibold text-gray-900">{{ number_format($stats['job_posts']) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">PTO subscribers</span>
                        <span class="font-semibold text-gray-900">{{ number_format($ptoSubscribersCount) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Sponsor contact requests</span>
                        <span class="font-semibold text-gray-900">{{ number_format($contactSponsorCount) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- REMOVED: SEATS BOOKED (LAST 7 DAYS) SECTION -->
    </div>
@endsection
