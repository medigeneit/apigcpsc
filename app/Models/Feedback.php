<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];


    protected $casts = [
        'ratings' => 'json'
    ];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id', 'id')
            ->Latest();
    }

    public function question()
    {
        return $this->belongsTo(FeedbackQuestion::class, 'fq_id', 'id');
    }
    public function appointments()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'id');
    }

    // public function getRatingsAttribute($value)
    // {
    //     $ratings = json_decode($value);
    //     return $ratings;
    // }

    // public function setRatingsAttribute($value)
    // {
    //     $ratings = json_encode($value);
    //     $this->attributes['ratings'] = $ratings;
    // }
}
