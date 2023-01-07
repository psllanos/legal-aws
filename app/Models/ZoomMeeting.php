<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoomMeeting extends Model
{
     use HasFactory;
     protected $fillable = [
        'title',
        'meeting_id',
        'lead_id',
        'client_id',
        'password',
        'start_date',
        'duration',
        'start_url',
        'join_url',
        'created_by',
    ];

     protected $appends  = array(
        'lead_name',
        'client_name',
        'user_name',
    );

    public function getLeadNameAttribute($value)
    {
        $lead = Lead::select('id', 'name')->where('id', $this->lead_id)->first();

        return $lead ? $lead->name : '';
    }
    public function getClientNameAttribute($value)
    {
        $client = user::select('id', 'name')->where('id', $this->client_id)->first();

        return $client ? $client->name : '';
    }

    public function getUserNameAttribute($value)
    {
        $user = user::select('id', 'name')->where('id', $this->user_id)->first();

        return $user ? $user->name : '';
    }

    public function checkDateTime(){
        $m = $this;
        if (\Carbon\Carbon::parse($m->start_date)->addMinutes($m->duration)->gt(\Carbon\Carbon::now())) {
            return 1;
        }else{
            return 0;
        }
    }
}
