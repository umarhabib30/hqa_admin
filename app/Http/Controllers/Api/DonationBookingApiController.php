<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationBooking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;

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
}
