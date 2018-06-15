<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class NotificationSMS extends Model
{
    public $timestamps = true;

    protected $table = 'notification_sms';

    protected $primaryKey = 'id';

    public function project()
    {
        return $this->morphTo('App\Argo\Project', 'notify');
    }
}
