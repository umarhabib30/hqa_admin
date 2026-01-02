<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeModal;

class HomeModalApiController extends Controller
{
    /**
     * GET all home modals
     */
    public function index()
    {
        $modals = HomeModal::all()->map(function ($modal) {
            return [
                'id' => $modal->id,
                'title' => $modal->title,
                'cdesc' => $modal->cdesc,
                'image' => $modal->image
                    ? asset('storage/' . $modal->image)
                    : null,
                'created_at' => $modal->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $modals
        ], 200);
    }

    /**
     * GET single modal
     */
    public function show($id)
    {
        $modal = HomeModal::find($id);

        if (!$modal) {
            return response()->json([
                'status' => false,
                'message' => 'Modal not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $modal->id,
                'title' => $modal->title,
                'cdesc' => $modal->cdesc,
                'image' => $modal->image
                    ? asset('storage/' . $modal->image)
                    : null,
            ]
        ], 200);
    }
}
