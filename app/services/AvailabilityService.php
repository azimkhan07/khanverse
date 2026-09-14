<?php

namespace App\Services;

use App\Models\Seller;
use Carbon\Carbon;

class AvailabilityService
{
    public const DAY_NAMES = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    public static function isConfigured(Seller $seller): bool
    {
        return $seller->availability()->exists();
    }

    /**
     * Whether the seller is available right now. Sellers who never configured
     * availability are treated as always available.
     */
    public static function isAvailableNow(Seller $seller, ?Carbon $at = null): bool
    {
        if (! self::isConfigured($seller)) {
            return true;
        }

        $at = $at ?: Carbon::now();
        $slot = self::slotFor($seller, (int) $at->dayOfWeek);

        if (! $slot) {
            return false;
        }

        if ($slot->is_off) {
            return false;
        }

        $start = Carbon::parse($slot->start_time);
        $end = Carbon::parse($slot->end_time);

        return $at->format('H:i') >= $start->format('H:i')
            && $at->format('H:i') < $end->format('H:i');
    }

    /**
     * First upcoming open slot within the next 7 days.
     *
     * @return array{day_of_week: int, day_name: string, date: string, start_time: string, end_time: string}|null
     */
    public static function nextAvailability(Seller $seller, ?Carbon $from = null): ?array
    {
        if (! self::isConfigured($seller)) {
            return null;
        }

        $from = ($from ?: Carbon::now())->copy()->startOfDay();

        for ($d = 0; $d < 7; $d++) {
            $day = $from->copy()->addDays($d);
            $slot = self::slotFor($seller, (int) $day->dayOfWeek);

            if (! $slot || $slot->is_off || ! $slot->start_time) {
                continue;
            }

            return [
                'day_of_week' => (int) $day->dayOfWeek,
                'day_name' => self::DAY_NAMES[(int) $day->dayOfWeek],
                'date' => $day->format('Y-m-d'),
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
            ];
        }

        return null;
    }

    public static function slotFor(Seller $seller, int $dayOfWeek)
    {
        return $seller->availability()->where('day_of_week', $dayOfWeek)->first();
    }
}