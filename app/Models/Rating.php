<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rating extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'sum_ratings' => 'json'
    ];

    public function feedback_question()
    {
        return $this->belongsTo(FeedbackQuestion::class,'fq_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
