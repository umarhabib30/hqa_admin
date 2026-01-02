<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Calender;
use Illuminate\Http\JsonResponse;
use Throwable;

class CalendarApiController extends Controller
{
    /**
     * GET: All Calendar Events
     * URL: /api/calendar-events
     */
    public function index(): JsonResponse
    {
        try {
            $events = Calender::orderBy('start_date')->get();

            return response()->json([
                'status'  => true,
                'message' => 'Calendar events fetched successfully',
                'data'    => $events
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch calendar events',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: Single Calendar Event
     * URL: /api/calendar-events/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $event = Calender::find($id);

            if (!$event) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Calendar event not found'
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Calendar event fetched successfully',
                'data'    => $event
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch calendar event',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
