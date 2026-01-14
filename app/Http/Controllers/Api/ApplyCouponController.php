<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CouponCode;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplyCouponController extends Controller
{
    /**
     * Apply a coupon code to a given amount.
     */
    public function apply(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric|min:0',
            'coupon_code' => 'required|string',
        ]);

        $code = CouponCode::with('coupon')->where('coupon_code', $data['coupon_code'])->first();

        if (!$code || !$code->coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code.',
            ], 404);
        }

        if ($code->is_used) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon code has already been used.',
            ], 400);
        }

        $coupon = $code->coupon;
        $amount = (float) $data['amount'];
        $discount = 0;

        if ($coupon->coupon_type === 'percentage') {
            if (empty($coupon->discount_percentage) || $coupon->discount_percentage <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Coupon is not configured with a valid percentage.',
                ], 400);
            }
            $discount = round($amount * ($coupon->discount_percentage / 100), 2);
        } else {
            if (empty($coupon->discount_price) || $coupon->discount_price <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Coupon is not configured with a valid amount.',
                ], 400);
            }
            $discount = min($amount, (float) $coupon->discount_price);
        }

        $payable = max($amount - $discount, 0);
        $couponValue = $coupon->coupon_type === 'percentage'
            ? (float) $coupon->discount_percentage
            : (float) $coupon->discount_price;

        DB::transaction(function () use ($code, $data) {
            $code->update([
                'is_used' => true,
                'used_by_email' => $data['email'],
                'used_at' => now(),
            ]);

            CouponUsage::create([
                'coupon_id' => $code->coupon_id,
                'used_by_email' => $data['email'],
                'used_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully.',
            'data' => [
                'coupon_code' => $code->coupon_code,
                'coupon_type' => $coupon->coupon_type,
                'coupon_value' => $couponValue,
                'coupon_value_display' => $coupon->coupon_type === 'percentage'
                    ? $couponValue . '%'
                    : '$' . number_format($couponValue, 2),
                'original_amount' => $amount,
                'discount_applied' => $discount,
                'remaining_amount' => $payable,
            ],
        ]);
    }
}
