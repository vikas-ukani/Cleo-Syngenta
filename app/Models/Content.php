<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Content extends Model implements Sortable, HasMedia
{

    use SortableTrait;
    use InteractsWithMedia;

    protected $table = 'content';

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    protected $casts = ['data' => 'array'];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($content) {
            $content->author = auth()->user()->id;
            $content->order = 0;
            $content->status = 'published';
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('content_multi');
    }

    public function content_author()
    {
        return $this->belongsTo('App\User', 'author');
    }

    public function business()
    {
        return $this->belongsToMany(Business::class)->withTimestamps();
    }

    public function nematodes()
    {
        return $this->belongsToMany(Disease::class)->withTimestamps();
    }

    public function crops()
    {
        return $this->belongsToMany(Crop::class)->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(ContentCategory::class)->withTimestamps();
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class)->withTimestamps();
    }

}
