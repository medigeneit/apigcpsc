<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    // protected $appends = ['question_retting'];


    static $Types = [
        0 => 'Patient',
        1 => 'Mentor',
    ];


    public function setQuestionsAttribute($value)
    {
        $this->attributes['questions'] = json_encode($value);
    }
    public function getQuestionsAttribute($value)
    {
        $questions = json_decode($value);
        return $questions;
    }

    public function feedbacks()
    {
        return $this->hasmany(Feedback::class, 'fq_id', 'id');
    }

    public function user_ratings()
    {
        return $this->hasmany(Rating::class, 'fq_id', 'id');
    }
    public function rating_ratio()
    {
        return $this->hasmany(RatingRatio::class, 'fq_id', 'id');
    }


    // public function getQuestionsRettingAttribute(Type $var = null)
    // {
    //     $rettings = [];
    //     foreach($this->questions as $key=>$question){
    //         $rettings[$question]
    //     }
    //     $this->feedbacks
    // }


}
