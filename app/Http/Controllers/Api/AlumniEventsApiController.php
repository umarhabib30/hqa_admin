<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlumniEvent;
use Illuminate\Http\JsonResponse;
use Throwable;

class AlumniEventsApiController extends Controller
{
    /**
     * GET: All Alumni Events
     * URL: /api/alumni-events
     */
    public function index(): JsonResponse
    {
        try {
            $events = AlumniEvent::latest()->get();

            return response()->json([
                'status'  => true,
                'message' => 'Alumni events fetched successfully',
                'data'    => $events,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch alumni events',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET: Single Alumni Event
     * URL: /api/alumni-events/{id}
     */
    public function show(int $id): JsonResponse
    {
        try {
            $event = AlumniEvent::find($id);

            if (!$event) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Alumni event not found',
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Alumni event fetched successfully',
                'data'    => $event,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch alumni event',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
