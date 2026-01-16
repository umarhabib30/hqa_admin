<?php

namespace App\Http\Controllers;

use App\Models\AlumniForm;
use App\Models\DonationBooking;
use App\Models\jobPost;
use App\Models\PtoSubscribeMails;
use Illuminate\Http\Request;
use Carbon\Carbon;
class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stats = [
            'alumni_forms' => AlumniForm::count(),

            'bookings' => DonationBooking::get()->sum(function ($booking) {
                $tableBookings = $booking->table_bookings ?? [];
                $seats = 0;

                foreach ($tableBookings as $table) {
                    $seats += collect($table)->sum('total_seats');
                }

                return $seats;
            }),

            'job_posts' => JobPost::count(),
        ];

        // Seats booked today
        $todaySeats = DonationBooking::get()->sum(function ($booking) {
            if (!Carbon::parse($booking->created_at)->isToday()) {
                return 0;
            }

            $tableBookings = $booking->table_bookings ?? [];
            $seats = 0;

            foreach ($tableBookings as $table) {
                $seats += collect($table)->sum('total_seats');
            }

            return $seats;
        });

        // Seats booked last 7 days
        $last7Days = collect(range(6, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i);

            $seats = DonationBooking::get()->sum(function ($booking) use ($date) {
                if (!Carbon::parse($booking->created_at)->isSameDay($date)) {
                    return 0;
                }

                $tableBookings = $booking->table_bookings ?? [];
                $total = 0;

                foreach ($tableBookings as $table) {
                    $total += collect($table)->sum('total_seats');
                }

                return $total;
            });

            return [
                'date' => $date->format('d M'),
                'seats' => $seats,
            ];
        });

        return view('dashboard.main.index', compact('stats', 'todaySeats', 'last7Days'));
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
