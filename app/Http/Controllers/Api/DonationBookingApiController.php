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
                return response()->json([
                    'success' => false,
                    'message' => 'No event found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $event
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch event',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /* =====================================================
       POST: Book Seat / Full Table
    ===================================================== */
    public function bookSeat(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'booking_type' => 'required|in:full_table,seats',
                'seat_types'   => 'nullable|array',

                'first_name' => 'required|string',
                'last_name'  => 'required|string',
                'email'      => 'required|email',
                'phone'      => 'required|string',
                'amount'     => 'nullable|numeric|min:0',
            ]);

            $event = DonationBooking::findOrFail($id);
            $bookings = $event->table_bookings ?? [];
            $assignedTables = [];
            $paymentId = (string) Str::uuid();
            $paidAmount = $validated['amount'] ?? 0;

            Log::info('API donation booking request', [
                'event_id' => $id,
                'booking_type' => $validated['booking_type'],
                'email' => $validated['email'],
                'seat_types' => $validated['seat_types'] ?? [],
                'amount' => $paidAmount,
            ]);

            /* =====================================================
               🟢 FULL TABLE BOOKING (ONLY EMPTY TABLE)
            ===================================================== */
            $successMessage = 'Seats booked successfully';
            $responseData = [];

            if ($validated['booking_type'] === 'full_table') {

                $tableNo = null;

                // 🔍 find first EMPTY table
                for ($i = 1; $i <= $event->total_tables; $i++) {
                    $tableBookings = $bookings[$i] ?? [];
                    $bookedSeats = collect($tableBookings)->sum('total_seats');

                    if ($bookedSeats === 0) {
                        $tableNo = $i;
                        break;
                    }
                }

                if (!$tableNo) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No empty table available for full table booking'
                    ], 400);
                }

                $bookings[$tableNo][] = [
                    'type'        => 'full_table',
                    'first_name'  => $validated['first_name'],
                    'last_name'   => $validated['last_name'],
                    'email'       => $validated['email'],
                    'phone'       => $validated['phone'],
                    'seat_types'  => [],
                    'total_seats' => $event->seats_per_table,
                    'booked_at'   => now()->toDateTimeString(),
                    'payment_id'  => $paymentId,
                    'paid_amount' => $paidAmount,
                    'checked_in_at' => null,
                ];

                $event->update([
                    'table_bookings'     => $bookings,
                    'full_tables_booked' => $event->full_tables_booked + 1
                ]);
                $assignedTables[] = $tableNo;
                $successMessage = 'Full table booked successfully';
                $responseData = ['table_no' => $tableNo];
            }

            /* =====================================================
               🔵 SEAT BOOKING (AUTO TABLE SHIFT)
            ===================================================== */
            $seatTypes = $validated['seat_types'] ?? [];
            $totalSeats = array_sum($seatTypes);

            if ($validated['booking_type'] === 'seats' && $totalSeats <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Select at least one seat'
                ], 422);
            }

            $remainingSeats = $totalSeats;
            $remainingSeatTypes = $seatTypes;

            for ($table = 1; $table <= $event->total_tables; $table++) {

                // ❌ Skip tables that already have FULL TABLE
                $tableBookings = $bookings[$table] ?? [];
                $alreadyBooked = collect($tableBookings)->sum('total_seats');

                if ($alreadyBooked >= $event->seats_per_table) {
                    continue;
                }

                $availableSeats = $event->seats_per_table - $alreadyBooked;
                if ($availableSeats <= 0) continue;

                $seatsForTable = min($availableSeats, $remainingSeats);

                // 🔥 Seat-type proportional split
                $assignedSeatTypes = $this->splitSeatTypes(
                    $remainingSeatTypes,
                    $seatsForTable
                );

                // Reduce remaining
                foreach ($assignedSeatTypes as $type => $qty) {
                    $remainingSeatTypes[$type] -= $qty;
                    if ($remainingSeatTypes[$type] <= 0) {
                        unset($remainingSeatTypes[$type]);
                    }
                }

                $bookings[$table][] = [
                    'type'        => 'seats',
                    'first_name'  => $validated['first_name'],
                    'last_name'   => $validated['last_name'],
                    'email'       => $validated['email'],
                    'phone'       => $validated['phone'],
                    'seat_types'  => $assignedSeatTypes,
                    'total_seats' => array_sum($assignedSeatTypes),
                    'booked_at'   => now()->toDateTimeString(),
                    'payment_id'  => $paymentId,
                    'paid_amount' => $paidAmount,
                    'checked_in_at' => null,
                ];

                $remainingSeats -= $seatsForTable;
                $assignedTables[] = $table;
                if ($remainingSeats <= 0) break;
            }

            if ($validated['booking_type'] === 'seats' && $remainingSeats > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough seats available'
                ], 400);
            }

            $event->update([
                'table_bookings' => $bookings
            ]);

            // Email ticket with QR
            $bookingSummary = [
                'type' => $validated['booking_type'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'seat_types' => $seatTypes,
                'total_seats' => $validated['booking_type'] === 'full_table'
                    ? $event->seats_per_table
                    : array_sum($seatTypes),
                'tables' => array_unique($assignedTables),
            ];

            $mailSent = true;
            $mailError = null;
            try {
                // Encode a check-in URL in the QR so it can be opened directly
                $qrPayload = route('donationBooking.checkIn', ['qr_token' => $paymentId]);

                $qr = QrCode::create($qrPayload)
                    ->setEncoding(new Encoding('UTF-8'))
                    ->setSize(400);
                $writer = new PngWriter();
                $qrDataUrl = $writer->write($qr)->getDataUri();

                Mail::to($validated['email'])->send(
                    new DonationBookingTicketMail(
                        $event->toArray(),
                        $bookingSummary,
                        $paymentId,
                        $qrDataUrl,
                        (float) $paidAmount
                    )
                );
            } catch (Throwable $mailEx) {
                $mailSent = false;
                $mailError = $mailEx->getMessage();
                Log::error('API donation booking mail send failed', [
                    'event_id' => $event->id,
                    'payment_id' => $paymentId,
                    'email' => $validated['email'],
                    'error' => $mailError,
                ]);
            }

            if ($mailSent) {
                Log::info('API donation booking mail sent', [
                    'event_id' => $event->id,
                    'payment_id' => $paymentId,
                    'email' => $validated['email'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'data' => $responseData,
                'payment_id' => $paymentId,
                'mail_sent' => $mailSent,
                'mail_error' => $mailError,
            ], 200);
        } catch (Throwable $e) {
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
        $validated = $request->validate([
            'qr_token' => 'required|string',
        ]);

        $event = DonationBooking::latest()->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'No event found'
            ], 404);
        }

        $bookings = $event->table_bookings ?? [];
        $matched = [];
        $updated = false;

        foreach ($bookings as $tableNo => $tableBookings) {
            foreach ($tableBookings as $idx => $booking) {
                if (($booking['payment_id'] ?? '') !== $validated['qr_token']) {
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
                    'seat_types' => $booking['seat_types'] ?? [],
                    'total_seats' => $booking['total_seats'] ?? 0,
                    'checked_in_at' => $checkInTime,
                    'already_checked_in' => $alreadyChecked,
                ];
            }
        }

        if (empty($matched)) {
            return response()->json([
                'success' => false,
                'message' => 'No booking found for this QR token',
            ], 404);
        }

        if ($updated) {
            $event->update(['table_bookings' => $bookings]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'event_id' => $event->id,
                'payment_id' => $validated['qr_token'],
                'bookings' => $matched,
            ],
        ]);
    }
}
