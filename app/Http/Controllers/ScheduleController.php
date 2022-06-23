<?php

namespace App\Http\Controllers;

use App\Http\Resources\mentorScheduleResource;
use App\Http\Resources\ScheduleResource;
use App\Models\Appointment;
use App\Models\Chamber;
use App\Models\Feedback;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Boolean;
use Spatie\Permission\Models\Permission;

class ScheduleController extends Controller
{


    // public function __construct()
    // {
    //     $this->middleware('can:Schedule List')->only('index', 'show');
    //     $this->middleware('can:Schedule Create')->only('create', 'store');
    //     $this->middleware('can:Schedule Edit')->only('edit', 'update');
    //     $this->middleware('can:Schedule Download')->only('exportExcel');
    //     // $this->middleware('can:Schedule Delete')->only('destroy');
    // }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $support_types = Role::query()
            ->where('type', 2)
            ->pluck('name', 'id');

        $schedules = Schedule::query()
            ->get()
            ->makeHidden([
                'created_at',
                'updated_at',
                'deleted_at',
            ]);

        $schedules->load([
            'appointments:id,schedule_id,type',
            'appointments.assign_mentor:id,mentor_id,appointment_id',
            'appointments.assign_mentor.user:id,name',
        ]);

        $mentors = [];

        $schedule_map = $schedules->map(function ($schedule) {
            $mentors = $schedule->appointments->whereNotNull('assign_mentor')->groupBy('type');

            $mentors = $mentors->map(function ($mentor, $key) {
                return $mentor->pluck('assign_mentor.user.name', 'assign_mentor.user.id');
            });

            $schedule->mentors = $mentors;

            $appointments = $schedule->appointments->groupBy('type');

            $appointments = $appointments->map(function ($appointment, $key) {
                return $appointment->count();
            });

            $schedule->unsetRelation('appointments');

            $schedule->appointments = $appointments;
        });




