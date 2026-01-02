<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PtoSubscribeMails;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;

class PtoSubscribeMailApiController extends Controller
{
    /**
     * POST: Subscribe Email
     * URL: /api/pto/subscribe
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:pto_subscribe_mails,email',
            ]);

            $email = PtoSubscribeMails::create([
                'email' => $request->email,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Email subscribed successfully',
                'data'    => $email
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to subscribe email',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
