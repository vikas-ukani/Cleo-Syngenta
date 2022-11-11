<?php

namespace App\Nova\Flexible\Presets;

use App\Models\Country;
use App\Models\Business;
use App\Models\CropTechnicalKey;
use App\Nova\Flexible\Resolvers\ApplicationUseRateResolver;
use Exception;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Text;
use Whitecube\NovaFlexibleContent\Flexible;
use Whitecube\NovaFlexibleContent\Layouts\Preset;

class ApplicationUseRatePreset extends Preset
{

    protected $countries;
    protected $businesses;

    public function __construct()
    {
        $this->countries = Country::orderBy('name')->get();
        $this->businesses = Business::orderBy('name')->get();
    }

    /**
     * Execute the preset configuration
     *
     * @param Flexible $field
     * @return void
     * @throws Exception
     */
    public function handle(Flexible $field)
    {
        $cropTechnicalKeys =  CropTechnicalKey::select(['id', 'key', 'label'])->distinct('key')->get();

        $field->hideFromIndex();
        $field->button('Add Technical Data');


        $businesses = $this->businesses->reduce(function($values, $business) {
            $values[$business->id] = $business->name;
            return $values;
        }, []);

        $countries = $this->countries->reduce(function($values, $country) {
            $values[$country->id] = $country->name;
            return $values;
        }, []);

        if (isset($cropTechnicalKeys) && count($cropTechnicalKeys) > 0) {
            $cropTechnicalKeys = $cropTechnicalKeys->toArray();
            $fieldData = [
                Select::make('Business', 'business_id')
                    ->options($businesses)
                    ->displayUsingLabels(),

                Select::make('Country', 'country_id')
                    ->options($countries)
                    ->displayUsingLabels(),
            ];

                // Text::make('Formulation'),
                // Text::make('A Code'),
                // Text::make('Target'),
                // Text::make('Treatment Type'),
                // Trix::make('cGap'),
                // Text::make('Registration')

            foreach ($cropTechnicalKeys as $key => $technical_key) {
                $fieldData[] = Text::make($technical_key['label']);
            }

        } else {
             /** default old code */
        // Now we'll create the layout featuring a Select containing all those authors
            $fieldData = [

                Select::make('Business', 'business_id')
                    ->options($businesses)
                    ->displayUsingLabels(),

                Select::make('Country', 'country_id')
                    ->options($countries)
                    ->displayUsingLabels(),

                Text::make('Formulation'),
                Text::make('A Code'),
                Text::make('Target'),
                Text::make('Treatment Type'),
                Trix::make('cGap'),
                Text::make('Registration')
            ];
        }
        $field->addLayout('Technical Data', 'technical_data', $fieldData);
    }
}
