<?php

namespace App\Http\Controllers\Seller\Api;

use App\Http\Controllers\Controller;
use App\Models\SellerAvailability;
use App\Services\AvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityApiController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $seller = $user->seller;

        return response()->json([
            'days' => AvailabilityService::DAY_NAMES,
            'is_configured' => AvailabilityService::isConfigured($seller),
            'available_now' => AvailabilityService::isAvailableNow($seller),
            'availability' => $seller->availability()->orderBy('day_of_week')->get()->map(fn ($row) => [
                'id' => $row->id,
                'day_of_week' => (int) $row->day_of_week,
                'start_time' => $row->start_time,
                'end_time' => $row->end_time,
                'is_off' => (bool) $row->is_off,
            ]),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = auth()->user();
        $seller = $user->seller;

        $validated = $request->validate([
            'days' => ['required', 'array'],
            'days.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'days.*.enabled' => ['sometimes', 'boolean'],
            'days.*.is_off' => ['sometimes', 'boolean'],
            'days.*.start_time' => ['nullable', 'date_format:H:i'],
            'days.*.end_time' => ['nullable', 'date_format:H:i', 'after:days.*.start_time'],
        ]);

        $seller->availability()->delete();

        foreach ($validated['days'] as $row) {
            $enabled = isset($row['enabled']) ? (bool) $row['enabled'] : false;

            if (! $enabled) {
                continue;
            }

            SellerAvailability::create([
                'seller_id' => $seller->id,
                'day_of_week' => $row['day_of_week'],
                'start_time' => ! empty($row['is_off']) ? null : ($row['start_time'] ?? '09:00'),
                'end_time' => ! empty($row['is_off']) ? null : ($row['end_time'] ?? '18:00'),
                'is_off' => ! empty($row['is_off']),
            ]);
        }

        $seller->load('availability');

        return response()->json([
            'message' => 'Availability saved successfully.',
            'is_configured' => AvailabilityService::isConfigured($seller),
        ]);
    }
}