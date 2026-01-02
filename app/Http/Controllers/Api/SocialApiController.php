<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Social;
use Exception;

class SocialApiController extends Controller
{
    /**
     * GET all socials
     */
    public function index()
    {
        try {
            $socials = Social::all()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'desc' => $item->desc,
                    'image' => $item->image ? asset('storage/' . $item->image) : null,
                    'fblink' => $item->fblink,
                    'ytlink' => $item->ytlink,
                    'instalink' => $item->instalink,
                    'tiktoklink' => $item->tiktoklink,
                    'created_at' => $item->created_at?->toDateTimeString(),
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $socials,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch social links',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET single social link
     */
    public function show($id)
    {
        try {
            $social = Social::find($id);

            if (!$social) {
                return response()->json([
                    'status' => false,
                    'message' => 'Social link not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $social->id,
                    'title' => $social->title,
                    'desc' => $social->desc,
                    'image' => $social->image ? asset('storage/' . $social->image) : null,
                    'fblink' => $social->fblink,
                    'ytlink' => $social->ytlink,
                    'instalink' => $social->instalink,
                    'tiktoklink' => $social->tiktoklink,
                    'created_at' => $social->created_at?->toDateTimeString(),
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch social link',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
