<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlumniForm;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Validation\ValidationException;

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
