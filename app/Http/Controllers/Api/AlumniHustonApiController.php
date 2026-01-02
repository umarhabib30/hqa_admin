<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlumniHuston;
use Illuminate\Http\JsonResponse;
use Throwable;

class AlumniHustonApiController extends Controller
{
    /**
     * GET: All Alumni Huston
     * URL: /api/alumni-huston
     */
    public function index(): JsonResponse
    {
        try {
            $alumni = AlumniHuston::latest()->get();

            return response()->json([
                'status'  => true,
                'message' => 'Alumni fetched successfully',
                'data'    => $alumni
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch alumni',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: Single Alumni Huston
     * URL: /api/alumni-huston/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $alumni = AlumniHuston::find($id);

            if (!$alumni) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Alumni not found'
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Alumni fetched successfully',
                'data'    => $alumni
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch alumni',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
