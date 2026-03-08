<?php

namespace App\Http\Controllers;

use App\Models\AlumniEvent;
use App\Models\AlumniForm;
use App\Models\DonationBooking;
use App\Models\GeneralDonation;
use App\Models\ContactSponserModel;
use App\Models\PtoEvents;
use App\Models\PtoSubscribeMails;
use App\Models\SponserPackageSubscriber;
use App\Models\jobPost as JobPost;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $can = [
            'donations' => $user->hasPermission('donation.view'),
            'donation_booking' => $user->hasPermission('donation.booking'),
            'sponsor' => $user->hasPermission('sponsor_packages.view'),
            'alumni' => $user->hasAnyPermission(['alumni.view', 'alumni.forms', 'alumni.events']),
            'alumni_forms' => $user->hasAnyPermission(['alumni.view', 'alumni.forms']),
            'alumni_events' => $user->hasAnyPermission(['alumni.view', 'alumni.events']),
            'pto' => $user->hasAnyPermission(['pto.view', 'pto.events', 'pto.subscribe']),
            'pto_events' => $user->hasAnyPermission(['pto.view', 'pto.events']),
            'pto_subscribe' => $user->hasAnyPermission(['pto.view', 'pto.subscribe']),
            'career' => $user->hasAnyPermission(['career.view', 'career.job_posts']),
            'contact_sponsor' => $user->hasPermission('contact_sponsor.view'),
        ];

        // Date range for donations chart (default: last 30 days)
        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : Carbon::today()->endOfDay();
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : Carbon::today()->subDays(30)->startOfDay();
        if ($dateFrom->gt($dateTo)) {
            $dateFrom = $dateTo->copy()->subDays(30)->startOfDay();
        }
        $donationsChartDateFrom = $dateFrom->format('Y-m-d');
        $donationsChartDateTo = $dateTo->format('Y-m-d');

        // Donations (only if user can see donations)
        $donationsByPurpose = collect();
        $donationsByPurposeGrandTotal = 0.0;
        $donationStats = ['total_amount' => 0.0, 'total_count' => 0, 'today_amount' => 0.0, 'paid_now_amount' => 0.0, 'pledged_amount' => 0.0];
        $latestDonations = collect();
        if ($can['donations']) {
            $donationsByPurpose = GeneralDonation::query()
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('donation_for, SUM(amount) as total')
                ->groupBy('donation_for')
                ->orderByDesc('total')
                ->get()
                ->map(function ($row) {
                    return [
                        'purpose' => $row->donation_for ?: 'Unspecified',
                        'total' => (float) $row->total,
                    ];
                });
            $donationsByPurposeGrandTotal = (float) $donationsByPurpose->sum('total');
            $donationStats = [
                'total_amount' => (float) (GeneralDonation::sum('amount') ?? 0),
                'total_count' => GeneralDonation::count(),
                'today_amount' => (float) (GeneralDonation::whereDate('created_at', Carbon::today())->sum('amount') ?? 0),
                'paid_now_amount' => (float) (GeneralDonation::where('donation_mode', 'paid_now')->sum('amount') ?? 0),
                'pledged_amount' => (float) (GeneralDonation::where('donation_mode', 'pledged')->sum('amount') ?? 0),
            ];
            $latestDonations = GeneralDonation::latest()->take(5)->get();
        }

        // Sponsor chart date range
        $sponsorDateTo = $request->filled('sponsor_date_to')
            ? Carbon::parse($request->sponsor_date_to)->endOfDay()
            : Carbon::today()->endOfDay();
        $sponsorDateFrom = $request->filled('sponsor_date_from')
            ? Carbon::parse($request->sponsor_date_from)->startOfDay()
            : Carbon::today()->subDays(30)->startOfDay();
        if ($sponsorDateFrom->gt($sponsorDateTo)) {
            $sponsorDateFrom = $sponsorDateTo->copy()->subDays(30)->startOfDay();
        }
        $sponsorChartDateFrom = $sponsorDateFrom->format('Y-m-d');
        $sponsorChartDateTo = $sponsorDateTo->format('Y-m-d');

        $sponsorSubscribersChartData = collect();
        $sponsorSubscribersChartGrandTotal = 0;
        $sponsorSubscriberCount = 0;
        $sponsorSubscribersByType = collect();
        if ($can['sponsor']) {
            $sponsorSubscribersChartData = SponserPackageSubscriber::query()
                ->whereBetween('created_at', [$sponsorDateFrom, $sponsorDateTo])
                ->select('sponsor_type')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('sponsor_type')
                ->orderByDesc('total')
                ->get();
            $sponsorSubscribersChartGrandTotal = (int) $sponsorSubscribersChartData->sum('total');
            $sponsorSubscriberCount = SponserPackageSubscriber::count();
            $sponsorSubscribersByType = SponserPackageSubscriber::select('sponsor_type', DB::raw('COUNT(*) as total'))
                ->groupBy('sponsor_type')
                ->orderByDesc('total')
                ->get();
        }

        // Donation booking stats (only if user can see donation booking)
        $allBookingEvents = $can['donation_booking'] ? DonationBooking::get() : collect();
        $totalSeatsBooked = 0;
        $todaySeats = 0;
        $last7Days = collect(range(6, 0))->map(fn ($i) => ['date' => Carbon::today()->subDays($i)->format('d M'), 'seats' => 0]);
        if ($can['donation_booking']) {
            $totalSeatsBooked = $allBookingEvents->sum(function ($booking) {
                $tableBookings = $booking->table_bookings ?? [];
                $seats = 0;
                foreach ($tableBookings as $table) {
                    $seats += collect($table)->sum(function ($entry) use ($booking) {
                        return DonationBooking::occupiedSeatsForBookingEntry((array) $entry, (int) $booking->seats_per_table);
                    });
                }
                return $seats;
            });
            $todaySeats = $allBookingEvents->sum(function ($booking) {
                if (!Carbon::parse($booking->created_at)->isToday()) {
                    return 0;
                }
                $tableBookings = $booking->table_bookings ?? [];
                $seats = 0;
                foreach ($tableBookings as $table) {
                    $seats += collect($table)->sum(function ($entry) use ($booking) {
                        return DonationBooking::occupiedSeatsForBookingEntry((array) $entry, (int) $booking->seats_per_table);
                    });
                }
                return $seats;
            });
            $last7Days = collect(range(6, 0))->map(function ($i) use ($allBookingEvents) {
                $date = Carbon::today()->subDays($i);
                $seats = $allBookingEvents->sum(function ($booking) use ($date) {
                    if (!Carbon::parse($booking->created_at)->isSameDay($date)) {
                        return 0;
                    }
                    $tableBookings = $booking->table_bookings ?? [];
                    $total = 0;
                    foreach ($tableBookings as $table) {
                        $total += collect($table)->sum(function ($entry) use ($booking) {
                            return DonationBooking::occupiedSeatsForBookingEntry((array) $entry, (int) $booking->seats_per_table);
                        });
                    }
                    return $total;
                });
                return ['date' => $date->format('d M'), 'seats' => $seats];
            });
        }

        $stats = [
            'alumni_forms' => $can['alumni_forms'] ? AlumniForm::count() : 0,
            'bookings' => $totalSeatsBooked,
            'job_posts' => $can['career'] ? JobPost::count() : 0,
        ];

        $contactSponsorCount = $can['contact_sponsor'] ? ContactSponserModel::count() : 0;
        $ptoSubscribersCount = $can['pto_subscribe'] ? PtoSubscribeMails::count() : 0;

        $today = Carbon::today();

        // Current donation booking event
        $currentDonationEvent = null;
        $currentDonationEventIsUpcoming = false;
        $currentDonationEventSeatsBooked = 0;
        if ($can['donation_booking']) {
            $currentDonationEvent = DonationBooking::whereDate('event_start_date', '<=', $today)
                ->whereDate('event_end_date', '>=', $today)
                ->orderBy('event_start_date')
                ->first();
            if (!$currentDonationEvent) {
                $currentDonationEvent = DonationBooking::whereDate('event_start_date', '>=', $today)
                    ->orderBy('event_start_date')
                    ->first();
                $currentDonationEventIsUpcoming = $currentDonationEvent ? true : false;
            }
            if ($currentDonationEvent) {
                $tableBookings = $currentDonationEvent->table_bookings ?? [];
                foreach ($tableBookings as $table) {
                    $currentDonationEventSeatsBooked += collect($table)->sum(function ($entry) use ($currentDonationEvent) {
                        return DonationBooking::occupiedSeatsForBookingEntry((array) $entry, (int) $currentDonationEvent->seats_per_table);
                    });
                }
            }
        }

        // Current PTO event
        $currentPtoEvent = null;
        $currentPtoEventIsUpcoming = false;
        $currentPtoEventAttendeeCount = 0;
        if ($can['pto_events']) {
            $currentPtoEvent = PtoEvents::whereDate('start_date', '<=', $today)
                ->where(function ($q) use ($today) {
                    $q->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
                })
                ->orderBy('start_date')
                ->first();
            if (!$currentPtoEvent) {
                $currentPtoEvent = PtoEvents::whereDate('start_date', '>=', $today)
                    ->orderBy('start_date')
                    ->first();
                $currentPtoEventIsUpcoming = $currentPtoEvent ? true : false;
            }
            $currentPtoEventAttendeeCount = $currentPtoEvent
                ? \App\Models\PtoEventAttendee::where('event_id', $currentPtoEvent->id)->count()
                : 0;
        }

        // Current Alumni event
        $currentAlumniEvent = null;
        $currentAlumniEventIsUpcoming = false;
        $currentAlumniEventAttendeeCount = 0;
        if ($can['alumni_events']) {
            $currentAlumniEvent = AlumniEvent::whereDate('start_date', '<=', $today)
                ->where(function ($q) use ($today) {
                    $q->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
                })
                ->orderBy('start_date')
                ->first();
            if (!$currentAlumniEvent) {
                $currentAlumniEvent = AlumniEvent::whereDate('start_date', '>=', $today)
                    ->orderBy('start_date')
                    ->first();
                $currentAlumniEventIsUpcoming = $currentAlumniEvent ? true : false;
            }
            $currentAlumniEventAttendeeCount = $currentAlumniEvent
                ? \App\Models\AlumniEventAttendee::where('event_id', $currentAlumniEvent->id)->count()
                : 0;
        }

        return view('dashboard.main.index', compact(
            'can',
            'stats',
            'todaySeats',
            'last7Days',
            'donationStats',
            'latestDonations',
            'donationsByPurpose',
            'donationsByPurposeGrandTotal',
            'donationsChartDateFrom',
            'donationsChartDateTo',
            'sponsorSubscribersChartData',
            'sponsorSubscribersChartGrandTotal',
            'sponsorChartDateFrom',
            'sponsorChartDateTo',
            'sponsorSubscriberCount',
            'sponsorSubscribersByType',
            'contactSponsorCount',
            'ptoSubscribersCount',
            'currentDonationEvent',
            'currentDonationEventIsUpcoming',
            'currentDonationEventSeatsBooked',
            'currentPtoEvent',
            'currentPtoEventIsUpcoming',
            'currentPtoEventAttendeeCount',
            'currentAlumniEvent',
            'currentAlumniEventIsUpcoming',
            'currentAlumniEventAttendeeCount',
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
