<?php

namespace App\Http\Controllers;

use App\Http\Resources\LastAppointmentResource;
use App\Http\Resources\UserResource;
use App\Models\Appointment;
use App\Models\MentorAssign;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\User;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\Extension\Mention\Mention;
use Spatie\Permission\Models\Permission;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $mentor = false)
    {
        // return Schedule::with('appointments.assign_mentor.user:id,name')->get();

        // return
        $user = $request->user()->id;


        $support_type = $request->type;

        if ($request->schedule_id) {

            $schedule = Schedule::query();

            // if ($support_type) {
            $schedule = $schedule->with([
                'appointments' => function ($q) use ($support_type, $mentor, $user) {
                    $q->when($support_type, function ($q) use ($support_type) {
                        $q->where('type', $support_type);
                    })->when($mentor == true, function ($q) use ($user) {
                        $q->whereHas('assign_mentor', function ($q) use ($user) {
                            $q->where('mentor_id', $user);
                        });
                    });
                    // ->coune();
                    // ->pluck('requested_mentor_id');
                },
                'appointments.patient',
                'appointments.requested_mentor',
                'appointments.assign_mentor.user:id,name'
            ]);
            // }
            // else {
            //     $schedule = $schedule->with(['appointments.patient', 'appointments.requested_mentor', 'appointments.assign_mentor.user:id,name']);
            // }
            // return
            $schedule = $schedule->where('id', $request->schedule_id)
                ->first();


            // return $schedule->appointments->whereNotNull('requested_mentor_id');

            $grouped = $schedule->appointments->whereNotNull('requested_mentor_id')->groupBy('requested_mentor_id')->map(function ($row) {
                if (!isset($row[0]->requested_mentor)) {
                    return [
                        "id" => NULL,
                        "name"  =>  "",
                        "phone"  =>  "",
                        "email"  =>  null,
                        "email_verified_at"  =>  null,
                        "gender"  =>  null,
                        "bmdc"  =>  null,
                        "medical"  =>  null,
                        "session"  =>  null,
                        "created_at"  =>  null,
                        "updated_at"  =>  null,
                        "frequency"  =>  0
                    ];
                }

                $mentor = $row[0]->requested_mentor;
                $mentor->frequency = $row->count();

                return $mentor;
            });

            $expected_mentors_frequescy = $grouped->sortByDesc('frequency')->sortBy('name')->whereNotNull('id')->values();

            // return
            // return
            $roles = Role::with('users')->where('type', 2)->get();
            // $roles = Role::with('users')->where('type', 2)->get();
            // return $roles->pluck('users','id');
            // return $roles->pluck('name', 'id');

            // foreach ($mentor_type as $type{

            //     $mentor_list[$type] =>

            // })


            LastAppointmentResource::$types =  Role::where('type', 2)->pluck('name', 'id');

            return [
                'type' => $roles->pluck('name', 'id'),
                'expected_mentors_frequescy' => $expected_mentors_frequescy,
                'data' => LastAppointmentResource::collection($schedule->appointments),
                // 'data2' => $schedule->appointments,
                'mentors' =>   $roles->pluck('users', 'id')
            ];
        }
    }

    public function mentor_appointment(Request $request)
    {
        $index = $this->index($request, true);
        $data['type'] = $index['type'];
        $data['appointments'] = $index['data'];
        return  $data;
    }

    public function my_profile()
    {
        // return request()->user()->id;
        //
        // $role = Role::where('type', 2)->get(['id', 'name']);
        // $mentors = [4]; // vobisshyat e dekha hobe
        // return
        $user_id = request()->user()->id;
        if ($user_id) {
            $last_appointment = Appointment::query()
                ->with(
                    'schedule:id,chamber_id,date,time_schedule',
                    'schedule.chamber',
                    'mentor',
                    'user_feedbacks.question'
                )
                ->where('user_id', $user_id)
                // ->whereIn('type', $mentors)
                ->latest()
                // ->hidden('created_at')
                ->paginate();
        }
        return [
            // 'role' => $role,
            'types' => Role::where('type', 2)->pluck('name', 'id'),
            'user' => request()->user(),
            'last_appointment' => $last_appointment,
        ];


        // $user_id = 2;
        // if ($user_id) {
        //     return
        //         Appointment::query()
        //         ->where('user_id', $user_id)
        //         ->get();
        // }
    }

    public function mentor_assign(Request $request)
    {

        return
            MentorAssign::withTrashed()->updateOrCreate([
                'appointment_id' => $request->appointment_id,
            ], [
                'mentor_id' => $request->mentor_id,
                'user_id' => $userId = Auth::id(),
                'deleted_at' => NULL,
            ]);
    }

    public function mentor_assign_edit(Request $request, $id)
    {

        // return $id;

        if ($request->mentor_id == null) {
            // return 6544;
            // return
            $mentor_delete = MentorAssign::where('id', $id)->delete();
            if ($mentor_delete)
                return ['message' => 'Record deleted'];
            else
                return ['message' => 'No mentor assigneed'];
        }

        // return
        // $mentor_assign = MentorAssign::find($id)->withTrashed();


        MentorAssign::withTrashed()->updateOrCreate([
            'appointment_id' => $request->appointment_id,
        ], [
            'mentor_id' => $request->mentor_id,
            'user_id' => $userId = Auth::id(),
            'deleted_at' => NULL,
        ]);
        return ['message' => 'Record updated'];
        return
            $mentor_assign = MentorAssign::find($id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $roles = Role::with('users')->where('type', 2)->get();

        return [
            'support_types' => Role::select('id', 'name')->where('type', 2)->get(),
            'mentor' => $roles->pluck('users', 'id')
        ];
        // $support_type = Role::select('id', 'name')->with('users')->where('type', 2)->get();

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $missed_appointments_count = 0;

        if (!$request->user_id) {
            $user = User::create([
                'phone' => $request->phone,
                'name' => $request->name,
            ]);
            if ($user) {
                $user_id = $user->id;
            } else {
                return [
                    'success' => false,
                    'message' => "Sorry...!!!\nUser creation failed.",
                    'appointment' => null
                ];
            }
        } else {
            $user_id = $request->user_id;
        }

        $serial = count(Appointment::where('schedule_id', $request->schedule_id)->get()) + 1;
        $mentors = User::$payable_counselling_type; // vobisshyat e dekha hobe
        // return
        $last_appointments = Appointment::query()
            ->with('mentor')
            ->where('user_id', $user_id)
            ->whereIn('type', $mentors)
            ->oldest()
            ->get();

        foreach ($last_appointments as $appointment) {
            if ($appointment->mentor) {
                $missed_appointments_count = 0;
            } else
                $missed_appointments_count++;
        }


        if ($missed_appointments_count > 3) {
            return [
                'success' => false,
                'message' => "Dear doctor,\nYou have missed too many appointmemnts.\nYou cannot book any more.\nPlese contuct with the hotline",
                'appointment' => null
            ];
        }

        // return
        $appointment = Appointment::create([
            'user_id' => $user_id,
            'schedule_id' => $request->schedule_id,
            'serial' => $serial,
            'type' => $request->type,
            // 'questions' => $request->questions,
            'payable' => $request->payable,
            'requested_mentor_id' => $request->requested_mentor_id  ?? null,
            'questions' => $request->questions  ?? [],
        ]);

        if ($appointment) {
            return [
                'success' => true,
                'message' => "Congratulations...!!!\nThe booking is completed successfully",
                'appointment' => $appointment
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show(Appointment $appointment)
    {
        $appointment->load('schedule', 'assign_mentor.user:id,name,phone,email');
        LastAppointmentResource::$types =  Role::where('type', 2)->pluck('name', 'id');
        $appointment_data['schedule'] = $appointment->schedule;
        $appointment_data['appointment'] = new LastAppointmentResource($appointment);
        //  return
        return $appointment_data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function edit(Appointment $appointment)
    {
        // return $appointment->patient;
        $roles = Role::with('users')->where('type', 2)->get();


        return [
            'patient' => new UserResource($appointment->patient),
            'appointment' => $appointment,
            'support_types' => Role::select('id', 'name')->where('type', 2)->get(),
            'mentor' => $roles->pluck('users', 'id')
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Appointment $appointment)
    {
        $missed_appointments_count = 0;
        // $user_id = $request->user_id;

        $serial = count(Appointment::where('schedule_id', $request->schedule_id)->get()) + 1;
        $mentors = User::$payable_counselling_type; // vobisshyat e dekha hobe
        // return
        $last_appointments = Appointment::query()
            ->with('mentor')
            ->where('user_id', $request->user_id)
            ->whereIn('type', $mentors)
            ->oldest()
            ->get();

        foreach ($last_appointments as $appointment) {
            if ($appointment->mentor) {
                $missed_appointments_count = 0;
            } else
                $missed_appointments_count++;
        }


        if ($missed_appointments_count > 3) {
            return [
                'success' => false,
                'message' => "Dear doctor,\nYou have missed too many appointmemnts.\nYou cannot book any more.\nPlese contuct with the hotline",
                'appointment' => null
            ];
        }

        // return
        $appointment->update([
            'user_id' => $request->user_id,
            'schedule_id' => $request->schedule_id,
            'serial' => $serial,
            'type' => $request->type,
            'questions' => $request->questions,
            'payable' => $request->payable,
            'requested_mentor_id' => $request->requested_mentor_id  ?? null,
            // 'questions' => json_encode($request->questions  ?? []),
        ]);

        if ($appointment) {
            return [
                'success' => true,
                'message' => "Congratulations...!!!\nThe booking is completed successfully",
                'appointment' => $appointment
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        //
    }
}
