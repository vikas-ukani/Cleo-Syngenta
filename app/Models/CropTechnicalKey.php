<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CropTechnicalKey extends Model
{
    protected $table = 'crop_technical_key';

    protected $fillable = [
        'id',
        'key',
        'label',
    ];
}
