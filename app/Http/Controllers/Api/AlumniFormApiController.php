<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlumniForm;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Illuminate\Validation\ValidationException;
use App\Mail\AlumniFormConfirmationMail;

class AlumniFormApiController extends Controller
{
    /**
     * POST: Submit Alumni Form
     * URL: /api/alumni/form
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'graduation_year' => 'required',
                'status' => 'required',
                'email' => 'required|email|unique:alumni_forms,email',
                'phone' => 'required',
                'password' => 'required|string|min:8',
                'address' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zipcode' => 'required',
                'college' => 'required',
                'degree' => 'required',
                'company' => 'nullable',
                'job_title' => 'nullable',
                'achievements' => 'nullable',
                'image' => 'nullable|image',
                'document' => 'nullable|file',
            ]);

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')
                    ->store('alumni/images', 'public');
            }

            if ($request->hasFile('document')) {
                $data['document'] = $request->file('document')
                    ->store('alumni/documents', 'public');
            }

            $form = AlumniForm::create($data);

            try {
                if (!empty($form->email)) {
                    Mail::to($form->email)->send(new AlumniFormConfirmationMail($form));
                }
            } catch (Throwable $mailException) {
                // Don't break the API if mail fails
            }

            return response()->json([
                'status'  => true,
                'message' => 'Alumni form submitted successfully',
                'data'    => $form
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
                'message' => 'Failed to submit alumni form',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
