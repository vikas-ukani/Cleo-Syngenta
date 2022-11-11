<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    //

    public function region()
    {
        return $this->hasOne(Region::class);
    }

    public function businesses()
    {
        return $this->belongsToMany(Business::class)->withTimestamps();
    }

    public function regions()
    {
        return $this->belongsTo(Region::class);
    }

    public function crops()
    {
        return $this->belongsToMany(Crop::class)->withTimestamps();
    }

    public function nematodes()
    {
        return $this->belongsToMany(Disease::class)->withTimestamps();
    }
}
