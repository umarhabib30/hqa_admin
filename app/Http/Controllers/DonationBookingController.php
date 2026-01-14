<?php

namespace App\Http\Controllers;

use App\Models\DonationBooking;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\DonationBookingTicketMail;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;

class DonationBookingController extends Controller
{
    public function index()
    {
        $event = DonationBooking::latest()->first();
        return view('dashboard.donation.donationBooking.index', compact('event'));
    }

    public function create()
    {
        return view('dashboard.donation.donationBooking.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_title' => 'required|string',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'event_start_time' => 'required',
            'event_end_time' => 'required',
            'event_location' => 'required|string',
            'contact_number' => 'required|string',

            'total_tables' => 'required|integer|min:1',
            'seats_per_table' => 'required|integer|min:1',

            // ticket categories
            'ticket_types' => 'required|array|min:1',
            'ticket_types.*.name' => 'required|string',
            'ticket_types.*.price' => 'required|numeric|min:0',

            // ✅ FULL TABLE CONFIG
            'allow_full_table' => 'nullable|boolean',
            'full_table_price' => 'nullable|required_if:allow_full_table,1|numeric|min:0',
        ]);

        $totalSeats = $request->total_tables * $request->seats_per_table;

        DonationBooking::create([
            'event_title' => $request->event_title,
            'event_desc' => $request->event_desc,

            'event_start_date' => $request->event_start_date,
            'event_end_date' => $request->event_end_date,
            'event_start_time' => $request->event_start_time,
            'event_end_time' => $request->event_end_time,

            'event_location' => $request->event_location,
            'contact_number' => $request->contact_number,

            'total_tables' => $request->total_tables,
            'seats_per_table' => $request->seats_per_table,
            'total_seats' => $totalSeats,

            // ✅ FULL TABLE SETTINGS
            'allow_full_table' => $request->has('allow_full_table'),
            'full_table_price' => $request->has('allow_full_table')
                ? $request->full_table_price
                : null,

            // INITIAL STATE
            'full_tables_booked' => 0,
            'table_bookings' => [],

            // ticket categories (Adult / Youth etc)
            'ticket_types' => $request->ticket_types,
        ]);

