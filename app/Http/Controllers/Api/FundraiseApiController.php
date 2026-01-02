<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FundRaisa;
use Illuminate\Http\JsonResponse;
use Throwable;

class FundraiseApiController extends Controller
{
    /**
     * GET: All Fundraise Goals
     * URL: /api/fundraises
     */
    public function index(): JsonResponse
    {
        try {
            $fundRaises = FundRaisa::latest()->get();

            return response()->json([
                'status'  => true,
                'message' => 'Fundraise goals fetched successfully',
                'data'    => $fundRaises
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch fundraise goals',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: Single Fundraise Goal
     * URL: /api/fundraises/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $fundRaise = FundRaisa::find($id);

            if (!$fundRaise) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Fundraise goal not found'
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Fundraise goal fetched successfully',
                'data'    => $fundRaise
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch fundraise goal',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
