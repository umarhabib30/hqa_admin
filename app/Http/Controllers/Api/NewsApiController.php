<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Exception;

class NewsApiController extends Controller
{
    /**
     * GET all news
     */
    public function index()
    {
        try {
            $news = News::all()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'image' => $item->image
                        ? asset('storage/' . $item->image)
                        : null,
                    'video_link' => $item->video_link,
                    'social_links' => $item->social_links ?? [],
                    'created_at' => $item->created_at?->toDateTimeString(),
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $news,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch news',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET single news
     */
    public function show($id)
    {
        try {
            $news = News::find($id);

            if (!$news) {
                return response()->json([
                    'status' => false,
                    'message' => 'News not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $news->id,
                    'title' => $news->title,
                    'description' => $news->description,
                    'image' => $news->image
                        ? asset('storage/' . $news->image)
                        : null,
                    'video_link' => $news->video_link,
                    'social_links' => $news->social_links ?? [],
                    'created_at' => $news->created_at?->toDateTimeString(),

                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch news',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
