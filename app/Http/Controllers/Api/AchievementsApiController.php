<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Achievements;
use Illuminate\Http\JsonResponse;
use Throwable;

class AchievementsApiController extends Controller
{
    /**
     * GET: All achievements
     * URL: /api/achievements
     */
    public function index(): JsonResponse
    {
        try {
            $achievements = Achievements::latest()->get();

            return response()->json([
                'status' => true,
                'message' => 'Achievements fetched successfully',
                'data' => $achievements
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while fetching achievements',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: Single achievement
     * URL: /api/achievements/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $achievement = Achievements::find($id);

            if (!$achievement) {
                return response()->json([
                    'status' => false,
                    'message' => 'Achievement not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Achievement fetched successfully',
                'data' => $achievement
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while fetching achievement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
