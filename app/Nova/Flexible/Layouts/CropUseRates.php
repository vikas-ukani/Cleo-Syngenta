<?php

namespace App\Nova\Flexible\Layouts;

use Benjacho\BelongsToManyField\BelongsToManyField;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Trix;
use Whitecube\NovaFlexibleContent\Layouts\Layout;

class CropUseRates extends Layout
{
    /**
     * The layout's unique identifier
     *
     * @var string
     */
    protected $name = 'use_rates';

    /**
     * The displayed title
     *
     * @var string
     */
    protected $title = 'Application Use Rates';

    /**
     * Get the fields displayed by the layout.
     *
     * @return array
     */
    public function fields()
    {
        return [
            HasMany::make('Country', 'countries', 'App\Nova\Country'),
            BelongsToManyField::make('Business', 'businesses', 'App\Nova\Business')->help('Which business does this application rate belong to?'),
//            BelongsToManyField::make('Country', 'countries', 'App\Nova\Country'),
            Trix::make('Application Rate'),
        ];
    }

}
