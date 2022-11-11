<?php

namespace App\Http\Controllers\Syngenta\API;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Content;
use App\Models\Crop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{

    public $returnContent = null;
    public $menuData = [];
    public $crops = [];
    public $nematodes = [];
    public $selectedCrop = null;

    public function getLatestUpdatedContent(Request $request)
    {
        $tableNames = ['businesses', 'business_country', 'business_crop', 'business_disease', 'crops', 'crop_disease', 'countries', 'country_crop', 'country_disease', 'diseases'];
        $subquery = '';
        $count = 0;
        foreach($tableNames as $tableName){
            $subquery .= "select max(`updated_at`) as `lastUpdated` from $tableName\r\n";
            if($count < count($tableNames) - 1)
                $subquery .= "union\r\n";

            $count++;
        }
        $query = "select max(`lastUpdated`) as `contentLastUpdated` from (\r\n$subquery) as `checkContentLastUpdated`;";

        $lastUpdated = DB::select( DB::raw( $query ) );
        if(count($lastUpdated) > 0){
            return response()->json(['lastUpdated' => $lastUpdated[0]->contentLastUpdated]);
        }
        return false;

    }

    public function getContent(Request $request)
    {

        if (array_key_exists('business', $request->params)) {
            // 
            $business = Business::with(['content', 'crops', 'nematodes', 'countries'])
                // ->where('id', $request->params['business'])
                ->where('shortname', $request->params['business'])
                ->get();
            
            if (isset($business) && count($business) > 0){
                $business = $business[0];

                if ($request->params['crop']) {
                    foreach ($business->crops as $crop) {
                        if ($crop->name !== '') {
                            if (Str::slug($crop->name) === $request->params['crop']) {
                                $crop->getMedia();
                                $this->selectedCrop = $crop;
                                break;
                            }
                        }
                    }
                }
            }
               

            Log::info($this->selectedCrop);

//            if () {
//                foreach ($business[0]->crops as $crop) {
//                    $crop->slug = Str::slug($crop->name);
//
//                    $this->crops[] = $crop;
//                }
//            }

//            if ($business->nematodes) {
//                foreach ($business->nematodes as $nematode) {
//                    $nematode->slug = Str::slug($nematode->name);
//                    $this->nematodes[] = $nematode;
//                }
//            }

//            $this->menuData = [
//                'crops' => $this->crops,
//                'nematodes' => $this->nematodes,
//            ];
        }

//        // We have a slug, so get the content related to the slug
//        if (array_key_exists('slug', $request->params)) {
//            $business = Content::with(['countries.regions', 'media', 'nematodes', 'business', 'crops', 'categories']);
//            $this->returnContent = $business->where('slug', $request->params['slug'])->get();
//        }

        return response()->json(['crop' => $this->selectedCrop]);


    }
}
