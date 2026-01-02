<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PtoEvents as PtoEvent;
use Illuminate\Http\JsonResponse;
use Throwable;

class PtoEventsApiController extends Controller
{
    /**
     * GET: All PTO Events
     * URL: /api/pto-events
     */
    public function index(): JsonResponse
    {
        try {
            $events = PtoEvent::latest()->get();

            return response()->json([
                'status'  => true,
                'message' => 'PTO events fetched successfully',
                'data'    => $events
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch PTO events',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: Single PTO Event
     * URL: /api/pto-events/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $event = PtoEvent::find($id);

            if (!$event) {
                return response()->json([
                    'status'  => false,
                    'message' => 'PTO event not found'
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'PTO event fetched successfully',
                'data'    => $event
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch PTO event',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
