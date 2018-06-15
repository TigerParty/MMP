<?php
namespace App\Argo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackReply extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $table = 'feedback_reply';
    protected $primaryKey = 'id';
    protected $hidden = ['deleted_at'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'payload' => 'json',
    ];

    public function feedback()
    {
        return $this->belongsTo('App\Argo\Feedback');
    }
}
