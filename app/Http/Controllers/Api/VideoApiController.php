<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Viedo as video;
use Exception;

class VideoApiController extends Controller
{
    /**
     * GET all videos
     */
    public function index()
    {
        try {
            $videos = Video::all()->map(function ($video) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'desc' => $video->desc,
                    'video_link' => $video->video_link,
                    'image' => $video->image
                        ? asset('storage/' . $video->image)
                        : null,
                    'created_at' => $video->created_at?->toDateTimeString(),
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $videos,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch videos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET single video
     */
    public function show($id)
    {
        try {
            $video = Video::find($id);

            if (!$video) {
                return response()->json([
                    'status' => false,
                    'message' => 'Video not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $video->id,
                    'title' => $video->title,
                    'desc' => $video->desc,
                    'video_link' => $video->video_link,
                    'image' => $video->image
                        ? asset('storage/' . $video->image)
                        : null,
                    'created_at' => $video->created_at?->toDateTimeString(),
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
