<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PtoImage;
use Illuminate\Http\JsonResponse;
use Throwable;

class PtoImagesApiController extends Controller
{
    /**
     * GET: All PTO Galleries
     * URL: /api/pto-galleries
     */
    public function index(): JsonResponse
    {
        try {
            $galleries = PtoImage::latest()->get();

            return response()->json([
                'status'  => true,
                'message' => 'PTO galleries fetched successfully',
                'data'    => $galleries
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch PTO galleries',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: Single PTO Gallery
     * URL: /api/pto-galleries/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $gallery = PtoImage::find($id);

            if (!$gallery) {
                return response()->json([
                    'status'  => false,
                    'message' => 'PTO gallery not found'
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'PTO gallery fetched successfully',
                'data'    => $gallery
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch PTO gallery',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
