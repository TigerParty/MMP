<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ReportCitizen extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'report_citizen';

    protected $primaryKey = 'id';

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime:c'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'email', 'phone', 'comment', 'source', 'version',
        'lat', 'lng', 'is_read', 'meta'
    ];

    public function attachments()
    {
        return $this->morphToMany('App\Argo\Attachment', 'attachable')->using('App\AttachablePivot');
    }
}
