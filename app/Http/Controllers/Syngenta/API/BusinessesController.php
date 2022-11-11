<?php

namespace App\Http\Controllers\Syngenta\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;

class BusinessesController extends Controller
{
    public function getBusinesses(Request $request)
    {
        $businesses = Business::with(['countries.crops', 'countries.nematodes', 'crops', 'nematodes'])->get();

        if ($businesses->isEmpty()) {
            return response()->json(['data' => null, 'lastUpdated' => null]);
        }

        $lastUpdated = $businesses->sortBy('updated_at')->first()->updated_at;

        return response()->json([
            'data' => $businesses,
            'lastUpdated' => $lastUpdated,
        ]);
    }
}
