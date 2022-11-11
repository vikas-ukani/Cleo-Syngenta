<?php

namespace App\Nova\Flexible\Resolvers;

use Whitecube\NovaFlexibleContent\Value\ResolverInterface;

class ApplicationUseRateResolver implements ResolverInterface
{
    /**
     * get the field's value
     *
     * @param  mixed  $resource
     * @param  string $attribute
     * @param  Whitecube\NovaFlexibleContent\Layouts\Collection $layouts
     * @return Illuminate\Support\Collection
     */
    public function get($resource, $attribute, $layouts)
    {

        // First we'll fetch the authors linked to the resource
        $countries = $resource->countries()->get();

        // Now we'll map them into the "Book author" layout
        return $countries->map(function($country, $key) use ($layouts) {
            $layout = $layouts->find('use_rates');

            // Using duplicateAndHydrate, we'll create a copy of the original
            // Layout and fill it with the current author's data
            return $layout->duplicateAndHydrate($key, ['country_select' => $country->id]);
        });
    }

    /**
     * Set the field's value
     *
     * @param  mixed  $model
     * @param  string $attribute
     * @param  Illuminate\Support\Collection $groups
     * @return string
     */
    public function set($model, $attribute, $groups)
    {

    }
}
