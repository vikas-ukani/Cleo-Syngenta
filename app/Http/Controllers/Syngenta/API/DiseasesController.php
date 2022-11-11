<?php

namespace App\Http\Controllers\Syngenta\API;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Country;
use App\Models\Crop;
use App\Models\Disease;
use Illuminate\Http\Request;

class DiseasesController extends Controller
{
    public function getDiseases(Request $request)
    {

        $businesses = Business::with(['countries.nematodes', 'nematodes'])->get();
        foreach ($businesses as $business) {
            $data['business'][$business->id]['all'] = $business->nematodes;
            foreach ($business->countries as $country) {
                $data['business'][$business->id]['country'][$country->id] = $country->nematodes;
            }

        }

        return response()->json($data);
    }
}
