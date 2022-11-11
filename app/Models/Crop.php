<?php

namespace App\Models;

use App\Models\CropTechnicalData;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Whitecube\NovaFlexibleContent\Concerns\HasFlexible;

class Crop extends Model implements HasMedia
{
    use SoftDeletes, HasFlexible, InteractsWithMedia;

    // protected $appends = ['slug'];

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        // 'technical_data' => 'array',
        'content' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($crop) {
            $crop['technical_data'] = static::getTechnicalDataFromTable($crop->id);
        });

        static::updating(function ($crop) {
            if ($crop->technical_data) {
                static::createAndSaveCropTechnicalData($crop);
                $crop->technical_data = null;
            }
        });
        static::created(function ($crop) {
            if ($crop->technical_data) {
                static::createAndSaveCropTechnicalData($crop);
                $crop->technical_data = null;
            }
            $crop->technical_data = null;
            $crop->save();
        });

        static::deleted(function ($crop) {
            CropTechnicalData::where('crop_id', $crop->id)->delete();
        });
    }

    /**
     * Get Technical Data
     *
     * @param int $crop_id
     * @return array|object
     */
    public static function getTechnicalDataFromTable($crop_id)
    {
        $allCropTechnicalKeys = static::getFilteredCropTechnicalKeysFromDB();

        // $sql = "select crop_id,business_id, country_id,group_concat(concat(meta_key,':',meta_value) separator ',') as detail from crop_technical_data WHERE crop_id = 184 group by business_id, country_id";
        // $tempData = \DB::query($sql);

        // $tempData = CropTechnicalData::select('crop_id', 'business_id', 'country_id')->where('crop_id','=','184')->groupBy('crop_technical_data.business_id')->get();
        $tempData = DB::table('crop_technical_data')->where('crop_id', $crop_id)
            ->select(['crop_id','business_id','country_id', DB::raw("group_concat(concat(meta_key,':',meta_value) separator ',') as detail")])
            ->groupBy(['crop_id', 'business_id','country_id'])
            ->get();
        
        $results = $tempData->toArray();

        $newFormedData= [];
        $i = 0;
        foreach ($results as $key => $data) {

            $finData = [];
            foreach (explode(',', $data->detail) as $key => $value) {
                $splitData = explode(':', $value) ;
                $finData[$splitData[0]] = $splitData[1];
            }
            // $tData = ;
            
            // $finData1 = array_merge($finData, [
            //     'business_id' =>  $data->business_id,
            //     'country_id' =>  $data->country_id,
            //     'crop_id' =>  $data->crop_id,
            // ]);
            
            $newFormedData[] = [
                "layout" => "technical_data",
                "key" => uniqid(),
                "attributes" => array_merge($finData, [
                    'business_id' =>  $data->business_id,
                    'country_id' =>  $data->country_id,
                    'crop_id' =>  $data->crop_id,
                ])
            ];
        }
        return  $newFormedData ;

        /** get all crops key details */
        $allCropTechnicalData = CropTechnicalData::where('crop_id', $crop_id)->get();
        $allBusiness  = array_values(collect($allCropTechnicalData)->pluck('business_id')->all());
        $allCountry = array_values(collect($allCropTechnicalData)->pluck('country_id')->all());

        $data = [];
        foreach ($allBusiness as $bId) {
            foreach ($allCountry as $cId) {

                $cropTechnicalDatas = collect($allCropTechnicalData)->where('business_id', $bId)
                    ->where('country_id', $cId)
                    ->where('crop_id', $crop_id)
                    ->all();

                $cropTechnicalDatas = collect($cropTechnicalDatas)->toArray();
                 
                $uniqueData = [];
                foreach ($cropTechnicalDatas as $key => $cropTechnicalData) {
                    $keyWiseData = [];
                    foreach ($allCropTechnicalKeys as $key => $value) {
                        if ($cropTechnicalData['meta_key'] == $key  && isset($cropTechnicalData['meta_value'])) {
                            // && isset($cropTechnicalData['meta_value'])
                            $keyWiseData[$key] = $cropTechnicalData['meta_value'] ?? null;
                        }
                    }
                    $uniqueData[] = $keyWiseData;
                    // $uniqueData[] = array_merge(
                    //     $allCropTechnicalKeys,
                    //     $keyWiseData
                    // );
                }
                $unqData = collect($cropTechnicalDatas)->unique('meta_key')->mapWithKeys(function ($item) {
                    return [$item['meta_key'] => $item['meta_value']];
               })->all();
                
                $newFormedData = [];
                // foreach ($uniqueData as $key => $datas) {
                //     foreach ($datas as $key => $value) {
                //         if (isset($datas[$key])) {
                //             $newFormedData[$key] = $value;
                //         }
                //     }
                // }

                /** get all key wise values from array object */
                $uniqueData = array_map('array_filter', $uniqueData);
                $newFormedData = collect($uniqueData)->mapWithKeys(function ($item) {
                     return [array_key_first($item) => $item[array_key_first($item)]];
                });
 
                $newFormedData['business_id'] = $bId;
                $newFormedData['country_id'] = $cId;
                $newFormedData['crop_id'] = $crop_id;

                $data[] = $newFormedData; // working
            }
        }
        /** make a reverse loop to get all data */
        /** make it unique data */
        $allBusiness = array_unique($allBusiness);
        $allCountry = array_unique($allCountry);

        // $finalizeData = [];
        // $mainSampleData = null;
        $returnData = [];
        foreach ($allBusiness as $key => $businessID) {
            foreach ($allCountry as $key => $countryID) {
                $sampleData = null;
                $check = collect($returnData)->where('attributes.business_id', $businessID)->where('attributes.country_id', $countryID)->first(); // NEW
                if ($check == null) {
                    $filtered = collect($data)->where('business_id', $businessID)->where('country_id', $countryID)->first();
                    if (isset($filtered) && count($filtered) > 3) {
                        $sampleData = [
                            "layout" => "technical_data",
                            "key" => uniqid(),
                            "attributes" => $filtered
                        ];
                    }
                }
                if (isset($sampleData)) {
                    $returnData[] = $sampleData;
                }
            }

            /** check again if already data exists in finalizeData array? */
            // $check = collect($finalizeData)->where('business_id', $businessID)->where('country_id', $countryID)->first();
            // if ($check == null) {
            //     $filtered = collect($data)->where('business_id', $businessID)->where('country_id', $countryID)->first();
            //     if (isset($filtered)) {
            //         $mainSampleData = $sampleData;
            //     }
            // }
            // $finalizeData[] = $mainSampleData;
        }
        return $returnData;
    }

    /**
     * Get filter crop technical keys from database
     *
     * @return array|object
     */
    public static function getFilteredCropTechnicalKeysFromDB()
    {
        /** get all keys from technical keys table */
        $allCropTechnicalKeys = CropTechnicalKey::select(['id', 'key'])->get();
        $allCropTechnicalKeys = collect($allCropTechnicalKeys)->pluck('key')->all();
        $allCropTechnicalKeys = array_flip($allCropTechnicalKeys); // Exchanges all keys with their associated values in an array
        $allCropTechnicalKeys = array_map(function () {
            return null;
        }, $allCropTechnicalKeys);
        return $allCropTechnicalKeys;
    }

    /**
     * Create And Update Crop Technical Datas
     *
     * @param array|object $crop
     * @return array|object
     */
    public static function createAndSaveCropTechnicalData($crop)
    {
        // Finds whether a variable is an object
        if (is_object($crop)) {
            $cropId = $crop->id;
            $technical_datas = $crop->technical_data->toArray();
        } else {
            $cropId = $crop['id'];
            $technical_datas = $crop['technical_data'];
        }

        /** checking for updating or Creating */
        if (isset($cropId)) {
            CropTechnicalData::where('crop_id', $cropId)->delete();
        }

        static::updateAndCreateNewTechnicalDataSubMethod($technical_datas, $cropId);
    }

    /**
     * Store and Update Crop Technical Data
     *
     * @param array $technical_datas
     * @param int $cropId
     * @return void
     */
    public static function updateAndCreateNewTechnicalDataSubMethod(array $technical_datas, $cropId)
    {
        foreach ($technical_datas as $key => $data) {
            $business_id = (int) $data['attributes']['business_id'] ?? null;
            $country_id = (int) $data['attributes']['country_id'] ?? null;

            /** first remove all existing crop technical data by filters */
            // $deletedData = CropTechnicalData::where('business_id', $business_id)->where('country_id', $country_id)->where('crop_id', $cropId)->get();
            // dd('created', $business_id, $country_id, $cropId, $deletedData->toArray());

            /** instead of removing country wise and business wise removing, We remove all by crop id wise.  */

            // CropTechnicalData::where('business_id', $business_id)->where('country_id', $country_id)->where('crop_id', $cropId)->delete();

            /** remove unnecessary attributes from attribute array */
            unset($data['attributes']['business_id'], $data['attributes']['country_id']);
            /** make a repeat to create an array and store in database */
            foreach ($data['attributes'] as $key => $value) {
                if (isset($data['attributes'][$key])) {
                    $matchingCriteria = [
                        'business_id' => $business_id,
                        'country_id' => $country_id,
                        'crop_id' => $cropId ?? null,
                        'meta_key' => $key
                    ];
                    /** first create and then update old one */
                    // CropTechnicalData::updateOrCreate(
                    //     $matchingCriteria,
                    CropTechnicalData::create(
                        array_merge($matchingCriteria, [
                            'meta_value' => $value
                        ])
                    );
                }
            }
        }
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('crop_content_collection');
    }

    public function cropTechnicalData()
    {
        return $this->hasOne(CropTechnicalData::class);
    }

    public function getFlexibleContentAttribute()
    {
        return $this->flexible('technical_data');
    }

    public function businesses()
    {
        return $this->belongsToMany(Business::class)->withTimestamps();
    }

    public function nematodes()
    {
        return $this->belongsToMany(Disease::class, 'crop_disease')->withTimestamps();
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class)->withTimestamps();
    }
}
