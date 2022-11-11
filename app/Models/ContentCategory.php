<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContentCategory extends Model
{
  public static function boot()
  {
    parent::boot();
    static::creating(function ($category) {
      $category->slug = Str::slug($category->name);
    });
  }

  public function parent()
  {
    return $this->belongsTo('App\Models\ContentCategory');
  }

  public function children()
  {
    return $this->hasMany('App\Models\ContentCategory');
  }

  public function content()
  {
    return $this->belongsToMany('App\Models\Content')->withTimestamps();
  }
}
