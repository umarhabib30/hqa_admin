<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\jobApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class JobApplicationApiController extends Controller
{
    /**
     * Store a newly created job application.
     */
    public function store(Request $request)
    {
        // 🔹 Validate request
        $validator = Validator::make($request->all(), [
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|email|max:255',
            'phone'             => 'required|string|max:30',
            'years_experience'  => 'required|integer|min:0',
            'cv'                => 'required|mimes:pdf,doc,docx|max:2048',
            'description'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // 🔹 Upload CV
        $cvPath = $request->file('cv')->store('cvs', 'public');

        // 🔹 Save to DB
        $jobApp = jobApp::create([
            'first_name'       => $request->first_name,
            'last_name'        => $request->last_name,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'years_experience' => $request->years_experience,
            'cv_path'          => $cvPath,
            'description'      => $request->description,
        ]);

        // 🔹 API response
        return response()->json([
            'status'  => true,
            'message' => 'Job application submitted successfully',
            'data'    => $jobApp,
        ], 201);
    }
}
