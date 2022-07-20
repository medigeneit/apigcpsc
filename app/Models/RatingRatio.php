<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RatingRatio extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;
    protected $guarded  = [];

    // static $Rating = [
    //     1 => 'one',
    //     2 => 'two',
    //     3 => 'three',
    //     4 => 'four',
    //     5 => '',
    // ]

    public function feedback_question()
    {
        return $this->belongsTo(FeedbackQuestion::class,'fq_id', 'id');
    }

}
