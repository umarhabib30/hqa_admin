<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PtoLetterGuide;
use Illuminate\Http\JsonResponse;
use Throwable;

class PtoLetterGuideApiController extends Controller
{
    /**
     * GET: All PTO Letter Guides
     * URL: /api/pto-letter-guides
     */
    public function index(): JsonResponse
    {
        try {
            $items = PtoLetterGuide::latest()->get();

            return response()->json([
                'status'  => true,
                'message' => 'PTO letter guides fetched successfully',
                'data'    => $items
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch PTO letter guides',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: Single PTO Letter Guide
     * URL: /api/pto-letter-guides/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $item = PtoLetterGuide::find($id);

            if (!$item) {
                return response()->json([
                    'status'  => false,
                    'message' => 'PTO letter guide not found'
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'PTO letter guide fetched successfully',
                'data'    => $item
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch PTO letter guide',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
