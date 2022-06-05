<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //

    public function join(Request $request)
    {

        $fields = $request->validate([
            'phone'     => 'required|string'
        ]);

        // Check phone
        $user = User::where('phone', $fields['phone'])->first();

        if (!$user) {
            $code = rand(1111, 9999);

            Otp::updateOrCreate(
                ['phone' => $fields['phone']],
                ['code'  => $code,]
            );

            $sms = new Sms();

            $text = "Your OTP code : {$code}";

            $sms->setText($text)->setRecipient($fields['phone'])->send();
        }

        return response([
            'phone'     => $fields['phone'],
            'has_user'  => (bool) $user,
        ], 200);
    }

    public function confirm(Request $request)
    {
        $fields = $request->validate([
            'phone'     => 'required|string',
            'code'      => 'required|size:4|regex:/^[0-9]+$/',
        ]);

        $otp = $this->getOtp($fields['code'], $fields['phone']);

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
        // return  bcrypt($request->password);
        $fields = $request->validate([
            'name'      => 'required|string',
            // 'phone'     => 'required|string',
            'phone'     => 'required|string|unique:users,phone',
            'password'  => 'required|string|min:3',
            // 'code'      => 'required|size:4|regex:/^[0-9]+$/',
        ]);

        // return $fields;

        // $otp = $this->getOtp($fields['code'], $fields['phone'], 30 * 60);

        // if (!$otp) {
        //     return response([
        //         'user'      => null,
        //         'token'     => '',
        //         'success'   => false,
        //         'message'   => 'Session expired!',
        //     ], 404);
        // }

        $user = User::create([
            'name'      => $fields['name'],
            'phone'     => $fields['phone'],
            'password'  => $fields['password'],
            'hash_password'  => bcrypt($fields['password']),
        ]);

        //Otp::find($fields['phone'])->delete();

        // $token = $user->createToken(Request()->ip())->plainTextToken;
        if ($user) {
            $user->success = true;
            $user->message = 'Registration successfull!';
        } else {
            $user->success = false;
            $user->message = 'Registration unsuccessfull!';
        }

        return response($user, 201);
    }

    public function login(Request $request)
    {
        // return
        $fields = $request->validate([
            'phone'     => 'required|string',
            'password'  => 'required|string|min:3',
        ]);

        // Check phone
        $user = User::where('phone', $fields['phone'])->first();

        // Check password
        // return Hash::check($fields['password'], $user->hash_password);

        if (!$user || !Hash::check($fields['password'], $user->hash_password)) {
            return response([
                'message' => 'Phone or Password wrong!',
            ], 401);
        }

        return response($user, 201);
    }



    private function loginResource(User $user, $token): array
    {
        AuthUserResource::withoutWrapping();

        return [
            'user'  => new AuthUserResource($user),
            'token' => $token,
            'tokenHash' => base64_encode($token),
        ];
    }

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
