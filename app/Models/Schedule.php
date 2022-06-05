<?php

namespace App\Models;

use Carbon\Carbon;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Schedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    static $support_type =null;

    static $mentor_details = false;


    public function scopeSearch($request)
    {
        //try korsi ... hoy nai
    }

    static function encodeTime($rawtime)
    {
        $parts = explode(':', $rawtime);
        return (float)($parts[0] * 60 + floor($parts[1]));
    }

    static function decodeTime($rawtime)
    {
        // return
        $actual_time = $rawtime / 60;
        $time_as_string = sprintf('%02d:%02d', (int) $actual_time, fmod($actual_time, 1) * 60);
        $time_with_date_mix = Carbon::createFromTimeString($time_as_string);
        return $time_with_date_mix->format('h:i A');
    }


    public function setTimeScheduleAttribute($value)
    {
        // $times = explode(",", $value);
        $encode_times = [];
        $encode_times['s'] = self::encodeTime($value['s']);
        $encode_times['e'] = self::encodeTime($value['e']);
        $this->attributes['time_schedule'] = json_encode($encode_times);
    }

    public function getTimeScheduleAttribute($value)
    {
        $times = json_decode($value);
        $time_schedule = [];
        $time_schedule['start_time'] = $this->decodeTime($times->s);
        $time_schedule['end_time'] = $this->decodeTime($times->e);

        return $time_schedule;
    }

    public function getSlotThresholdAttribute($value)
    {
        $slot_threshold = json_decode($value);
        return $slot_threshold;
    }
    // public function setSlotThresholdAttribute($value)
    // {

    //     // $value=[
    //     //     '3' => [
    //     //         "slot" => 20,
    //     //         "threshold" => 3
    //     //     ],
    //     //     '4' => [
    //     //         "slot" => 10,
    //     //         "threshold" => 2
    //     //     ]
    //     // ]
    //     // return json_encode($value);
    //     $this->attributes['slot_threshold'] = json_encode($value);

    // }


    public function getMentorsAttribute($value)
    {
        $mentors = json_decode($value);

        if ($mentors && self::$mentor_details) {
            $arr = [];
            foreach ($mentors as  $key => $mentor) {
                $role = Role::where('id', $key)->pluck('name');
                $arr[$role[0]] =  User::whereIn('id', $mentor)->get(['id', 'name']);
            }
            return $arr;
        }
        return  $mentors;
    }
    // public function setMentorsAttribute($value)
    // {
    //     $this->attributes['mentors'] = json_encode($value);
    // }


    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'schedule_id', 'id');
    }
    // public function assigned_mentors()
    // {
    //     return $this->belongsToMany(::class,  'role_user');
    // }

    // public function appointments()
    // {
    //     return $this->hasMany(Appointment::class, 'schedule_id', 'id')
    //     ->where('type',self::$support_type);
    // }

    public function chamber()
    {
        return ($this->belongsTo(Chamber::class));
    }



    protected $casts = [
        'time_schedule' => 'object',
    ];
}
