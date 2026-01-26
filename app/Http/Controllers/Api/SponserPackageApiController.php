<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SponsorPackage;
use App\Models\SponserPackageSubscriber;
use Illuminate\Http\Request;

class SponserPackageApiController extends Controller
{
    public function packages(){
        try{
            // Keep packages response as it was (raw list)
            $packages = SponsorPackage::all();

            // Separate subscribers array grouped by sponsor_type, with asset image urls
            $subscribersGrouped = SponserPackageSubscriber::latest()
                ->get()
                ->map(function ($subscriber) {
                    return [
                        'id' => $subscriber->id,
                        'user_name' => $subscriber->user_name,
                        'user_email' => $subscriber->user_email,
                        'user_phone' => $subscriber->user_phone,
                        'sponsor_package_id' => $subscriber->sponsor_package_id,
                        'sponsor_type' => $subscriber->sponsor_type,
                        'status' => $subscriber->status,
                        'image' => $subscriber->image,
                        'image_url' => $subscriber->image ? asset('storage/' . $subscriber->image) : null,
                        'amount' => $subscriber->amount,
                        'payment_id' => $subscriber->payment_id,
                        'created_at' => $subscriber->created_at,
                        'updated_at' => $subscriber->updated_at,
                    ];
                })
                ->groupBy('sponsor_type')
                ->map(fn ($items) => $items->values())
                ->all();

            // Ensure all package names exist even if empty
            $subscribers = [];
            foreach ($packages as $package) {
                $subscribers[$package->title] = $subscribersGrouped[$package->title] ?? [];
            }

            return response()->json([
                'status' => true,
                'data' => $packages,
                'subscribers' => $subscribers,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch sponsor packages',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
