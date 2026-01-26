<?php

namespace App\Http\Controllers;

use App\Models\AlumniForm;
use App\Models\DonationBooking;
use App\Models\GeneralDonation;
use App\Models\ContactSponserModel;
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
    public function index()
    {
        $allBookingEvents = DonationBooking::get();

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

        $stats = [
            'alumni_forms' => AlumniForm::count(),
            'bookings' => $totalSeatsBooked,

            'job_posts' => JobPost::count(),
        ];

        // Seats booked today
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

        // Seats booked last 7 days
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

            return [
                'date' => $date->format('d M'),
                'seats' => $seats,
            ];
        });

        // Donations overview
        $donationStats = [
            'total_amount' => (float) (GeneralDonation::sum('amount') ?? 0),
            'total_count' => GeneralDonation::count(),
            'today_amount' => (float) (GeneralDonation::whereDate('created_at', Carbon::today())->sum('amount') ?? 0),
            'paid_now_amount' => (float) (GeneralDonation::where('donation_mode', 'paid_now')->sum('amount') ?? 0),
            'pledged_amount' => (float) (GeneralDonation::where('donation_mode', 'pledged')->sum('amount') ?? 0),
        ];
        $latestDonations = GeneralDonation::latest()->take(5)->get();

        // Sponsor subscribers
        $sponsorSubscriberCount = SponserPackageSubscriber::count();
        $sponsorSubscribersByType = SponserPackageSubscriber::select('sponsor_type', DB::raw('COUNT(*) as total'))
            ->groupBy('sponsor_type')
            ->orderByDesc('total')
            ->get();

        // Contact sponsor requests + PTO mail subscribers
        $contactSponsorCount = ContactSponserModel::count();
        $ptoSubscribersCount = PtoSubscribeMails::count();

        // Current running donation booking event (fallback: next upcoming)
        $today = Carbon::today();
        $currentDonationEvent = DonationBooking::whereDate('event_start_date', '<=', $today)
            ->whereDate('event_end_date', '>=', $today)
            ->orderBy('event_start_date')
            ->first();
        $currentDonationEventIsUpcoming = false;

        if (!$currentDonationEvent) {
            $currentDonationEvent = DonationBooking::whereDate('event_start_date', '>=', $today)
                ->orderBy('event_start_date')
                ->first();
            $currentDonationEventIsUpcoming = $currentDonationEvent ? true : false;
        }

        $currentDonationEventSeatsBooked = 0;
        if ($currentDonationEvent) {
            $tableBookings = $currentDonationEvent->table_bookings ?? [];
            foreach ($tableBookings as $table) {
                $currentDonationEventSeatsBooked += collect($table)->sum(function ($entry) use ($currentDonationEvent) {
                    return DonationBooking::occupiedSeatsForBookingEntry((array) $entry, (int) $currentDonationEvent->seats_per_table);
                });
            }
        }

        return view('dashboard.main.index', compact(
            'stats',
            'todaySeats',
            'last7Days',
            'donationStats',
            'latestDonations',
            'sponsorSubscriberCount',
            'sponsorSubscribersByType',
            'contactSponsorCount',
            'ptoSubscribersCount',
            'currentDonationEvent',
            'currentDonationEventIsUpcoming',
            'currentDonationEventSeatsBooked',
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
