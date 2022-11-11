<?php

namespace App\Http\Controllers\Syngenta\API;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Country;
use App\Models\Crop;
use App\Models\Disease;
use Illuminate\Http\Request;

class CropController extends Controller
{
    public function getCrops(Request $request)
    {

        $businesses = Business::with(['countries.crops', 'crops.countries'])->get();

        foreach ($businesses as $business) {
            $data['business'][$business->id]['all'] = $business->crops;
            foreach ($business->countries as $country) {
                $data['business'][$business->id]['country'][$country->id] = $country->crops;
            }
            foreach ($business->crops as $crop) {
                $data['business'][$business->id]['crop'][$crop->id] = $crop->countries->pluck('iso_code');
            }
        }


        return response()->json($data);
    }
}
