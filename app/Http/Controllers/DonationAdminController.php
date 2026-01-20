<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralDonation;

class DonationAdminController extends Controller
{
    public function index()
    {
        // Fetch all donations, newest first
        $donations = GeneralDonation::latest()->get();

        // Path matches: resources/views/dashboard/gernalDonation/donations_index.blade.php
        return view('dashboard.gernalDonation.donations_index', compact('donations'));
    }
}
