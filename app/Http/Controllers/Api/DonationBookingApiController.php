<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationBooking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;
use Carbon\Carbon;

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
            ]);

            $event = DonationBooking::findOrFail($id);
            $bookings = $event->table_bookings ?? [];

            /* =====================================================
               🟢 FULL TABLE BOOKING (ONLY EMPTY TABLE)
            ===================================================== */
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
                ];

                $event->update([
                    'table_bookings'     => $bookings,
                    'full_tables_booked' => $event->full_tables_booked + 1
                ]);

                return response()->json([
                    'success'  => true,
                    'message'  => 'Full table booked successfully',
                    'table_no' => $tableNo
                ], 200);
            }

            /* =====================================================
               🔵 SEAT BOOKING (AUTO TABLE SHIFT)
            ===================================================== */
            $seatTypes = $validated['seat_types'] ?? [];
            $totalSeats = array_sum($seatTypes);

            if ($totalSeats <= 0) {
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
                ];

                $remainingSeats -= $seatsForTable;
                if ($remainingSeats <= 0) break;
            }

            if ($remainingSeats > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough seats available'
                ], 400);
            }

            $event->update([
                'table_bookings' => $bookings
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Seats booked successfully'
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
