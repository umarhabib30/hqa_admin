<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlumniFeePersonPrice;
use Illuminate\Http\Request;

class AlumniFeePersonPriceApiController extends Controller
{
    /**
     * Get the active fee for an Alumni Event
     * Example: /api/alumni-fees?event_id=5
     */
    public function index(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:alumni_events,id'
        ]);

        $fee = AlumniFeePersonPrice::where('event_id', $request->event_id)
            ->where('is_active', true)
            ->first();

        if (!$fee) {
            return response()->json([
                'status' => false,
                'message' => 'No active fee found for this Alumni event'
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
