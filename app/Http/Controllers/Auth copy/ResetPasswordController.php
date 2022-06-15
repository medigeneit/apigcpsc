<?php

namespace App\Http\Controllers\Doctors\Auth;

use App\Helpers\Sms\Sms;
use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Otp;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{

    public function checkUser(Request $request)
    {

        // return $request;
        $fields = $request->validate([
            'phone'     => 'required|string'
        ]);

        // Check phone
        $user = Doctor::where('phone', $fields['phone'])->first();

        if ($user) {
            $code = rand(1111, 9999);

            Otp::updateOrCreate(
                ['phone' => $fields['phone']],
                ['code'  => $code,]
            );


            $text = "Your OTP code : {$code}";

            // Sms::setText($text)->setRecipient($fields['phone'])->send();
        }

        return response([
            'phone'     => $fields['phone'],
            'has_user'  => (bool) $user,
            'code'  =>  $code,
        ], 200);
    }


    private function updateUserAndReturnResponse(Doctor $user, $data, $deleteToken = false)
    {
        if ($user->update($data)) {

            if ($deleteToken) {
                $user->tokens()->delete();
            }

            return $user->setAndGetLoginResponse(null, [
                'success'   => true,
                'message'   => 'Password Changed successfully!',
            ]);
        }

        return response([
            'success'   => false,
            'message'   => 'Nothing updated!',
        ], 204);
    }

    public function setNewPassword(Request $request)
    {

// return
        $fields = $request->validate([
            'phone'     => 'required|string',
            'password'  => 'required|string|min:3',
            'code'      => 'boolean',
        ]);

        //return $fields;

        // $otp = $this->getOtp($fields['code'], $fields['phone'], 5 * 60);

        // if (!$otp) {
        //     return response([
        //         'success'   => false,
        //         'message'   => 'Session expired!',
        //     ], 404);
        // }

        if ($fields['code']) {
            // return
            $user = Doctor::where('phone', $fields['phone'])->first();

            if (!$user instanceof Doctor) {
                return response([
                    'success'   => false,
                    'message'   => 'User not found!',
                ], 400);
            }


            return $this->updateUserAndReturnResponse($user,  [
                'password'  => bcrypt($fields['password']),
                'security'  => $fields['password'],
            ], true);
        }
    }



    public function changePassword(Request $request)
    {

        $student = request()->user();

        $fields = $request->validate([
            'old_password'  => 'required|string|min:3|current_password:sanctum',
            'new_password'  => 'required|string|min:3',
        ]);

        if ($student->passwordIsValid($fields['new_password'])) {
            return response([
                'success'   => false,
                'message'   => 'You have provided your old password!',
            ], 400);
        }

        return $this->updateUserAndReturnResponse($student,  [
            'password'  => bcrypt($fields['new_password']),
            'security'  => $fields['new_password'],
        ], true);
    }
}
