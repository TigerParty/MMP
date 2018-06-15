<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class ReportRaw extends Model
{
    public $timestamps = true;

    protected $table = 'report_raw';

    protected $fillable = ['payload', 'source'];

    public function attachments()
    {
        return $this->morphToMany('App\Argo\Attachment', 'attachable');
    }
}
