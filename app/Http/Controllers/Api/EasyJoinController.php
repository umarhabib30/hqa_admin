<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EasyJoin;
use App\Models\feePersonPrice;
use Illuminate\Http\Request;

class EasyJoinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $joins = EasyJoin::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $joins
        ], 200);
    }

    /**
     * POST: Store Easy Join
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'first_name'   => 'required|string',
                'last_name'    => 'required|string',
                'email'        => 'required|email|unique:easy_joins,email',
                'is_attending' => 'required|in:yes,no',
                'guest_count'  => 'required|integer|min:1', // total people
            ]);

            // Get active fee (admin controlled)
            $fee = FeePersonPrice::where('is_active', true)->first();

            if (!$fee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Fee not configured by admin'
                ], 422);
            }

            if ($data['is_attending'] === 'no') {
                $data['fee_per_person'] = 0;
                $data['total_fee'] = 0;
            } else {
                $data['fee_per_person'] = $fee->price;
                $data['total_fee'] = $data['guest_count'] * $fee->price;
            }

            $data['fee_person_price_id'] = $fee->id;

            $join = EasyJoin::create($data);

            return response()->json([
                'status' => true,
                'message' => 'RSVP submitted successfully',
                'data' => $join
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
