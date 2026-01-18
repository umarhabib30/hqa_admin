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
        try {
            $validated = $request->validate([
                'booking_type'   => 'required|in:full_table,seats',
                'seat_types'     => 'nullable|array',
                'first_name'     => 'required|string',
                'last_name'      => 'required|string',
                'email'          => 'required|email',
                'phone'          => 'required|string',
                'amount'         => 'required|numeric|min:1',
                'payment_method' => 'required|string', // From React
            ]);

            $event = DonationBooking::findOrFail($id);

            /* -----------------------------------------------------
               💳 STRIPE PAYMENT PROCESSING
            ----------------------------------------------------- */
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::create([
                'amount' => $validated['amount'] * 100, // Cents
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

            /* -----------------------------------------------------
               💾 DATABASE ALLOCATION LOGIC
            ----------------------------------------------------- */
            $bookings = $event->table_bookings ?? [];
            $assignedTables = [];
            $paidAmount = $validated['amount'];
            $fullTablesCount = $event->full_tables_booked;

            if ($validated['booking_type'] === 'full_table') {
                $tableNo = null;
                for ($i = 1; $i <= $event->total_tables; $i++) {
                    $tableBookings = $bookings[$i] ?? [];
                    $bookedSeats = collect($tableBookings)->sum('total_seats');
                    if ($bookedSeats === 0) {
                        $tableNo = $i;
                        break;
                    }
                }

                if (!$tableNo) {
                    return response()->json(['success' => false, 'message' => 'No empty table available'], 400);
                }

                $bookings[$tableNo][] = [
                    'type'          => 'full_table',
                    'first_name'    => $validated['first_name'],
                    'last_name'     => $validated['last_name'],
                    'email'         => $validated['email'],
                    'phone'         => $validated['phone'],
                    'seat_types'    => [],
                    'total_seats'   => $event->seats_per_table,
                    'booked_at'     => now()->toDateTimeString(),
                    'payment_id'    => $paymentId,
                    'paid_amount'   => $paidAmount,
                    'checked_in_at' => null,
                ];
                $fullTablesCount++;
                $successMessage = 'Full table booked successfully';
                $assignedTables[] = $tableNo;
            } else {
                $seatTypes = $validated['seat_types'] ?? [];
                $totalSeatsRequested = array_sum($seatTypes);

                if ($totalSeatsRequested <= 0) {
                    return response()->json(['success' => false, 'message' => 'Select at least one seat'], 422);
                }

                $remainingSeats = $totalSeatsRequested;
                $remainingSeatTypes = $seatTypes;

                for ($table = 1; $table <= $event->total_tables; $table++) {
                    $tableBookings = $bookings[$table] ?? [];
                    $alreadyBooked = collect($tableBookings)->sum('total_seats');

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
                        'booked_at'     => now()->toDateTimeString(),
                        'payment_id'    => $paymentId,
                        'paid_amount'   => $paidAmount,
                        'checked_in_at' => null,
                    ];

                    $remainingSeats -= $seatsForThisTable;
                    $assignedTables[] = $table;
                    if ($remainingSeats <= 0) break;
                }

                if ($remainingSeats > 0) {
                    return response()->json(['success' => false, 'message' => 'Not enough seats available'], 400);
                }
                $successMessage = 'Seats booked successfully';
            }

            /* -----------------------------------------------------
               ✅ UPDATE DATABASE RECORD (With Payment Info)
            ----------------------------------------------------- */
            $event->update([
                'table_bookings'           => $bookings,
                'full_tables_booked'       => $fullTablesCount,
                'stripe_payment_intent_id' => $paymentId,
                'payment_status'           => 'paid',
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
                'seat_types'  => $validated['seat_types'] ?? [],
                'total_seats' => $validated['booking_type'] === 'full_table' ? $event->seats_per_table : $totalSeatsRequested,
                'tables'      => array_unique($assignedTables),
            ];

            $mailSent = false;
            try {
                $qrPayload = route('donationBooking.checkIn', ['qr_token' => $paymentId]);
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
                'mail_sent' => $mailSent
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
