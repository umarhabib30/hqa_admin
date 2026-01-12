<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TopAchiever;
use Exception;

class TopAchieverApiController extends Controller
{
    /**
     * GET all top achievers
     */
    public function index()
    {
        
        try {
            $achievers = TopAchiever::all()->map(function ($achiever) {
                return [
                    'id' => $achiever->id,
                    'class_achiever' => $achiever->class_achiever,
                    'achiever_name' => $achiever->achiever_name,
                    'achiever_desc' => $achiever->achiever_desc,
                    'image' => $achiever->image
                        ? asset('storage/' . $achiever->image)
                        : null,

                    'meta_data' => collect($achiever->meta_data ?? [])->map(function ($meta) {
                        return [
                            'title' => $meta['title'] ?? null,
                            'image' => !empty($meta['image'])
                                ? asset('storage/' . $meta['image'])
                                : null,
                        ];
                    }),

                    'created_at' => $achiever->created_at?->toDateTimeString(),
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $achievers
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch top achievers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET single achiever
     */
    public function show($id)
    {
        try {
            $achiever = TopAchiever::find($id);

            if (!$achiever) {
                return response()->json([
                    'status' => false,
                    'message' => 'Top Achiever not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $achiever->id,
                    'title' => $achiever->title,
                    'desc' => $achiever->desc,
                    'class_achiever' => $achiever->class_achiever,
                    'achiever_name' => $achiever->achiever_name,
                    'achiever_desc' => $achiever->achiever_desc,
                    'image' => $achiever->image
                        ? asset('storage/' . $achiever->image)
                        : null,

                    'meta_data' => collect($achiever->meta_data ?? [])->map(function ($meta) {
                        return [
                            'title' => $meta['title'] ?? null,
                            'image' => !empty($meta['image'])
                                ? asset('storage/' . $meta['image'])
                                : null,
                        ];
                    }),
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch achiever',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
