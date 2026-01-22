<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FundRaisa;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Throwable;

class FundraiseApiController extends Controller
{
    /**
     * GET: All Fundraise Goals
     * URL: /api/fundraises
     */
    public function index(): JsonResponse
    {
        try {
            $goal = FundRaisa::with(['generalDonations' => function ($q) {
                $q->latest();
            }])->latest()->first();

            if (! $goal) {
                return response()->json([
                    'status'  => true,
                    'message' => 'No fundraise goal found',
                    'data'    => null,
                ], 200);
            }

            /** @var Collection<int, \App\Models\GeneralDonation> $donations */
            $donations = $goal->generalDonations ?? collect();

            // Only "paid_now" counts as collected amount.
            $paidDonations = $donations->filter(fn ($d) => ($d->donation_mode ?? 'paid_now') === 'paid_now');

            $totalCollected = (float) $paidDonations->sum('amount');
            $totalDonors = (int) $paidDonations->count();

            $goalEnd = (float) ($goal->ending_goal ?? 0);
            $remaining = max($goalEnd - $totalCollected, 0);
            $completionPct = $goalEnd > 0 ? min(($totalCollected / $goalEnd) * 100, 100) : 0;

            return response()->json([
                'status'  => true,
                'message' => 'Latest fundraise goal fetched successfully',
                'data'    => [
                    'goal_start' => $goal->starting_goal,
                    'goal_end' => $goal->ending_goal,
                    'total_donation_collected' => round($totalCollected, 2),
                    'total_donors' => $totalDonors,
                    'remaining_amount' => round($remaining, 2),
                    'goal_completion_percentage' => round($completionPct, 2),
                    'goal' => $goal,
                ],
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch fundraise goals',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: Single Fundraise Goal
     * URL: /api/fundraises/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $fundRaise = FundRaisa::find($id);

            if (!$fundRaise) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Fundraise goal not found'
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Fundraise goal fetched successfully',
                'data'    => $fundRaise
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch fundraise goal',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
