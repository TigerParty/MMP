<?php
namespace App\Argo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $table = 'feedback';
    protected $primaryKey = 'id';
    protected $hidden = ['deleted_at'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime:c'
    ];

    public function feedbackReplies()
    {
        return $this->hasMany('App\Argo\FeedbackReply', 'feedback_id', 'id');
    }

    public function feedbackable()
    {
        return $this->morphTo();
    }

    public function scopeComment($query)
    {
        return $query->where('type', '=', 'comment');
    }
}
