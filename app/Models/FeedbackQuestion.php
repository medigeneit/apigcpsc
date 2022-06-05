<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackQuestion extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];


    public function getQuestionsAttribute($value)
    {
        $questions = json_decode($value);
        return $questions;
    }

    public function feedbacks()
    {
        return $this->hasmany(Feedback::class,'fq_id','id');
    }
}
