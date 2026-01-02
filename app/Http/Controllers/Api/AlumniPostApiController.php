<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlumniPost;
use Illuminate\Http\JsonResponse;
use Throwable;

class AlumniPostApiController extends Controller
{
    /**
     * GET: All Alumni Posts
     * URL: /api/alumni-posts
     */
    public function index(): JsonResponse
    {
        try {
            $posts = AlumniPost::latest()->get();

            return response()->json([
                'status'  => true,
                'message' => 'Alumni posts fetched successfully',
                'data'    => $posts
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch alumni posts',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: Single Alumni Post
     * URL: /api/alumni-posts/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $post = AlumniPost::find($id);

            if (!$post) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Alumni post not found'
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Alumni post fetched successfully',
                'data'    => $post
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch alumni post',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
