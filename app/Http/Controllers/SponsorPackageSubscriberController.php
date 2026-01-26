<?php

namespace App\Http\Controllers;

use App\Models\SponserPackageSubscriber;

class SponsorPackageSubscriberController extends Controller
{
    public function show(SponserPackageSubscriber $subscriber)
    {
        $subscriber->load('package');

        return view('dashboard.sponsor-packages.subscribers.show', compact('subscriber'));
    }
}

