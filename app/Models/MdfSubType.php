<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdfSubType extends Model
{
    protected $fillable = [
        'mdf_type',
        'name',
        'created_by',
    ];

    public function type()
    {
        return $this->hasOne('App\Models\MdfType', 'id', 'mdf_type');
    }
}
