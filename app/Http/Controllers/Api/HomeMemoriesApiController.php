<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\homeMemories;
use Exception;

class HomeMemoriesApiController extends Controller
{
    /**
     * GET all home memories
     */
    public function index()
    {
        try {
            $memories = homeMemories::all()->map(function ($memory) {
                return [
                    'id' => $memory->id,
                    'title' => $memory->title,
                    'desc' => $memory->desc,
                    'quote' => $memory->quote,
                    'name' => $memory->name,
                    'graduated' => $memory->graduated,
                    'image' => $memory->image
                        ? asset('storage/' . $memory->image)
                        : null,
                    'created_at' => $memory->created_at?->toDateTimeString(),
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $memories,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch memories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET single memory
     */
    public function show($id)
    {
        try {
            $memory = homeMemories::find($id);

            if (!$memory) {
                return response()->json([
                    'status' => false,
                    'message' => 'Memory not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $memory->id,
                    'title' => $memory->title,
                    'desc' => $memory->desc,
                    'quote' => $memory->quote,
                    'name' => $memory->name,
                    'graduated' => $memory->graduated,
                    'image' => $memory->image
                        ? asset('storage/' . $memory->image)
                        : null,
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch memory',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
