<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SponsorSubscriberCreatedMail;
use Illuminate\Http\Request;
use App\Models\SponserPackageSubscriber;
use App\Models\SponsorPackage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SponserApiSubscriber extends Controller
{
    public function store(Request $request)
    {
        $image = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('sponsor_package_subscribers', 'public');
        }
        $package_id = SponsorPackage::where('title', $request->sponsor_type)->first()->id;
        $subscriber = SponserPackageSubscriber::create([
            'user_name' => $request->user_name,
            'user_email' => $request->user_email,
            'user_phone' => $request->user_phone,
            'sponsor_package_id' => $package_id,
            'sponsor_type' => $request->sponsor_type,
            'image' => $image,
            'amount' => $request->amount,
            'payment_id' => $request->payment_id,
        ]);

        // Notify admin on each new subscriber (do not block API if mail fails)
        try {
            $subscriber->load('package');
            Mail::to('mumarhabibrb102@gmail.com')->send(new SponsorSubscriberCreatedMail($subscriber));
        } catch (\Throwable $e) {
            Log::warning('Sponsor subscriber email failed', [
                'subscriber_id' => $subscriber->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Subscriber created successfully',
            'data' => $subscriber
        ], 201);
    }
}
