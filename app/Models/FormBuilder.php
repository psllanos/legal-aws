<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormBuilder extends Model
{
    protected $fillable = [
        'name',
        'code',
        'created_by',
        'is_active',
        'is_lead_active',
    ];

    public static $fieldTypes = [
        'text' => 'Text',
        'email' => 'Email',
        'number' => 'Number',
        'date' => 'Date',
        'textarea' => 'Textarea',
    ];

    public function form_field()
    {
        return $this->hasMany('App\Models\FormField', 'form_id', 'id');
    }

    public function fieldResponse()
    {
        return $this->hasOne('App\Models\FormFieldResponse', 'form_id', 'id');
    }

    public function response()
    {
        return $this->hasMany('App\Models\FormResponse', 'form_id', 'id');
    }
}
