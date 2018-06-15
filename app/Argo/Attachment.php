<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    public $timestamps = true;

    protected $table = 'attachment';

    protected $primaryKey = 'id';

    public function projects()
    {
        return $this->morphedByMany('App\Argo\Project', 'attachable');
    }

    public function reports()
    {
        return $this->morphedByMany('App\Argo\Report', 'attachable');
    }

    public function trackers()
    {
        return $this->morphedByMany('App\Argo\Tracker', 'attachable');
    }
}
