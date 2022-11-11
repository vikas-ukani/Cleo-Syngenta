<?php

namespace App\Nova;

use Laravel\Nova\Panel;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use App\Nova\Flexible\Layouts\CropUseRates;
use Laravel\Nova\Http\Requests\NovaRequest;
use Whitecube\NovaFlexibleContent\Flexible;
use Ebess\AdvancedNovaMediaLibrary\Fields\Files;
use Benjacho\BelongsToManyField\BelongsToManyField;
use App\Nova\Flexible\Presets\ApplicationUseRatePreset;
use Whitecube\NovaFlexibleContent\Concerns\HasFlexible;

class Crop extends Resource
{

    use HasFlexible;
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Crop';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public static $group = 'Disease and Crop';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            new Panel('Basic Information', $this->basicDataFields()),
            new Panel('Categorization', $this->categorizationFields()),
            new Panel('Technical Data', $this->useRateFields()),
            // new Panel('Crop Technical Data', $this->cropTechnicalDataFields()),
            // HasOne::make('Crop Technical Data'),
            new Panel('Content', $this->contentFields()),
            new Panel('Media', $this->mediaFields()),

        ];
    }

    private function useRateFields()
    {
        return [
            Flexible::make('Technical Data', 'technical_data')
                ->preset(ApplicationUseRatePreset::class)
                ->help('Add the technical data for this crop.')
                ->confirmRemove('Are you sure you want to delete this technical data?', 'Delete', 'Cancel'),
        ];
    }

    private function cropTechnicalDataFields()
    {
        return [
            BelongsToMany::make('CropTechnicalData', 'technical_data'),
            // HasOne::make('Crop Technical Data', 'crop_technical_data'),
        ];
    }

    private function categorizationFields()
    {
        return [
            BelongsToManyField::make('Business', 'businesses', 'App\Nova\Business'),
            BelongsToManyField::make('Country', 'countries', 'App\Nova\Country')->help('Which countries are these crops in?'),
            BelongsToManyField::make('Nematodes / Diseases', 'nematodes', 'App\Nova\Disease')->help('Which nematodes is this crop affected by?'),
        ];
    }

    private function basicDataFields()
    {
        return [
            Avatar::make('Image')
                ->disk('public')
                ->disableDownload()
                ->prunable()
                ->nullable()
                ->maxWidth(300),

            Text::make('Crop Name', 'name'),
            Trix::make('Description', 'description')->withFiles('public'),
        ];
    }


    private function contentFields()
    {
        return [
            Flexible::make('Content Sections', 'content')
                ->addLayout('Content', 'additional_content', [
                    Text::make('Title'),
                    Trix::make('Body')->withFiles('public'),
                ])
                ->button('Add Content Section'),
        ];
    }

    private function mediaFields()
    {
        return [
            Files::make('Downloads and Interactive Content', 'crop_content_collection')
                ->nullable()
                ->enableExistingMedia()
                ->customPropertiesFields([
                    Text::make('Filename'),
                    Select::make('File Type')->options([
                        'pdf' => 'PDF',
                        'xslx' => 'Excel Spreadsheet',
                        'pptp' => 'Powerpoint Spreadsheet',
                        'docx' => 'Word Document',
                        'txt' => 'Plain Text',
                        'mov' => 'Quicktime Movie',
                        'mp4' => 'Mpeg4 Movie',
                    ]),
                ]),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
