<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlumniMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Validation\ValidationException;

class AlumniMailApiController extends Controller
{
    /**
     * POST: Subscribe Alumni Email
     * URL: /api/alumni/subscribe
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $email = AlumniMail::create([
                'email' => $request->email,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Email subscribed successfully',
                'data'    => $email
            ], 201);
        } catch (ValidationException $e) {

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
