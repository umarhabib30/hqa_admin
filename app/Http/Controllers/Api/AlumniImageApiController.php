<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlumniImage;
use Illuminate\Http\JsonResponse;
use Throwable;

class AlumniImageApiController extends Controller
{
    /**
     * GET: All Alumni Galleries
     * URL: /api/alumni-galleries
     */
    public function index(): JsonResponse
    {
        try {
            $galleries = AlumniImage::latest()->get();

            return response()->json([
                'status'  => true,
                'message' => 'Alumni galleries fetched successfully',
                'data'    => $galleries
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch alumni galleries',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: Single Alumni Gallery
     * URL: /api/alumni-galleries/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $gallery = AlumniImage::find($id);

            if (!$gallery) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Alumni gallery not found'
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Alumni gallery fetched successfully',
                'data'    => $gallery
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch alumni gallery',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
