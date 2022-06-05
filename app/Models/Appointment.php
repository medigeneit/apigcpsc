<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $table =  'appointment';

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function user_feedbacks()
    {
        return $this->hasOne(Feedback::class, 'appointment_id', 'id')
            ->whereNull('mentor_id')
            ->Latest();
    }
    public function mentor_feedbacks()
    {
        return $this->hasOne(Feedback::class, 'appointment_id', 'id')
            ->whereNotNull('mentor_id')
            ->Latest();
    }
    public function mentor()
    {
        return $this->hasOneThrough(
            User::class,
            Feedback::class,
            'appointment_id',
            'id',
            'id',
            'mentor_id',
        )
            ->whereNotNull('mentor_id')
            ->Latest();
    }


    public function patient()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function requested_mentor()
    {
        return $this->belongsTo(User::class, 'requested_mentor_id', 'id');
    }
    public function assign_mentor()
    {
        return $this->hasOne(MentorAssign::class);
    }


    public function getQuestionsAttribute($value)
    {
        $questions = json_decode($value);
        return $questions;
    }
}
