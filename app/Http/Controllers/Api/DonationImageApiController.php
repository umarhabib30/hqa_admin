<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\donationImage;
use Illuminate\Http\JsonResponse;
use Throwable;

class DonationImageApiController extends Controller
{
    /**
     * GET: All Galleries
     * URL: /api/donation-galleries
     */
    public function index(): JsonResponse
    {
        try {
            $galleries = donationImage::latest()->get();

            return response()->json([
                'status'  => true,
                'message' => 'Donation galleries fetched successfully',
                'data'    => $galleries
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch donation galleries',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: Single Gallery
     * URL: /api/donation-galleries/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $gallery = donationImage::find($id);

            if (!$gallery) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Gallery not found'
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Donation gallery fetched successfully',
                'data'    => $gallery
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch donation gallery',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
