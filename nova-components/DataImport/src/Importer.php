<?php

namespace Syngenta\DataImport;

use App\Models\Business;
use App\Models\Country;
use App\Models\Crop;
use Laravel\Nova\Resource;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Row;

class Importer implements OnEachRow, ToModel, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsOnFailure, SkipsOnError
{
    use Importable, SkipsFailures, SkipsErrors;

    /** @var Resource */
    protected $resource;
    protected $attributes;
    protected $attribute_map;
    protected $rules;
    protected $model_class;

    public function model(array $row)
    {
        // return new Crop([
        //     'id' => $row[0],
        //     'name' => $row[1],
        //     'technical_data' => [
        //         'layout' => 'technical_data',
        //         'key' => '',
        //     ]
        // ]);
    }

    public function onRow(Row $row)
    {
        $row = $row->toArray();

        $existCrop = null;


        if (isset($row['crop_id'])) {
            /** make sure we use (or Where) to minimize duplication of same name crops */
            // $model = $model->orWhere('id', $row['crop_id']);
            $existCrop = Crop::where('id', $row['crop_id'])->first();
        }

        /** check if crop id does not exists and fetch by crop name */
        if ($existCrop == null && isset($row['crop'])) {
            /** make a query by crop name to minimize duplications... */
            $existCrop = Crop::where('name', $row['crop'])->first();
            // $model = $model->where('name', 'LIKE', '%' . $row['crop'] . '%'); // Not Applicable
        }

        /** create a business id and country id  */
        $countryId = null;
        $businessId = null;
        if ($country = Country::where('name', $row['country'])->select(['id'])->first()) {
            $countryId = $country->id;
        }
        if ($business = Business::where('name', $row['business'])->select(['id'])->first()) {
            $businessId = $business->id;
        }

        /** make a crop data */
        $data = [
            'name' => $row['crop'],
            'technical_data' => [
                [
                    'layout' => 'technical_data',
                    'key' => uniqid(),
                    'attributes' => [
                        'country_id' => $countryId ?? null,
                        "business_id" => $businessId ?? null,
                        "formulation" =>  $row['formulation'],
                        "a_code" => $row['a_code'],
                        "registration" => $row['registration'],
                        "target" => $row['target'],
                        "treatment_type" => $row['treatment_type_method'],
                        "cgap" => $row['cgap_regulatory_trials']
                    ]
                ]
            ]
        ];

        $createUpdateData = [
            'name' => $data['name'],
            'image' => $data['image'] ?? null,
            'description' => $data['description'] ?? null,
        ];

        /** if crop is exists the update crop and create their technical data */
        if ($existCrop) {
            // ! Working code
            $data['id'] = $existCrop->id;
            /** updating crop by it's id */
            $existCrop->businesses()->detach([$businessId]); // Remove old with same id
            $existCrop->businesses()->attach($businessId); // create new with same id
            // $existCrop->countries()->sync([$countryId]); // it is not added new id, It just update (remove old and add new one)
            $existCrop->countries()->detach([$countryId]);
            $existCrop->countries()->attach($countryId);
            $newCrop = Crop::where('id', $existCrop->id)->update($createUpdateData);
            if ($newCrop == 1) {
                /** if updated then create CROP's Technical Datas */
                Crop::updateAndCreateNewTechnicalDataSubMethod($data['technical_data'], $existCrop->id);
            }
        } else {
            // ! Working code
            /** creating new crop */
            // $newCrop = Crop::create($createUpdateData);
            $newCrop = new Crop();
            $newCrop->name = $data['name'];
            $newCrop->image = $data['image'] ?? null;
            $newCrop->description = $data['description'] ?? null;
            $newCrop->description = $data['description'] ?? null;
            $newCrop->save();
            $newCrop->countries()->attach($countryId);
            $newCrop->businesses()->attach($businessId);
            $data['id'] = $newCrop->id;
            /** create a new CROP's Technical Datas */
            Crop::updateAndCreateNewTechnicalDataSubMethod($data['technical_data'], $newCrop->id);
        }
        return $data;
    }

    public function rules(): array
    {
        return $this->rules;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param mixed $attributes
     * @return Importer
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttributeMap()
    {
        return $this->attribute_map;
    }

    /**
     * @param mixed $map
     * @return Importer
     */
    public function setAttributeMap($map)
    {
        $this->attribute_map = $map;

        return $this;
    }

    /**
     * @param mixed $rules
     * @return Importer
     */
    public function setRules($rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getModelClass()
    {
        return $this->model_class;
    }

    /**
     * @param mixed $model_class
     * @return Importer
     */
    public function setModelClass($model_class)
    {
        $this->model_class = $model_class;

        return $this;
    }

    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    private function mapRowDataToAttributes($row)
    {
        $data = [];

        foreach ($this->attributes as $field) {
            $data[$field] = null;

            foreach ($this->attribute_map as $column => $attribute) {
                if (!isset($row[$column]) || $field !== $attribute) {
                    continue;
                }

                $data[$field] = $this->preProcessValue($row[$column]);
            }
        }

        return $data;
    }

    private function preProcessValue($value)
    {
        switch ($value) {
            case 'FALSE':
                return false;
                break;
            case 'TRUE':
                return true;
                break;
        }

        return $value;
    }
}
