<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    //

    public function countries()
    {
        return $this->hasMany(Country::class, 'regions_id');
    }
}
