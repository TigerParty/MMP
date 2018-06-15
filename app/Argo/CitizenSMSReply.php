<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class CitizenSMSReply extends Model
{
    public $timestamps = true;

    protected $table = 'citizen_sms_reply';

    protected $fillable = [
        'citizen_sms_id', 'message'
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime:c'
    ];

    public function citizenSms()
    {
        return $this->belongsTo('App\Argo\CitizenSMS');
    }
}
