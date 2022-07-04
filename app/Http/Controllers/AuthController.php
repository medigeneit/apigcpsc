<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

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

        $code = rand(1111, 9999);

        if (!$user) {

            $code = rand(1111, 9999);

            // $message = "Your GCPSC verification pin {$code}";

            // $this->sendSMS($fields['phone'], $message);

            Otp::updateOrCreate(
                ['phone' => $fields['phone']],
                ['code'  => $code,]
            );
        }

        return response([
            'phone'     => $fields['phone'],
            'name'      => $user->name ?? '',
            'has_user'  => (bool) $user,
            // 'code'      => $code ?? 0,
        ], 200);
    }

    public function confirm(Request $request)
    {
        $fields = $request->validate([
            'phone'     => 'required|string',
            'code'      => 'required|size:4|regex:/^[0-9]+$/',
        ]);
        // return
        $otp = $this->getOtp($fields['code'], $fields['phone']);

        if (!$otp) {
            return response([
                'message'       => 'Otp expired or doesn\'t match',
                'phone'         => $fields['phone'],
                'otp_confirm'   => false,
                'user_genesis'   => $user_genesis ?? null
            ], 404);
        }


        // return
        $response = Http::get('https://api.genesisedu.info/general/find-doc', [
            // $response = Http::get('http://192.168.88.189:7000/general/find-doc', [
            'phone' => $request->phone,
            // 'demand ' => ['name','mobile_number',' ','main_password','gender','bmdc_no']
        ]);
        $user_genesis =  $response->object()->data ?? NULL;

        // return
        // $user_genesis->makeHidden(['photo',])




        return response([
            'message'       => 'Otp matched!',
            'phone'         => $fields['phone'],
            'otp_confirm'   => true,
            'user_genesis'   => $user_genesis ?? null
        ], 200);
    }

    public function register(Request $request)
    {



        // return 654654;


        // return  bcrypt($request->password);
        // return
        $fields = $request->validate([
            'name'      => 'required|string',
            // 'phone'     => 'required|string',
            'phone'     => 'required|string|unique:users,phone',
            'email'     => 'nullable|email|unique:users,email',
            'password'  => 'required|string|min:3',
            'gender'  => '',
            'bmdc'  => '',
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
            'bmdc'     => $fields['bmdc'],
            'email'     => $fields['email'] ?? null,
            'gender'  => $fields['gender'] ?? null,
            'password'  => $fields['password'],
            'hash_password'  => bcrypt($fields['password']),
        ]);

        //Otp::find($fields['phone'])->delete();

        // $token = $user->createToken(Request()->ip())->plainTextToken;


        $token = $user->createToken(Request()->ip())->plainTextToken;
        return response($user->setAndGetLoginResponse() +
            [
                'success'   => true,
                'message'   => 'Registration successfull!',
            ], 201);


        // if ($user) {
        //     $user->success = true;
        //     $user->message = 'Registration successfull!';
        // } else {
        //     $user->success = false;
        //     $user->message = 'Registration unsuccessfull!';
        // }

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

        return response($user->setAndGetLoginResponse(), 201);
    }



    // private function loginResource(User $user, $token): array
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
