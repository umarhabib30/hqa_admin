<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponCode;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the coupons.
     */
    public function index()
    {
        $coupons = Coupon::withCount(['couponCodes as total_codes', 'couponCodes as used_codes' => function ($query) {
            $query->where('is_used', true);
        }])->latest()->get();

        return view('dashboard.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new coupon.
     */
    public function create()
    {
        return view('dashboard.coupons.create');
    }

    /**
     * Generate a unique 8-character alphanumeric coupon code
     */
    private function generateCouponCode()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        $maxAttempts = 100;
        $attempts = 0;

        do {
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $attempts++;
        } while (CouponCode::where('coupon_code', $code)->exists() && $attempts < $maxAttempts);

        if ($attempts >= $maxAttempts) {
            throw new \Exception('Unable to generate unique coupon code. Please try again.');
        }

        return $code;
    }

    /**
     * Store a newly created coupon in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'coupon_name' => 'required|string|max:255',
            'coupon_type' => 'required|in:amount,percentage',
            'discount_price' => 'nullable|numeric|min:0.01|required_if:coupon_type,amount',
            'discount_percentage' => 'nullable|numeric|min:0.01|max:100|required_if:coupon_type,percentage',
            'quantity' => 'required|integer|min:1|max:1000',
            'seats_allowed' => 'required|integer|min:1|max:1000',
        ]);

        // Create the coupon
        $coupon = Coupon::create([
            'coupon_name' => $request->coupon_name,
            'coupon_type' => $request->coupon_type,
            'discount_price' => $request->coupon_type === 'amount' ? $request->discount_price : null,
            'discount_percentage' => $request->coupon_type === 'percentage' ? $request->discount_percentage : null,
            'quantity' => $request->quantity,
            'seats_allowed' => (int) $request->seats_allowed,
        ]);

        // Generate unique coupon codes
        $codes = [];
        for ($i = 0; $i < $request->quantity; $i++) {
            $codes[] = [
                'coupon_id' => $coupon->id,
                'coupon_code' => $this->generateCouponCode(),
                'is_used' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk insert for performance
        CouponCode::insert($codes);

        return redirect()
            ->route('coupons.index')
            ->with('success', "Coupon created successfully with {$request->quantity} unique codes.");
    }

    /**
     * Display all coupon codes for a specific coupon.
     */
    public function showCodes($id)
    {
        $coupon = Coupon::with('couponCodes')->findOrFail($id);
        $codes = $coupon
            ->couponCodes()
            ->orderByRaw('
                CASE
                    WHEN is_used = 0 AND is_copied = 0 THEN 1
                    WHEN is_used = 0 AND is_copied = 1 THEN 2
                    WHEN is_used = 1 THEN 3
                END
            ')
            ->orderBy('created_at')
            ->get();

        return view('dashboard.coupons.codes', compact('coupon', 'codes'));
    }

    /**
     * Show the form for editing the specified coupon.
     */
    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);

        // Don't allow editing quantity if codes already exist
        $existingCodes = $coupon->couponCodes()->count();

        return view('dashboard.coupons.edit', compact('coupon', 'existingCodes'));
    }

    /**
     * Update the specified coupon in storage.
     */
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'coupon_name' => 'required|string|max:255',
            'coupon_type' => 'required|in:amount,percentage',
            'discount_price' => 'nullable|numeric|min:0.01|required_if:coupon_type,amount',
            'discount_percentage' => 'nullable|numeric|min:0.01|max:100|required_if:coupon_type,percentage',
            'seats_allowed' => 'required|integer|min:1|max:1000',
        ]);

        $coupon->update([
            'coupon_name' => $request->coupon_name,
            'coupon_type' => $request->coupon_type,
            'discount_price' => $request->coupon_type === 'amount' ? $request->discount_price : null,
            'discount_percentage' => $request->coupon_type === 'percentage' ? $request->discount_percentage : null,
            'seats_allowed' => (int) $request->seats_allowed,
        ]);

        return redirect()
            ->route('coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    /**
     * Remove the specified coupon from storage.
     */
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();  // This will cascade delete all coupon codes

        return redirect()
            ->route('coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    /**
     * Mark coupon code as copied
     */
    public function markAsCopied(Request $request)
    {
        $request->validate([
            'code_id' => 'required|exists:coupon_codes,id',
        ]);

        $code = CouponCode::findOrFail($request->code_id);

        // Only update if not already copied
        if (!$code->is_copied) {
            $code->update([
                'is_copied' => true,
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
