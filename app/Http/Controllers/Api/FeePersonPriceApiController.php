<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\feePersonPrice;
use Illuminate\Http\Request;

class FeePersonPriceApiController extends Controller
{
    /**
     * Get the active price for a specific event.
     * URL Example: /api/fees?event_id=5
     */
    public function index(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:pto_events,id'
        ]);

        $fee = feePersonPrice::where('event_id', $request->event_id)
            ->where('is_active', true)
            ->first();

        if (!$fee) {
            return response()->json([
                'status' => false,
                'message' => 'No active fee found for this specific event'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id'       => $fee->id,
                'event_id' => $fee->event_id,
                'title'    => $fee->title,
                'price'    => (float) $fee->price,
            ]
        ], 200);
    }
}
