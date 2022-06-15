<?php

namespace App\Http\Controllers\Doctors\Auth;

use App\Helpers\Sms\Sms;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthUserResource;
use App\Models\Doctor;
use App\Models\Otp;
use App\Models\Student;
use App\Models\User;
use App\Providers\BranchServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    // public function join(Request $request)
    // {

    //     $fields = $request->validate([
    //         'phone'     => 'required|string'
    //     ]);

    //     // Check phone
    //     $user = Student::where('phone', $fields['phone'])->first();

    //     if (!$user) {
    //         $code = rand(1111, 9999);

    //         Otp::updateOrCreate(
    //             ['phone' => $fields['phone']],
    //             ['code'  => $code,]
    //         );

    //         $sms = new Sms();

    //         $text = "Your OTP code : {$code}";

    //         $sms->setText($text)->setRecipient($fields['phone'])->send();
    //     }

    //     return response([
    //         'phone'     => $fields['phone'],
    //         'has_user'  => (bool) $user,
    //     ], 200);
    // }

    public function confirm(Request $request)
    {
        $fields = $request->validate([
            'phone'     => 'required|string',
            'code'      => 'required|size:4|regex:/^[0-9]+$/',
        ]);

        // return
        $otp = $this->getOtp($fields['code'], $fields['phone'], 5 * 60);

        if (!$otp) {
            return response([
                'message'       => 'Otp expired or doesn\'t match',
                'phone'         => $fields['phone'],
                'otp_confirm'   => false,
            ], 404);
        }

        return response([
            'message'       => 'Otp matched!',
            'phone'         => $fields['phone'],
            'otp_confirm'   => true,
        ], 200);
    }

    public function register(Request $request)
    {


        $fields = $request->validate([
            'name'      => 'required|string',
            'email'    => 'required|email|unique:doctors,email',
            'phone'     => 'required|string|unique:doctors,phone',
            'password'  => 'required|string|min:3',
            // 'code'      => 'required|size:4|regex:/^[0-9]+$/',
        ]);

        // $otp = $this->getOtp($fields['code'], $fields['phone'], 30 * 60);

        // if (!$otp) {
            //     return response([
                //         'user'      => null,
                //         'token'     => '',
                //         'success'   => false,
                //         'message'   => 'Session expired!',
                //     ], 404);
                // }

                // $branch_id = BranchServiceProvider::id( );

                $user = User::create([
                    'name'      => $fields['name'],
                    'email'     => $fields['email'],
                    'phone'    => $fields['phone'],
                    'password'  => bcrypt($fields['password']),
                    'security'  => $fields['password'],
                    // 'branch_id' => $branch_id,
                ]);
                // return $user;

        //Otp::find($fields['phone'])->delete();

        $token = $user->createToken(Request()->ip())->plainTextToken;
        return response($user->setAndGetLoginResponse() +
            [
                'success'   => true,
                'message'   => 'Registration successfull!',
            ], 201);

        //return $user->update(['password' => bcrypt('247507' )]);
    }

    public function login(Request $request)
    {
        // return $request;
        $fields = $request->validate([
            'phone'     => 'required|string',
            'password'  => 'required|string|min:3',
        ]);



        // Check phone
        return
        $user = Doctor::with('doc_speciality_subjects')->where('phone', $fields['phone'])->first();

        //        return $request;

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Phone or password doesn\'t match!',
            ], 401);
        }

        return response($user->setAndGetLoginResponse(), 201);
    }



    // private function loginResource(Student $user, $token): array
    // {
    //     AuthUserResource::withoutWrapping();

    //     return [
    //         'user'  => new AuthUserResource($user),
    //         'token' => $token,
    //         'tokenHash' => base64_encode($token),
    //     ];
    // }

    public function user()
    {
        $user = request()->user();

        return  $this->userResorce($user);
    }

    public function logout()
    {
        request()->user()->currentAccessToken()->delete();

        return response([
            'message'   => 'Logged Out'
        ], 200);
    }

    public function userResorce($user)
    {
        return [
            "id"    => $user->id,
            "name"  => $user->name,
            "phone" => $user->phone,
        ];
    }
}
