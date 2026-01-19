<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationBooking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\DonationBookingTicketMail;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

// 💳 STRIPE IMPORTS
use Stripe\Stripe;
use Stripe\PaymentIntent;

class DonationBookingApiController extends Controller
{
    /* =====================================================
       HELPER: Split Seat Types Safely
    ===================================================== */
    private function splitSeatTypes(array $seatTypes, int $allowedSeats): array
    {
        $result = [];
        foreach ($seatTypes as $type => $qty) {
            if ($allowedSeats <= 0) break;
            $take = min($qty, $allowedSeats);
            if ($take > 0) {
                $result[$type] = $take;
                $allowedSeats -= $take;
            }
        }
        return $result;
    }

    /* =====================================================
       GET: Latest Event
    ===================================================== */
    public function index(): JsonResponse
    {
        try {
            $event = DonationBooking::latest()->first();
            if (!$event) {
                return response()->json(['success' => false, 'message' => 'No event found'], 404);
            }
            return response()->json(['success' => true, 'data' => $event], 200);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch event',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /* =====================================================
       POST: Book Seat / Full Table with Stripe
    ===================================================== */
    public function bookSeat(Request $request, $id): JsonResponse
    {
        Log::info('Booking request received', ['request' => $request->all()]);
        try {
            $validated = $request->validate([
                'booking_type'   => 'required|in:full_table,seats',
                'seat_types'     => 'nullable|array',
                'table_count'    => 'nullable|required_if:booking_type,full_table|integer|min:1',
                'first_name'     => 'required|string',
                'last_name'      => 'required|string',
                'email'          => 'required|email',
                'phone'          => 'required|string',
                'amount'         => 'required|numeric|min:0',
                'payment_method' => 'nullable|string', 
            ]);

            $event = DonationBooking::findOrFail($id);

            /* -----------------------------------------------------
               💳 STRIPE PAYMENT PROCESSING (skip if amount = 0)
            ----------------------------------------------------- */
            $paidAmount = (float) $validated['amount'];
            $paymentId = null;
            $paymentStatus = 'paid';

            if ($paidAmount > 0) {
                if (empty($validated['payment_method'])) {
                    return response()->json(['success' => false, 'message' => 'payment_method is required when amount > 0'], 422);
                }

                Stripe::setApiKey(config('services.stripe.secret'));

                $intent = PaymentIntent::create([
                    'amount' => (int) round($paidAmount * 100), // Cents
                    'currency' => 'usd',
                    'payment_method' => $validated['payment_method'],
                    'confirm' => true,
                    'automatic_payment_methods' => [
                        'enabled' => true,
                        'allow_redirects' => 'never',
                    ],
                    'description' => "Booking for " . $event->event_title,
                    'receipt_email' => $validated['email'],
                ]);

                if ($intent->status !== 'succeeded') {
                    return response()->json(['success' => false, 'message' => 'Payment failed.'], 400);
                }

                $paymentId = $intent->id;
                $paymentStatus = 'paid';
            } else {
                // Free booking (no card/Stripe)
                $paymentId = (string) Str::uuid();
                $paymentStatus = 'free';
            }

            /* -----------------------------------------------------
               💾 DATABASE ALLOCATION LOGIC
            ----------------------------------------------------- */
            $bookings = $event->table_bookings ?? [];
            $assignedTables = [];
            $fullTablesCount = $event->full_tables_booked;

            if ($validated['booking_type'] === 'full_table') {
                $tableCount = (int) ($validated['table_count'] ?? 1);
                $seatTypes = $validated['seat_types'] ?? [];
                $babySitting = DonationBooking::countBabySittingFromSeatTypes($seatTypes);
                $emptyTables = [];
                for ($i = 1; $i <= $event->total_tables; $i++) {
                    $tableBookings = $bookings[$i] ?? [];
                    $bookedSeats = collect($tableBookings)->sum(function ($entry) use ($event) {
                        return DonationBooking::occupiedSeatsForBookingEntry((array) $entry, (int) $event->seats_per_table);
                    });
                    if ($bookedSeats === 0) {
                        $emptyTables[] = $i;
                        if (count($emptyTables) >= $tableCount) {
                            break;
                        }
                    }
                }

                if (count($emptyTables) < $tableCount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough empty tables available'
                    ], 400);
                }

                $firstEntryPointer = null; // ['table' => int, 'idx' => int]
                foreach ($emptyTables as $tableNo) {
                    $bookings[$tableNo][] = [
                        'type'          => 'full_table',
                        'first_name'    => $validated['first_name'],
                        'last_name'     => $validated['last_name'],
                        'email'         => $validated['email'],
                        'phone'         => $validated['phone'],
                        'seat_types'    => [],
                        'total_seats'   => $event->seats_per_table,
                        'baby_sitting'  => 0,
                        'booked_at'     => now()->toDateTimeString(),
                        'payment_id'    => $paymentId,
                        'paid_amount'   => $paidAmount,
                        'checked_in_at' => null,
                    ];
                    $assignedTables[] = $tableNo;
                    if ($firstEntryPointer === null) {
                        $firstEntryPointer = ['table' => $tableNo, 'idx' => count($bookings[$tableNo]) - 1];
                    }
                }

                // Store baby sitting separately (not part of table seating)
                if (($babySitting ?? 0) > 0 && $firstEntryPointer !== null) {
                    $t = $firstEntryPointer['table'];
                    $idx = $firstEntryPointer['idx'];
                    $bookings[$t][$idx]['baby_sitting'] = (int) $babySitting;
                }

                $fullTablesCount += $tableCount;
                $successMessage = $tableCount > 1 ? 'Full tables booked successfully' : 'Full table booked successfully';
            } else {
                $seatTypes = $validated['seat_types'] ?? [];
                $babySitting = DonationBooking::countBabySittingFromSeatTypes($seatTypes);
                $seatTypesForTables = DonationBooking::stripBabySittingFromSeatTypes($seatTypes);
                $totalSeatsRequested = array_sum($seatTypesForTables);

                if ($totalSeatsRequested <= 0) {
                    return response()->json(['success' => false, 'message' => 'Select at least one seat'], 422);
                }

                $remainingSeats = $totalSeatsRequested;
                $remainingSeatTypes = $seatTypesForTables;
                $firstEntryPointer = null; // ['table' => int, 'idx' => int]

                for ($table = 1; $table <= $event->total_tables; $table++) {
                    $tableBookings = $bookings[$table] ?? [];
                    $alreadyBooked = collect($tableBookings)->sum(function ($entry) use ($event) {
                        return DonationBooking::occupiedSeatsForBookingEntry((array) $entry, (int) $event->seats_per_table);
                    });

                    if ($alreadyBooked >= $event->seats_per_table) continue;

                    $availableInTable = $event->seats_per_table - $alreadyBooked;
                    $seatsForThisTable = min($availableInTable, $remainingSeats);
                    $assignedSeatTypes = $this->splitSeatTypes($remainingSeatTypes, $seatsForThisTable);

                    foreach ($assignedSeatTypes as $type => $qty) {
                        $remainingSeatTypes[$type] -= $qty;
                        if ($remainingSeatTypes[$type] <= 0) unset($remainingSeatTypes[$type]);
                    }

                    $bookings[$table][] = [
                        'type'          => 'seats',
                        'first_name'    => $validated['first_name'],
                        'last_name'     => $validated['last_name'],
                        'email'         => $validated['email'],
                        'phone'         => $validated['phone'],
                        'seat_types'    => $assignedSeatTypes,
                        'total_seats'   => array_sum($assignedSeatTypes),
                        'baby_sitting'  => 0,
                        'booked_at'     => now()->toDateTimeString(),
                        'payment_id'    => $paymentId,
                        'paid_amount'   => $paidAmount,
                        'checked_in_at' => null,
                    ];
                    if ($firstEntryPointer === null) {
                        $firstEntryPointer = ['table' => $table, 'idx' => count($bookings[$table]) - 1];
                    }

                    $remainingSeats -= $seatsForThisTable;
                    $assignedTables[] = $table;
                    if ($remainingSeats <= 0) break;
                }

                if ($remainingSeats > 0) {
                    return response()->json(['success' => false, 'message' => 'Not enough seats available'], 400);
                }

                // Store baby sitting separately (not part of table seating)
                if ($babySitting > 0 && $firstEntryPointer !== null) {
                    $t = $firstEntryPointer['table'];
                    $idx = $firstEntryPointer['idx'];
                    $bookings[$t][$idx]['baby_sitting'] = (int) $babySitting;
                }
                $successMessage = 'Seats booked successfully';
            }

            /* -----------------------------------------------------
               ✅ UPDATE DATABASE RECORD (With Payment Info)
            ----------------------------------------------------- */
            $event->update([
                'table_bookings'           => $bookings,
                'full_tables_booked'       => $fullTablesCount,
                // note: these fields are stored on the event record in this codebase
                'stripe_payment_intent_id' => $paidAmount > 0 ? $paymentId : null,
                'payment_status'           => $paymentStatus,
            ]);

            /* -----------------------------------------------------
               📧 EMAIL TICKET WITH QR
            ----------------------------------------------------- */
            $bookingSummary = [
                'type'        => $validated['booking_type'],
                'first_name'  => $validated['first_name'],
                'last_name'   => $validated['last_name'],
                'email'       => $validated['email'],
                'phone'       => $validated['phone'],
                'seat_types'  => $validated['booking_type'] === 'full_table' ? [] : ($seatTypesForTables ?? []),
                'baby_sitting' => $validated['booking_type'] === 'full_table' ? ($babySitting ?? 0) : ($babySitting ?? 0),
                'total_seats' => $validated['booking_type'] === 'full_table'
                    ? ($event->seats_per_table * (int) ($validated['table_count'] ?? 1))
                    : ($totalSeatsRequested ?? 0),
                'tables'      => array_unique($assignedTables),
            ];

            $mailSent = false;
            try {
                // Use url() to avoid wrong named-route URL generation in some deployments
                $qrPayload = url('/donation-booking/check-in') . '?qr_token=' . urlencode($paymentId);
                $qr = QrCode::create($qrPayload)->setEncoding(new Encoding('UTF-8'))->setSize(400);
                $writer = new PngWriter();
                $qrDataUrl = $writer->write($qr)->getDataUri();

                Mail::to($validated['email'])->send(
                    new DonationBookingTicketMail($event->toArray(), $bookingSummary, $paymentId, $qrDataUrl, (float)$paidAmount)
                );
                $mailSent = true;
            } catch (Throwable $mailEx) {
                Log::error('Mail failed', ['error' => $mailEx->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'payment_id' => $paymentId,
                'mail_sent' => $mailSent,
                'tables' => array_values(array_unique($assignedTables)),
                'baby_sitting' => $validated['booking_type'] === 'full_table' ? ($babySitting ?? 0) : ($babySitting ?? 0),
            ], 200);
        } catch (Throwable $e) {
            Log::error('Booking error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Booking failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /* =====================================================
       POST: Check-in via QR (payment_id)
    ===================================================== */
    public function checkIn(Request $request): JsonResponse
    {
        $validated = $request->validate(['qr_token' => 'required|string']);
        $event = DonationBooking::latest()->first();

        if (!$event) return response()->json(['success' => false, 'message' => 'No event found'], 404);

        $bookings = $event->table_bookings ?? [];
        $matched = [];
        $updated = false;

        foreach ($bookings as $tableNo => $tableBookings) {
            foreach ($tableBookings as $idx => $booking) {
                if (($booking['payment_id'] ?? '') === $validated['qr_token']) {
                    $alreadyChecked = !empty($booking['checked_in_at']);
                    $checkInTime = $alreadyChecked ? $booking['checked_in_at'] : Carbon::now()->toDateTimeString();

                    $bookings[$tableNo][$idx]['checked_in_at'] = $checkInTime;
                    $updated = $updated || !$alreadyChecked;

                    $matched[] = array_merge($booking, [
                        'table_no' => $tableNo,
                        'already_checked_in' => $alreadyChecked
                    ]);
                }
            }
        }

        if (empty($matched)) return response()->json(['success' => false, 'message' => 'Invalid QR token'], 404);
        if ($updated) $event->update(['table_bookings' => $bookings]);

        return response()->json(['success' => true, 'data' => ['bookings' => $matched]]);
    }
}
