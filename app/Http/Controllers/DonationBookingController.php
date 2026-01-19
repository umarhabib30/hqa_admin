<?php

namespace App\Http\Controllers;

use App\Models\DonationBooking;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors(['error' => $validator->errors()->first()])
                ->withInput();
        }

        $ticketTypes = $request->ticket_types ?? [];
        if ($request->boolean('enable_baby_sitting')) {
            $ticketTypes[] = [
                'name' => 'Baby Sitting',
                'price' => 0,
            ];
        }

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
            'ticket_types' => $ticketTypes,
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

        Log::info('Donation booking request received', [
            'event_id' => $id,
            'booking_type' => $request->booking_type,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'seat_types' => $request->seat_types ?? [],
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
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

                $seatTypes = $request->seat_types ?? [];
                $babySitting = DonationBooking::countBabySittingFromSeatTypes($seatTypes);
                $tableBooked = false;

                for ($i = 1; $i <= $event->total_tables; $i++) {
                    $tableUsers = $bookings[$i] ?? [];
                    $usedSeats = collect($tableUsers)->sum(function ($entry) use ($event) {
                        return DonationBooking::occupiedSeatsForBookingEntry((array) $entry, (int) $event->seats_per_table);
                    });

                    if ($usedSeats === 0) {
                        $bookings[$i][] = [
                            'type' => 'full_table',
                            'first_name' => $request->first_name,
                            'last_name'  => $request->last_name,
                            'email'      => $request->email,
                            'phone'      => $request->phone,
                            'seat_types' => [],
                            'total_seats' => $event->seats_per_table,
                            'baby_sitting' => 0,
                            'paid_amount' => $request->amount,
                            'payment_id' => $intent->id,
                            'booked_at'  => now(),
                            'checked_in_at' => null,
                        ];

                        if ($babySitting > 0) {
                            $bookings[$i][count($bookings[$i]) - 1]['baby_sitting'] = (int) $babySitting;
                        }

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
                $seatTypes = $request->seat_types ?? [];
                $babySitting = DonationBooking::countBabySittingFromSeatTypes($seatTypes);
                $seatTypesForTables = DonationBooking::stripBabySittingFromSeatTypes($seatTypes);
                $totalSeatsRequested = array_sum($seatTypesForTables);
                $remainingSeats = $totalSeatsRequested;
                $firstEntryPointer = null; // ['table' => int, 'idx' => int]

                for ($i = 1; $i <= $event->total_tables; $i++) {
                    if ($remainingSeats <= 0) break;

                    $tableUsers = $bookings[$i] ?? [];
                    $usedSeats = collect($tableUsers)->sum(function ($entry) use ($event) {
                        return DonationBooking::occupiedSeatsForBookingEntry((array) $entry, (int) $event->seats_per_table);
                    });
                    $available = $event->seats_per_table - $usedSeats;

                    if ($available <= 0) continue;

                    $allocate = min($available, $remainingSeats);

                    $bookings[$i][] = [
                        'type' => 'seats',
                        'first_name' => $request->first_name,
                        'last_name'  => $request->last_name,
                        'email'      => $request->email,
                        'phone'      => $request->phone,
                        'seat_types' => $seatTypesForTables,
                        'total_seats' => $allocate,
                        'baby_sitting' => 0,
                        'paid_amount' => $request->amount,
                        'payment_id' => $intent->id,
                        'booked_at'  => now(),
                        'checked_in_at' => null,
                    ];
                    if ($firstEntryPointer === null) {
                        $firstEntryPointer = ['table' => $i, 'idx' => count($bookings[$i]) - 1];
                    }

                    $remainingSeats -= $allocate;
                    $assignedTables[] = $i;
                }

                if ($remainingSeats > 0) {
                    throw new \Exception('Not enough seats available');
                }

                // Store baby sitting separately (not part of table seating)
                if ($babySitting > 0 && $firstEntryPointer !== null) {
                    $t = $firstEntryPointer['table'];
                    $idx = $firstEntryPointer['idx'];
                    $bookings[$t][$idx]['baby_sitting'] = (int) $babySitting;
                }
            }

            /* ============================
           3️⃣ SAVE EVENT
        ============================ */
            $event->update([
                'table_bookings' => $bookings
            ]);

            DB::commit();

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
        $event = DonationBooking::latest()->first();
        $bookingsList = [];

        if ($event) {
            $bookings = $event->table_bookings ?? [];
            $byPayment = [];

            foreach ($bookings as $tableNo => $tableBookings) {
                foreach ($tableBookings as $booking) {
                    $pid = (string) ($booking['payment_id'] ?? '');
                    if ($pid === '') {
                        continue;
                    }

                    if (!isset($byPayment[$pid])) {
                        $byPayment[$pid] = [
                            'payment_id' => $pid,
                            'type' => $booking['type'] ?? 'seats',
                            'first_name' => $booking['first_name'] ?? '',
                            'last_name' => $booking['last_name'] ?? '',
                            'email' => $booking['email'] ?? '',
                            'phone' => $booking['phone'] ?? '',
                            'tables' => [],
                            'total_seats' => 0,
                            'baby_sitting' => 0,
                            'checked_in' => false,
                            'checked_in_at' => null,
                            'booked_at' => $booking['booked_at'] ?? null,
                        ];
                    }

                    $byPayment[$pid]['tables'][$tableNo] = true;
                    $byPayment[$pid]['total_seats'] += DonationBooking::occupiedSeatsForBookingEntry((array) $booking, (int) $event->seats_per_table);
                    $byPayment[$pid]['baby_sitting'] = max(
                        (int) $byPayment[$pid]['baby_sitting'],
                        DonationBooking::babySittingForBookingEntry((array) $booking)
                    );

                    if (!empty($booking['checked_in_at'])) {
                        $byPayment[$pid]['checked_in'] = true;
                        $ts = (string) $booking['checked_in_at'];
                        $cur = (string) ($byPayment[$pid]['checked_in_at'] ?? '');
                        if ($cur === '' || strcmp($ts, $cur) > 0) {
                            $byPayment[$pid]['checked_in_at'] = $ts;
                        }
                    }
                }
            }

            $bookingsList = array_values($byPayment);
            foreach ($bookingsList as &$row) {
                $row['tables'] = array_map('intval', array_keys($row['tables'] ?? []));
                sort($row['tables']);
            }
            unset($row);

            usort($bookingsList, function ($a, $b) {
                // newest first (fallback to checked_in_at)
                $aKey = (string) ($a['booked_at'] ?? $a['checked_in_at'] ?? '');
                $bKey = (string) ($b['booked_at'] ?? $b['checked_in_at'] ?? '');
                return strcmp($bKey, $aKey);
            });
        }

        return view('dashboard.donation.donationBooking.scan', compact('event', 'bookingsList'));
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
            'enable_baby_sitting' => 'nullable|boolean',
        ]);

        $event = DonationBooking::findOrFail($id);

        $ticketTypes = $request->ticket_types ?? [];
        if ($request->boolean('enable_baby_sitting')) {
            $ticketTypes[] = [
                'name' => 'Baby Sitting',
                'price' => 0,
            ];
        }

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

            'ticket_types' => $ticketTypes,
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

    public function checkIn(Request $request)
    {
        // Accept qr_token via GET query string (e.g. /donation-booking/check-in?qr_token=xxx)
        $qrToken = (string) ($request->query('qr_token') ?? $request->input('qr_token') ?? '');
        $qrToken = trim($qrToken);

        if ($qrToken === '') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Missing QR token'], 422);
            }

            return redirect()->route('donationBooking.scan')->with('error', 'Missing QR token');
        }

        $event = DonationBooking::latest()->first();

        if (!$event) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No event found'], 404);
            }

            return redirect()->route('donationBooking.scan')->with('error', 'No event found');
        }

        $bookings = $event->table_bookings ?? [];
        $matched = [];
        $updated = false;

        foreach ($bookings as $tableNo => $tableBookings) {
            foreach ($tableBookings as $idx => $booking) {
                if (($booking['payment_id'] ?? '') !== $qrToken) {
                    continue;
                }

                $alreadyChecked = !empty($booking['checked_in_at']);
                $checkInTime = $alreadyChecked ? $booking['checked_in_at'] : Carbon::now()->toDateTimeString();

                // update booking entry
                $bookings[$tableNo][$idx]['checked_in_at'] = $checkInTime;
                $updated = $updated || !$alreadyChecked;

                $matched[] = [
                    'table_no' => $tableNo,
                    'type' => $booking['type'] ?? 'seats',
                    'first_name' => $booking['first_name'] ?? '',
                    'last_name' => $booking['last_name'] ?? '',
                    'email' => $booking['email'] ?? '',
                    'phone' => $booking['phone'] ?? '',
                    'seat_types' => DonationBooking::stripBabySittingFromSeatTypes((array) ($booking['seat_types'] ?? [])),
                    'total_seats' => DonationBooking::occupiedSeatsForBookingEntry((array) $booking, (int) $event->seats_per_table),
                    'baby_sitting' => DonationBooking::babySittingForBookingEntry((array) $booking),
                    'checked_in_at' => $checkInTime,
                    'already_checked_in' => $alreadyChecked,
                    'payment_id' => $qrToken,
                ];
            }
        }

        // Normalize baby_sitting so it displays consistently for multi-table bookings
        $babyForPayment = 0;
        foreach ($bookings as $tableBookings) {
            foreach ($tableBookings as $booking) {
                if (($booking['payment_id'] ?? '') === $qrToken) {
                    $babyForPayment = max($babyForPayment, DonationBooking::babySittingForBookingEntry((array) $booking));
                }
            }
        }
        if ($babyForPayment > 0) {
            foreach ($matched as &$m) {
                $m['baby_sitting'] = $babyForPayment;
            }
            unset($m);
        }

        if (empty($matched)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No booking found for this QR token'], 404);
            }

            return redirect()->route('donationBooking.scan')->with('error', 'No booking found for this QR token');
        }

        if ($updated) {
            $event->update(['table_bookings' => $bookings]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'event_id' => $event->id,
                    'payment_id' => $qrToken,
                    'bookings' => $matched,
                ],
            ]);
        }

        return redirect()
            ->route('donationBooking.scan')
            ->with('success', 'Check-in successful')
            ->with('checkin_data', [
                'event_id' => $event->id,
                'payment_id' => $qrToken,
                'bookings' => $matched,
            ]);
    }
}
