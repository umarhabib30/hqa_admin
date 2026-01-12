<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SponsorPackage;
use Illuminate\Http\Request;

class SponserPackageApiController extends Controller
{
    public function packages(){
        try{
            $packages = SponsorPackage::all();
            return response()->json([
                'status' => true,
                'data' => $packages
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch sponsor packages',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
