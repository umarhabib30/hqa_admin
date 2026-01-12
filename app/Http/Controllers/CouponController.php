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
        $coupons = Coupon::withCount(['couponCodes as total_codes', 'couponCodes as used_codes' => function($query) {
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
            'discount_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'quantity' => 'required|integer|min:1|max:1000',
        ]);

        // Ensure at least one discount type is provided
        if (empty($request->discount_price) && empty($request->discount_percentage)) {
            return back()
                ->withErrors(['discount_percentage' => 'Please provide either discount price or discount percentage.'])
                ->withInput();
        }

        // Create the coupon
        $coupon = Coupon::create([
            'coupon_name' => $request->coupon_name,
            'discount_price' => $request->discount_price,
            'discount_percentage' => $request->discount_percentage,
            'quantity' => $request->quantity,
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
        $codes = $coupon->couponCodes()->orderBy('is_used')->orderBy('created_at')->get();
        
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
            'discount_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        // Ensure at least one discount type is provided
        if (empty($request->discount_price) && empty($request->discount_percentage)) {
            return back()
                ->withErrors(['discount_percentage' => 'Please provide either discount price or discount percentage.'])
                ->withInput();
        }

        $coupon->update([
            'coupon_name' => $request->coupon_name,
            'discount_price' => $request->discount_price,
            'discount_percentage' => $request->discount_percentage,
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
        $coupon->delete(); // This will cascade delete all coupon codes

        return redirect()
            ->route('coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }
}
