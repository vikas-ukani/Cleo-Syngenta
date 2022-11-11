<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Business extends Model
{
    public static function boot()
    {
        parent::boot();
        static::creating(function ($business) {
            $business->shortname = Str::slug($business->name);
        });
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class)->orderBy('name');
    }

    public function nematodes()
    {
        return $this->belongsToMany(Disease::class)->orderBy('name');
    }

    // public function crops()
    // {
    //     return $this->hasMany(Crop::class)->orderBy('name');
    // }
    public function crops()
    {
        return $this->belongsToMany(Crop::class, 'business_crop', 'business_id', 'crop_id' );
    }

    public function content()
    {
        return $this->belongsToMany(Content::class);
    }
}
