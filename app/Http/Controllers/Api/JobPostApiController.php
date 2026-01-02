<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\jobPost as JobPost;
use Illuminate\Http\JsonResponse;
use Throwable;

class JobPostApiController extends Controller
{
    /**
     * GET: All Job Posts
     * URL: /api/job-posts
     */
    public function index(): JsonResponse
    {
        try {
            $jobPosts = JobPost::latest()->get();

            return response()->json([
                'status'  => true,
                'message' => 'Job posts fetched successfully',
                'data'    => $jobPosts
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch job posts',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: Single Job Post
     * URL: /api/job-posts/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $jobPost = JobPost::find($id);

            if (!$jobPost) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Job post not found'
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Job post fetched successfully',
                'data'    => $jobPost
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch job post',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
