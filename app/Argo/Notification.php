<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = true;

    protected $table = 'notification';

    protected $primaryKey = 'id';

    public function project()
    {
        return $this->morphTo('App\Argo\Project', 'notify');
    }

    public function report()
    {
        return $this->morphTo('App\Argo\Report', 'notify');
    }
}
