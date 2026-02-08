<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeModal;
use Illuminate\Http\JsonResponse;

class HomeModalApiController extends Controller
{
    /**
     * GET all home modals
     */
    public function index(): JsonResponse
    {
        $modals = HomeModal::latest()->get()->map(fn($modal) => $this->transform($modal));

        return response()->json([
            'status' => true,
            'count'  => $modals->count(),
            'data'   => $modals
        ], 200);
    }

    /**
     * GET single modal
     */
    public function show($id): JsonResponse
    {
        $modal = HomeModal::find($id);

        if (!$modal) {
            return response()->json([
                'status' => false,
                'message' => 'Home modal record not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $this->transform($modal)
        ], 200);
    }

    /**
     * Helper to keep data structure consistent
     */
    private function transform($modal): array
    {
        return [
            'id'           => $modal->id,
            'title'        => $modal->title,
            'description'  => $modal->cdesc,
            'image_url'    => $modal->image ? asset('storage/' . $modal->image) : null,
            'button' => [
                'text' => $modal->btn_text,
                'link' => $modal->btn_link,
            ],
            'external_link' => $modal->general_link,
            'created_at'    => $modal->created_at->toDateTimeString(),
        ];
    }
}
