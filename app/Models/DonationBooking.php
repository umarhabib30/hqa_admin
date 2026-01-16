<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationBooking extends Model
{
    protected $guarded = [];

    // ✅ THIS IS REQUIRED
    protected $casts = [
        'ticket_types'     => 'array',
        'table_bookings'   => 'array',
        'allow_full_table' => 'boolean',
    ];

    /**
     * Normalize seat type keys to compare reliably (e.g. "Baby Sitting", "baby_sitting").
     */
    public static function normalizeSeatType(string $type): string
    {
        $t = strtolower(trim($type));
        $t = str_replace(['_', '-'], ' ', $t);
        $t = preg_replace('/\s+/', ' ', $t);
        return $t ?? '';
    }

    /**
     * Baby sitting does NOT consume table seats (capacity).
     */
    public static function isBabySittingType(string $type): bool
    {
        $t = self::normalizeSeatType($type);
        return $t === 'baby sitting' || $t === 'babysitting';
    }

    /**
     * Count seats that should consume capacity (exclude baby sitting).
     */
    public static function countCountableSeatsFromSeatTypes(array $seatTypes): int
    {
        $total = 0;
        foreach ($seatTypes as $type => $qty) {
            if (self::isBabySittingType((string) $type)) {
                continue;
            }
            $total += (int) $qty;
        }
        return $total;
    }

    /**
     * For an entry inside table_bookings, determine how many seats consume capacity.
     * - full_table consumes seats_per_table
     * - seats consumes sum(seat_types excluding baby sitting), fallback to total_seats
     */
    public static function occupiedSeatsForBookingEntry(array $entry, int $seatsPerTable): int
    {
        $type = (string) ($entry['type'] ?? 'seats');
        if ($type === 'full_table') {
            return $seatsPerTable;
        }

        $seatTypes = $entry['seat_types'] ?? null;
        if (is_array($seatTypes) && count($seatTypes)) {
            return self::countCountableSeatsFromSeatTypes($seatTypes);
        }

        return (int) ($entry['total_seats'] ?? 0);
    }
}
