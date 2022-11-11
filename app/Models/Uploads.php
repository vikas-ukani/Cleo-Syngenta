<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\Models\Media;

class Uploads extends Model
{
  public function registerMediaConversions(Media $media = null)
  {
    $this->addMediaConversion('thumb')
      ->width(130)
      ->height(130);
  }

  public function registerMediaCollections()
  {
    $this->addMediaCollection('main')->singleFile();
    $this->addMediaCollection('my_multi_collection');
  }
}
