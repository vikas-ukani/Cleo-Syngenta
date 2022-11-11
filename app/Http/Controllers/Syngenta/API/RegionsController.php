<?php

namespace App\Http\Controllers\Syngenta\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Region;

class RegionsController extends Controller
{
    public function getRegions(Request $request)
    {
        $regions = Region::with('countries.nematodes')->get();
        return response()->json($regions);
    }
}