        return [
            'support_types' => $support_types,
            'schedules' => $schedules,
            // 'schedule_map' =>  $schedule_map
        ];
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return
        $chambers = Chamber::get();
        // return $chambers;
        // $roles = Role::select('id', 'name')->where('type', 2)->get();
        $support_type = Role::select('id', 'name')->with('users')->where('type', 2)->get();
        return [
            'chambers' => $chambers,
            'default_slot' => 10,
            'default_threshold' => 3,
            'support_type' => $support_type
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */




    public function store(Request $request)
    {

        // return json_encode([$request->support_type_id => [
        //     'slot' => $request->slot,
        //     'threshold' => $request->threshold]]);

        // json_encode($request->mentors);
        // return $request;
        $slot_threshold_db = [];
        foreach ($request->slot_thresholds as $slot_threshold) {
            $slot_threshold_db[$slot_threshold['support_type_id']] = [
                'slot' => $slot_threshold['slot'],
                'threshold' => $slot_threshold['threshold']
            ];
        }
        // return json_encode($slot_threshold_db);
        // return $slot_threshold['support_type_id'];


        return
            $schedule = Schedule::create([
                'time_schedule' => ['s' => $request->s_time, 'e' => $request->e_time],
                'chamber_id' => $request->chamber_id,
                'date' => $request->date,
                // 'slot_threshold' =>[$request->cc_slot,$request->p_slot]
                // 'slot_threshold' => $request->slot_threshold,
                // 'slot_threshold' =>json_encode([$request->support_type_id => [
                //     'slot' => $request->slot,
                //     'threshold' => $request->threshold
                // ]]),
                'slot_threshold' => json_encode($slot_threshold_db),
                'mentors' => $request->mentors,
                'active' => $request->active,
                // 'mentors' => [
                //     '3' => [2, 6],
                //     '4' => [1, 5]
                // ],
            ]);


        if (!$schedule) {
            return [
                'message' => 'Something is wrong...!',
                'success' => false
            ];
        }
        return [
            'message' => 'Record Successfully.',
            'success' => true
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function show(Schedule $schedule)
    {
        //
        Schedule::$mentor_details = true;

        return $schedule;
        // $arr = [];
        // foreach ($schedule->mentors as  $key => $mentor) {
        //     // return
        //     $role = Role::where('id', $key)->pluck('name');
        //     $arr[$role[0]] =  User::whereIn('id', $mentor)->get();
        // }
        // return $arr;

        // $roles = Role::select('id', 'name')->where('type', 2)->get();
        // $schedules = Schedule::query()
        //     // ->where('id', 56)
        //     // ->where('slot_threshold->3->slot', '>', 10)
        //     // ->whereNotNull('slot_threshold->4')
        //     ->get();

        // return [
        //     'roles' => $roles,
        //     'schedules' => $schedules
        // ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(Schedule $schedule)
    {
        // return $schedule;
        // return $schedule->time_schedule['start_time'];
        $schedule->start_time =  date("H:i:s", strtotime($schedule->time_schedule['start_time']));
        $schedule->end_time = date("H:i:s", strtotime($schedule->time_schedule['end_time']));
        // $schedule->start_time =  $schedule->time_schedule['start_time'];
        // $schedule->end_time =  $schedule->time_schedule['end_time'];
        $schedule->makeHidden('time_schedule');

        // return
        // $schedule->start_time;

        // return $schedule;
        $chambers = Chamber::get();
        // $chambers = Chamber::get();
        // return $chambers;
        // $roles = Role::select('id', 'name')->where('type', 2)->get();
        // return
        $support_type = Role::select('id', 'name')->with('users')->where('type', 2)->get();
        return [
            'schedule' => $schedule,
            'chambers' => $chambers,
            'default_slot' => 10,
            'default_threshold' => 3,
            'support_type' => $support_type
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Schedule $schedule)
    {
        $slot_threshold_db = [];
        foreach ($request->slot_thresholds as $slot_threshold) {
            $slot_threshold_db[$slot_threshold['support_type_id']] = [
                'slot' => $slot_threshold['slot'],
                'threshold' => $slot_threshold['threshold']
            ];
        }

        $schedule->update([
            'time_schedule' => ['s' => $request->s_time, 'e' => $request->e_time],
            'chamber_id' => $request->chamber_id,
            'date' => $request->date,
            // 'slot_threshold' =>[$request->cc_slot,$request->p_slot]
            // 'slot_threshold' => $request->slot_threshold,
            'slot_threshold' => json_encode($slot_threshold_db),
            'mentors' => $request->mentors,
            'active' => $request->active,
            // 'mentors' => [
            //     '3' => [2, 6],
            //     '4' => [1, 5]
            // ],
        ]);


        if (!$schedule) {
            return [
                'message' => 'Something is wrong...!',
                'success' => false
            ];
        }
        return [
            'message' => 'Record updated Successfully.',
            'success' => true
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        //
    }

    private $support_type = Null;
    private $previous_counsellor_id = Null;

    private function possibility($percentage)
    {

        if ($percentage == 100) {
            $message = "You are appreciated to book this appointment, But getting your requested mentor is not certain. Thank you";
        } elseif ($percentage >= 70)
            $message = "Getting your requested mentor has a high possibility, Thank You";
        elseif ($percentage < 70 && $percentage >= 50)
            $message = "Getting your requested mentor has a slightly high possibility, Thank You";
        elseif ($percentage < 50 && $percentage >= 30)
            $message = "Getting your requested mentor has a slightly low possibility, Thank You";
        elseif ($percentage < 30)
            $message = "Getting your requested mentor has a low possibility, Thank You";
        return $message;
    }

    public function scheduleEach($schedule)
    {
        $data["id"] = (int) ($schedule->id);
        $data["chamber_id"] = (int) ($schedule->chamber_id);
        $data["date"] = (string) ($schedule->date);
        $data["time_schedule"] = (object) ($schedule->time_schedule);
        $appointment_count = count($schedule->appointments);
        // return $data;

        foreach ($schedule->slot_threshold as $key => $value) {
            if ($key ==  $this->support_type) {
                // $data["slot_remains"] = (int)(($value->slot - $appointment_count) );
                $data["slot_remains"] = (float)(($value->slot - $appointment_count) / $value->slot * 100);
            }
        }
        // $data["mentor_possibility"] = (object) ($this->appointments->groupBy('requested_mentor_id')->sort()->map(function ($q) use ($appointment_count){
        //     return ($q->count()/ $appointment_count *100);
        // }));
        // $data["previous_counsellor_id"] = self::$previous_counsellor_id;

        if ($this->previous_counsellor_id != Null) {
            $data["mentor_probability"] = round((count(($schedule->appointments->Where('requested_mentor_id',  $this->previous_counsellor_id)->sort())) + 1) / ($appointment_count + 1) * 100);
            $data["mentor_possibility"] = $this->possibility($data["mentor_probability"]);

            if ($data["mentor_probability"] == 100) {
                $data["mentor_probability"] = 5;
                $data["mentor_possibility"] = "You are appreciated to book this appointment, But getting your requested mentor is not certain. Thank you";
            }
        } else {
            $data["mentor_probability"] = 100;
            $data["mentor_possibility"] = "You are appreciated to book this appointment, Thank you";
        }


        return (object)$data;
    }



    private function chambersMap($chambers, $chambers_pred)
    {
        // $data = [];
        foreach ($chambers as $chember) {
            $chember->status = Null;
            foreach ($chambers_pred as $key => $pred) {
                if ($chember->id == $key) {
                    // return $pred;
                    $chember->status = $pred;
                }
            }
        }

        return $chambers;
    }
    private function datesMap($dates, $date_pred)
    {
        $data = [];
        $new_datas = [];
        foreach ($dates as $date) {
            $data['date'] = $date;
            $data['status'] = Null;
            foreach ($date_pred as $key => $pred) {
                if ($date == $key) {
                    $data['status'] = $pred;
                }
            }
            $new_datas[] = $data;
            // return $data;
        }

        return collect($new_datas);
    }
    public function search_schedule($support_type, Request $request)
    {
        // return $support_type;
        // return
        $user_id = $request->user()->id ?? NULL;
        $payable = 0;
        $missed_appointments_count = 0;
        $mentors = User::$payable_counselling_type; // vobisshyat e dekha hobe

        if ($user_id) {
            $last_appointments = Appointment::query()
                ->with('mentor')
                ->where('user_id', $user_id)
                ->whereIn('type', $mentors)
                ->oldest()
                ->get();

            foreach ($last_appointments as $appointment) {
                if ($appointment->mentor) {
                    $payable += 1;
                    $missed_appointments_count = 0;
                } else
                    $missed_appointments_count++;
            }
        }



        $int_time = NULL;
        $mentor = NULL;
        if ($request->time)
            $int_time = (int) Schedule::encodeTime($request->time);
        $date = $request->date;
        $chamber_id = $request->chamber_id;
        $this->support_type = $support_type;
        $this->previous_counsellor_id = $request->previous_counsellor_id;
        // ScheduleResource::$previous_counsellor_id = 5;
        if ($request->mentor_id && $request->mentor_type)
            $mentor = [
                'mentor_id' => $request->mentor_id,
                'mentor_type' => $request->mentor_type
            ];
        // return $request;

        $raw_schedules = Schedule::query()
            // ->where('date', '>=', Carbon::now())
            ->when($this->support_type, function ($query, $support_type) {
                return $query->whereNotNull('slot_threshold->' . $support_type)
                    ->with(['appointments' => function ($q) use ($support_type) {
                        $q->select('schedule_id', 'requested_mentor_id')
                            ->where('type', $support_type);
                        // ->coune();
                        // ->pluck('requested_mentor_id');
                    }]);
            })
            ->when($mentor, function ($query, $mentor) {
                return $query->whereJsoncontains('mentors->' . (int)$mentor['mentor_type'], (int)($mentor['mentor_id']));
            })
            ->orderBy('date')
            ->get(['id', 'chamber_id', 'date', 'time_schedule', 'slot_threshold']);


        $schedules = (object)$raw_schedules
            ->when($int_time, function ($query, $int_time) {
                return $query->where('time_schedule->s', '<', $int_time)
                    ->where('time_schedule->e', '>', $int_time);
            })
            ->when($date, function ($query, $date) {
                return $query->where('date', $date);
            })
            ->when($chamber_id, function ($query, $chamber_id) {
                return $query->where('chamber_id', $chamber_id);
            });

        // return
        $raw_schedules = $raw_schedules->map([$this, 'scheduleEach']);
        $schedules = $schedules->map([$this, 'scheduleEach']);

        if (!$chamber_id && $date)
            $date_groups = $raw_schedules->groupBy('date');
        else {
            $date_groups =  (object)$raw_schedules
                ->when($chamber_id, function ($query, $chamber_id) {
                    return $query->where('chamber_id', $chamber_id);
                })->groupBy('date');
        }

        // return
        if ($chamber_id && !$date)
            $chamber_groups = $raw_schedules->groupBy('chamber_id');
        else {
            $chamber_groups = (object)$raw_schedules
                ->when($date, function ($query, $date) {
                    return $query->where('date', $date);
                })->groupBy('chamber_id');
        }
        // return

        $data['uniqueDates'] = $date_groups->map(function ($date_groups) {
            return [
                'slot_remains' => round($date_groups->avg('slot_remains')),
                'mentor_probability' => round(($date_groups->where('mentor_probability', '!=', 100)->where('mentor_probability', '!=', 5)->max('mentor_probability')) ?? 5),
                'mentor_possibility' =>  $this->possibility(($date_groups->where('mentor_probability', '!=', 100)->where('mentor_probability', '!=', 5)->max('mentor_probability')) ?? 100),
            ];
        });

        // return
        $data['uniqueChambers'] = $chamber_groups->map(function ($chamber_groups) {
            return [
                'slot_remains' => round($chamber_groups->avg('slot_remains')),
                'mentor_probability' => round(($chamber_groups->where('mentor_probability', '!=', 100)->where('mentor_probability', '!=', 5)->max('mentor_probability')) ?? 5),
                'mentor_possibility' =>  $this->possibility(($chamber_groups->where('mentor_probability', '!=', 100)->where('mentor_probability', '!=', 5)->max('mentor_probability')) ?? 100),
            ];
        });
        $chambers = Chamber::get(['id', 'name', 'address']);
        $dates = Schedule::orderBy('date')->distinct('date')->pluck('date');

        // return round(($chamber_groups->where('mentor_probability', '!=', 100)->where('mentor_probability', '!=', 5)->max('mentor_probability')) ?? 5);

        $chembers_map = $this->chambersMap($chambers, $data['uniqueChambers']);
        $dates_map =  $this->datesMap($dates, $data['uniqueDates']);

        if (!$chamber_id && !$date) {
            $test = 1;
            // $slot_massage = 0;
            $message = "Please select any date and place of your choice";
        } elseif (!$chamber_id && $date) {
            $test = 2;
            // $slot_massage = $dates_map->where('date', $date)->first()['status']['slot_remains'];
            $message = $dates_map->where('date', $date)->first()['status']['mentor_possibility'];
        } elseif ($chamber_id && !$date) {
            $test = 3;
            // $slot_massage = $chembers_map->where('id', $chamber_id)->first()->status['slot_remains'];
            $message = $chembers_map->where('id', $chamber_id)->first()->status['mentor_possibility'];
        } elseif ($chamber_id && $date && !$request->schedule_id) {
            $test = 4;
            // $slot_massage = $schedules->sortByDesc('mentor_possibility')->first()->slot_remains;
            $message = $schedules->sortByDesc('mentor_possibility')->first()->mentor_possibility;
        } elseif ($request->schedule_id) {
            $test = 5;
            $slot_remains = (bool) $schedules->where('id', $request->schedule_id)->first()->slot_remains;
            $slot_massage = $schedules->where('id', $request->schedule_id)->first()->slot_remains != 0 ? '' : "Sorry...!!!\nThis slots for this schedule has has been filled completely.";
            $message = $schedules->where('id', $request->schedule_id)->first()->mentor_possibility;
        }


        return [
            // 'test' => $test,
            // 'mentor_probability' => $mentor_probability,
            'slot_remains' => (bool)($slot_remains ?? false),
            // 'slot_massage' => "Please select any date and place of your choice",
            'slot_massage' => $slot_massage ?? '',
            'mentor_possibility' => "Please select any date and place of your choice",
            // 'mentor_possibility' => $message,
            'payable' => (float)($payable ? 500.00 : 0.00),
            'chambers' => $chembers_map,
            'dates' => $dates_map,
            'time_condition' => (bool)((bool)$date && (bool)$chamber_id && (count($schedules) >= 2) ? true : false),
            'schedules' => ((bool)$date && (bool)$chamber_id ? $schedules : []),

        ];
    }

    public function prev_history(Request $request)
    {

        $role = Role::where('type', 2)->get(['id', 'name']);
        $mentors = [4]; // vobisshyat e dekha hobe
        // $user_id = $request->user_id;
        // return $request->user();
        // return
        //    [ Auth::guard('sanctum')->id() ];

        $user_id = Auth::guard('sanctum')->id();
        // $user_id = Auth::guard('sanctum')->id()  ;
        if ($user_id) {
            $last_appointment = Appointment::query()
                ->with('schedule:id,chamber_id,date,time_schedule', 'schedule.chamber', 'mentor', 'user_feedbacks.question')
                ->where('user_id', $user_id)
                ->whereIn('type', $mentors)
                ->latest()
                ->first();
        }
        return [
            'support_types' => $role,
            'last_appointment' => $last_appointment ?? null,
        ];
    }

    public function mentor_schedule(Request $request)
    {
        // $user_id = 2;
        $user_id = $request->user()->id ?? NULL;


        $chamber_id = $request->chamber_id;

        $mentor = User::where('id', $user_id)->with('roles:id,name')->first();

        User::where('id', 2)->first()->getRoleNames();

        $support_types = Role::query()
            // ->with('users')
            ->where('type', 2)
            // ->get();
            ->pluck('name', 'id');
        // return
        $chembers = Chamber::get(['id', 'name', 'address']);

        // return 313;
        $schedules = Schedule::query()
            ->with([
                'appointments' => function ($q) use ($mentor, $request) {
                    $q->when($request->support_type, function ($q) use ($request) {
                        $q->where('type', $request->support_type);
                    })
                        ->whereHas('assign_mentor', function ($q) use ($mentor) {
                            $q->where('mentor_id', $mentor->id);
                        });
                }
            ])
            ->whereHas(
                'appointments',
                function ($q) use ($mentor, $request) {
                    $q->when($request->support_type, function ($q) use ($request) {
                        $q->where('type', $request->support_type);
                    })
                        ->whereHas('assign_mentor', function ($q) use ($mentor) {
                            $q->where('mentor_id', $mentor->id);
                        });
                }
            )
            ->when($chamber_id, function ($query, $chamber_id) {
                return $query->where('chamber_id', $chamber_id);
            });


        // return $schedules->get();

        // if ($request->counselling_type) {
        //     $schedules = $schedules->WhereJsoncontains('mentors->' . (int)$request->counselling_type, (int)($mentor->id));
        // } else {
        //     $schedules->where(function ($query) use ($mentor) {
        //         foreach ($mentor->roles as $role) {
        //             $query->orWhereJsoncontains('mentors->' . (int)$role->id, (int)($mentor->id));
        //         }
        //         return $query;
        //     });
        // }

        // if ($request->counselling_type) {
        //     $schedules = $schedules->WhereJsoncontains('mentors->' . (int)$request->counselling_type, (int)($mentor->id));
        // } else {
        //     $schedules->where(function ($query) use ($mentor) {
        //         foreach ($mentor->roles as $role) {
        //             $query->orWhereJsoncontains('mentors->' . (int)$role->id, (int)($mentor->id));
        //         }
        //         return $query;
        //     });
        // }
        // return
        $schedules = $schedules->orderBy('date', 'desc')
            ->take(20)
            ->get();
        // ->get(['id', 'chamber_id', 'date', 'time_schedule', 'slot_threshold',]);

        mentorScheduleResource::$chembers = $chembers;
        mentorScheduleResource::$mentor_id = $user_id;


        return [
            'support_types' => $support_types,
            'chembers' => $chembers,
            'schedules' => mentorScheduleResource::collection($schedules),
            'query_log' => DB::getQueryLog()
        ];
    }
}
