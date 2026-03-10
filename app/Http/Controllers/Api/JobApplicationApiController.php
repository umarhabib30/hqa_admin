<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\JobApplicationConfirmationMail;
use App\Mail\JobApplicationReceivedMail;
use App\Models\JobApp;
use App\Services\MailRecipientResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class JobApplicationApiController extends Controller
{
    /**
     * Store a newly created job application.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'phone'            => 'required|string|max:30',
            'years_experience' => 'required|integer|min:0',
            'cv'               => 'required|file|mimes:pdf,doc,docx',
            'description'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if (!$request->hasFile('cv')) {
                return response()->json([
                    'status' => false,
                    'errors' => [
                        'cv' => ['CV file is required.']
                    ],
                ], 422);
            }

            $cvPath = $request->file('cv')->store('cvs', 'public');

            $jobApp = JobApp::create([
                'first_name'       => $request->input('first_name'),
                'last_name'        => $request->input('last_name'),
                'email'            => $request->input('email'),
                'phone'            => $request->input('phone'),
                'years_experience' => (int) $request->input('years_experience'),
                'cv_path'          => $cvPath,
                'description'      => $request->input('description'),
            ]);

            try {
                Mail::to($jobApp->email)->send(new JobApplicationConfirmationMail($jobApp));
            } catch (\Throwable $e) {
                Log::error('Applicant confirmation email failed', [
                    'job_app_id' => $jobApp->id,
                    'error' => $e->getMessage(),
                ]);
            }

            try {
                $resolver = app(MailRecipientResolver::class);
                $adminEmails = $resolver->resolveByModule('job_applications', static::class . '@store');

                if (!empty($adminEmails)) {
                    Mail::to($adminEmails)->send(new JobApplicationReceivedMail($jobApp));
                }
            } catch (\Throwable $e) {
                Log::error('Internal notification email failed', [
                    'job_app_id' => $jobApp->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Job application submitted successfully.',
                'data'    => $jobApp,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Job application submission failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while submitting the application.',
                'error'   => $e->getMessage(), // remove in production
            ], 500);
        }
    }
}
