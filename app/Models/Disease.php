<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disease extends Model
{

    use SoftDeletes;

    public function businesses()
    {
        return $this->belongsToMany(Business::class)->withTimestamps();
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class)->withTimestamps();
    }

    public function crops()
    {
        return $this->belongsToMany(Crop::class)->withTimestamps();
    }
}