        return redirect()
            ->route('donationBooking.index')
            ->with('success', 'Donation event created successfully');
    }


    public function bookSeat(Request $request, $id)
    {
        $request->validate([
            'booking_type' => 'required|in:seats,full_table',
            'payment_method' => 'required|string',

            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => 'required|email',
            'phone'      => 'required|string',

            // seats booking ke liye
            'seat_types' => 'required_if:booking_type,seats|array',
            'amount'     => 'required|numeric|min:0',
        ]);

        $event = DonationBooking::findOrFail($id);
        $assignedTables = [];

        DB::beginTransaction();

        try {
            /* ============================
           1️⃣ STRIPE PAYMENT (TEST)
        ============================ */
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::create([
                'amount' => (int) ($request->amount * 100),
                'currency' => 'usd',
                'payment_method' => $request->payment_method,
                'confirm' => true,
                'metadata' => [
                    'event_id' => $event->id,
                    'event_title' => $event->event_title,
                    'booking_type' => $request->booking_type,
                    'customer_email' => $request->email,
                ],
            ]);


            if ($intent->status !== 'succeeded') {
                throw new \Exception('Payment failed');
            }

            /* ============================
           2️⃣ SEAT / TABLE BOOKING
        ============================ */
            $bookings = $event->table_bookings ?? [];

            if ($request->booking_type === 'full_table') {

                if (!$event->allow_full_table) {
                    throw new \Exception('Full table booking not allowed');
                }

                $tableBooked = false;

                for ($i = 1; $i <= $event->total_tables; $i++) {
                    $tableUsers = $bookings[$i] ?? [];
                    $usedSeats = collect($tableUsers)->sum('total_seats');

                    if ($usedSeats === 0) {
                        $bookings[$i][] = [
                            'type' => 'full_table',
                            'first_name' => $request->first_name,
                            'last_name'  => $request->last_name,
                            'email'      => $request->email,
                            'phone'      => $request->phone,
                            'seat_types' => [],
                            'total_seats' => $event->seats_per_table,
                            'paid_amount' => $request->amount,
                            'payment_id' => $intent->id,
                            'booked_at'  => now(),
                            'checked_in_at' => null,
                        ];

                        $event->full_tables_booked += 1;
                        $tableBooked = true;
                        $assignedTables[] = $i;
                        break;
                    }
                }

                if (!$tableBooked) {
                    throw new \Exception('No empty table available');
                }
            } else {
                // SEATS booking
                $totalSeatsRequested = array_sum($request->seat_types);
                $remainingSeats = $totalSeatsRequested;

                for ($i = 1; $i <= $event->total_tables; $i++) {
                    if ($remainingSeats <= 0) break;

                    $tableUsers = $bookings[$i] ?? [];
                    $usedSeats = collect($tableUsers)->sum('total_seats');
                    $available = $event->seats_per_table - $usedSeats;

                    if ($available <= 0) continue;

                    $allocate = min($available, $remainingSeats);

                    $bookings[$i][] = [
                        'type' => 'seats',
                        'first_name' => $request->first_name,
                        'last_name'  => $request->last_name,
                        'email'      => $request->email,
                        'phone'      => $request->phone,
                        'seat_types' => $request->seat_types,
                        'total_seats' => $allocate,
                        'paid_amount' => $request->amount,
                        'payment_id' => $intent->id,
                        'booked_at'  => now(),
                        'checked_in_at' => null,
                    ];

                    $remainingSeats -= $allocate;
                    $assignedTables[] = $i;
                }

                if ($remainingSeats > 0) {
                    throw new \Exception('Not enough seats available');
                }
            }

            /* ============================
           3️⃣ SAVE EVENT
        ============================ */
            $event->update([
                'table_bookings' => $bookings
            ]);

            DB::commit();

            // Prepare email + PDF
            $bookingSummary = [
                'type' => $request->booking_type,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'seat_types' => $request->seat_types ?? [],
                'total_seats' => $request->booking_type === 'full_table'
                    ? $event->seats_per_table
                    : array_sum($request->seat_types),
                'tables' => array_unique($assignedTables),
            ];

            $qrPayload = json_encode([
                'type' => 'donation_booking',
                'event_id' => $event->id,
                'payment_id' => $intent->id,
                'email' => $request->email,
            ]);

            $qr = QrCode::create($qrPayload)
                ->setEncoding(new Encoding('UTF-8'))
                ->setSize(400);
            $writer = new PngWriter();
            $qrDataUrl = $writer->write($qr)->getDataUri();

            Mail::to($request->email)->send(
                new DonationBookingTicketMail(
                    $event->toArray(),
                    $bookingSummary,
                    $intent->id,
                    $qrDataUrl,
                    (float) $request->amount
                )
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment & Booking successful',
                'payment_intent' => $intent->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function edit($id)
    {
        $event = DonationBooking::findOrFail($id);
        return view('dashboard.donation.donationBooking.update', compact('event'));
    }

    /**
     * Show QR scan page for check-in (admin/mobile friendly).
     */
    public function scanPage()
    {
        return view('dashboard.donation.donationBooking.scan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'event_title' => 'required|string',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'event_start_time' => 'required',
            'event_end_time' => 'required',
            'event_location' => 'required|string',
            'contact_number' => 'required|string',

            'total_tables' => 'required|integer|min:1',
            'seats_per_table' => 'required|integer|min:1',

            // ticket categories
            'ticket_types' => 'required|array|min:1',
            'ticket_types.*.name' => 'required|string',
            'ticket_types.*.price' => 'required|numeric|min:0',

            // ✅ FULL TABLE CONFIG
            'allow_full_table' => 'nullable|boolean',
            'full_table_price' => 'nullable|required_if:allow_full_table,1|numeric|min:0',
        ]);

        $event = DonationBooking::findOrFail($id);

        $totalSeats = $request->total_tables * $request->seats_per_table;

        $event->update([
            'event_title' => $request->event_title,
            'event_desc' => $request->event_desc,

            'event_start_date' => $request->event_start_date,
            'event_end_date' => $request->event_end_date,
            'event_start_time' => $request->event_start_time,
            'event_end_time' => $request->event_end_time,

            'event_location' => $request->event_location,
            'contact_number' => $request->contact_number,

            'total_tables' => $request->total_tables,
            'seats_per_table' => $request->seats_per_table,
            'total_seats' => $totalSeats,

            // ✅ FULL TABLE SETTINGS
            'allow_full_table' => $request->has('allow_full_table'),
            'full_table_price' => $request->has('allow_full_table')
                ? $request->full_table_price
                : null,

            'ticket_types' => $request->ticket_types,
        ]);

        return redirect()
            ->route('donationBooking.index')
            ->with('success', 'Donation event updated successfully');
    }


    public function destroy($id)
    {
        $event = DonationBooking::findOrFail($id);
        $event->delete();
        return redirect()->route('donationBooking.index')->with('success', 'Event deleted successfully');
    }
}
