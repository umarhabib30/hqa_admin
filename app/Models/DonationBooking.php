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

    public static function normalizeSeatType(string $type): string
    {
        $t = strtolower(trim($type));
        $t = str_replace(['_', '-'], ' ', $t);
        $t = preg_replace('/\s+/', ' ', $t);
        return $t ?? '';
    }

    public static function isBabySittingType(string $type): bool
    {
        $t = self::normalizeSeatType($type);
        return $t === 'baby sitting' || $t === 'babysitting';
    }

    /**
     * Sum baby sitting quantities from seat_types.
     */
    public static function countBabySittingFromSeatTypes(array $seatTypes): int
    {
        $total = 0;
        foreach ($seatTypes as $type => $qty) {
            if (self::isBabySittingType((string) $type)) {
                $total += (int) $qty;
            }
        }
        return $total;
    }

    /**
     * Return seat_types without baby sitting.
     */
    public static function stripBabySittingFromSeatTypes(array $seatTypes): array
    {
        $out = [];
        foreach ($seatTypes as $type => $qty) {
            if (self::isBabySittingType((string) $type)) {
                continue;
            }
            $out[$type] = (int) $qty;
        }
        return $out;
    }

    /**
     * Seats that consume TABLE capacity (exclude baby sitting).
     */
    public static function countOccupiedSeatsFromSeatTypes(array $seatTypes): int
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
     * Get baby sitting count for a stored booking entry.
     * - Prefer explicit 'baby_sitting'
     * - Otherwise derive from seat_types (backwards compatible)
     */
    public static function babySittingForBookingEntry(array $entry): int
    {
        if (isset($entry['baby_sitting'])) {
            return (int) $entry['baby_sitting'];
        }
        $seatTypes = $entry['seat_types'] ?? [];
        return is_array($seatTypes) ? self::countBabySittingFromSeatTypes($seatTypes) : 0;
    }

    /**
     * Seats that consume TABLE capacity for a stored booking entry.
     * - full_table consumes seatsPerTable
     * - seats consumes seat_types excluding baby sitting (backwards compatible), fallback to total_seats
     */
    public static function occupiedSeatsForBookingEntry(array $entry, int $seatsPerTable): int
    {
        $type = (string) ($entry['type'] ?? 'seats');
        if ($type === 'full_table') {
            return $seatsPerTable;
        }

        $seatTypes = $entry['seat_types'] ?? null;
        if (is_array($seatTypes) && count($seatTypes)) {
            return self::countOccupiedSeatsFromSeatTypes($seatTypes);
        }

        return (int) ($entry['total_seats'] ?? 0);
    }
}
