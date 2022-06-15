<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {

        if (env('APP_DEBUG', false)) {
            DB::enableQueryLog();
        }
    }


    protected function getOtp($code, $phone, $expire_time = 120)
    {
        // When the code expires, will be set null
        // return
        Otp::where('phone', $phone)
            ->whereRaw('( NOW( ) - updated_at  > ' . $expire_time . ')')
            ->update([
                'code' => null
            ]);

        return Otp::select('code')
            ->where('code', $code)
            ->find($phone);
    }
}
