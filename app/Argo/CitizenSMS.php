<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CitizenSMS extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'citizen_sms';

    protected $hidden = [
        'phone_number',
        'deleted_at'
    ];

    protected $fillable = [
        'group_id', 'message', 'phone_number',
        'is_read', 'is_approved', 'submitted_at'
    ];

    protected $casts = [
        'created_at' => 'datetime:c'
    ];

    protected $dates = [
        'submitted_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $appends = [
        'mask_phone'
    ];

    public function getMaskPhoneAttribute($value)
    {
        if (array_has($this->attributes, 'phone_number')) {
            return 'XXXX-XXXX-' . substr($this->attributes['phone_number'], -4);
        }
    }

    public function replies()
    {
        return $this->hasMany('App\Argo\CitizenSMSReply', 'citizen_sms_id');
    }
}
