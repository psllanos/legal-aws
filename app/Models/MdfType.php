<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdfType extends Model
{
    protected $fillable = [
        'name',
        'created_by',
    ];

    public function subType()
    {
        return $this->hasMany('App\Models\MdfSubType', 'mdf_type', 'id');
    }
}
