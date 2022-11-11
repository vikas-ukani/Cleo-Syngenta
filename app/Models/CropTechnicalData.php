<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// implements Sortable, HasMedia
class CropTechnicalData extends Model
{

    // use SortableTrait;
    // use InteractsWithMedia;

    protected $table = 'crop_technical_data';

    protected $fillable = [
        'id',
        'crop_id',
        'business_id',
        'country_id',
        'meta_key',
        'meta_value'
    ];

    // public $sortable = [
    //     'order_column_name' => 'order',
    //     'sort_when_creating' => true,
    // ];

    // protected $casts = ['data' => 'array'];

    // public static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($content) {
    //         $content->author = auth()->user()->id;
    //         $content->order = 0;
    //         $content->status = 'published';
    //     });
    // }

    // public function User()
    // {
    //     return $this->belongsTo('User', 'id', 'id_something');
    // }

    public function business()
    {
        return $this->belongsToMany(Business::class)->withTimestamps();
    }


    public function crops()
    {
        // return $this->hasMany(Crop::class, 'crop_id')->withTimestamps();
        return $this->belongsToMany(Crop::class)->withTimestamps();
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class)->withTimestamps();
    }
}
