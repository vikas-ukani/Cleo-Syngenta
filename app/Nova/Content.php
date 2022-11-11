<?php

namespace App\Nova;

use Ebess\AdvancedNovaMediaLibrary\Fields\Media;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Benjacho\BelongsToManyField\BelongsToManyField;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\KeyValue;
use Benjaminhirsch\NovaSlugField\Slug;
use Benjaminhirsch\NovaSlugField\TextWithSlug;
use Laravel\Nova\Panel;
use Ebess\AdvancedNovaMediaLibrary\Fields\Files;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use MichielKempen\NovaOrderField\Orderable;
use MichielKempen\NovaOrderField\OrderField;


class Content extends Resource
{
//    use Orderable;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Content';

    public static $displayInNavigation = false;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    public static $group = 'Content';

    /**W
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    public static function label()
    {
        return 'Content';
    }

//    public static $defaultOrderField = 'order';

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

            new Panel('Page Title', $this->titleFields()),
            new Panel('Content', $this->contentFields()),
            new Panel('Data', $this->dataFields()),
            new Panel('Categories and Related Nematodes/Crops', $this->categoryFields()),
            new Panel('Media', $this->mediaFields()),
            new Panel('Meta', $this->metaFields()),


        ];
    }

    private function titleFields()
    {
        return [
            TextWithSlug::make('Title')->slug('slug')->rules('required'),
            Heading::make('<p class="text-danger">* All fields are required.</p>')->asHtml()->onlyOnForms(),
        ];
    }

    private function contentFields()
    {
        return [
            Trix::make('Intro')->nullable(),
            Trix::make('Main Content', 'body')->nullable()->withFiles('public'),
        ];
    }

    private function categoryFields()
    {
        return [
            BelongsToManyField::make('Categories', 'categories', 'App\Nova\ContentCategory')->rules('max: 1'),
            BelongsToManyField::make('Business', 'business', 'App\Nova\Business')->help('Which business does this content belong to? This allows you to differentiate content based on the selected business.'),
            BelongsToManyField::make('Countries', 'countries', 'App\Nova\Country')->help('Which countries does this content relate to?'),
            BelongsToManyField::make('Related Nematodes', 'nematodes', 'App\Nova\Disease')->help('Which disease or nematodes does this content related to?')->nullable(true),
            BelongsToManyField::make('Related Crops', 'crops', 'App\Nova\Crop')->help('Which crop does this content related to?')->nullable(true),
        ];
    }

    private function dataFields()
    {
        return [
            KeyValue::make('Custom Data', 'data')
                ->keyLabel('Item')
                ->valueLabel('Label')
                ->actionText('Add Item')
                ->help('Use this field to add custom data'),
        ];
    }

    private function metaFields()
    {
        return [
            BelongsTo::make('Author', 'content_author', 'App\Nova\User')->hideWhenCreating()->display('name'),
            Select::make('Status')->options([
                'draft' => 'Draft',
                'published' => 'Published',
                'private' => 'Private',
            ])->displayUsingLabels()->help('By default, content will be saved as "Draft", unless you choose to publish the content manually, by selecting the "Published" option. Private content will be displayed only to those who know the link.'),
            Slug::make('Slug')->help('This is auto-generated to link directly to the page. There is no need to modify this.')->hideFromIndex(),
//            OrderField::make('Order'),
        ];
    }

    private function mediaFields()
    {
        return [
            Files::make('Downloads and Interactive Content', 'content_multi')
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
