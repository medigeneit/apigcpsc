<?php

namespace App\Models;

use App\Http\Resources\AuthUserResource;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\SpatieRole;
use Spatie\Permission\Models\Permission;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    static $payable_counselling_type = [4];

    protected $guarded = [];

    // protected $fillable = [
    //     'name',
    //     'email',
    //     'phone',
    //     'password',
    //     'hash_password',
    // ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'hash_password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function setAndGetLoginResponse($token = null, $additional = [])
    {
        AuthUserResource::withoutWrapping();

        // $branches = Branch::query()->whereNotNull('domain')->pluck('domain' );

        if ($token === null) {
            $token = $this->loginAndGetToken();
        }

        $roles = $this->roles()->get();
        $role_names = $this->roles()->pluck('name')->toArray();
        // return
        $roel_types = $roles->pluck('type')->toArray();

        foreach (Role::$TYPES as $key =>$type){
            $access['is'.$type] = in_array($key, $roel_types) ? 1 : 0;
        }


        if(in_array('Owner', $role_names) ||  in_array('Super Admin',$role_names)){
            $permissions = ['*'];
        }else{
            $permissions = $this->getAllPermissions()->pluck('name');
        }
        return [
            'user'  => (new AuthUserResource($this)),
            'token' => $token,
            'tokenHash' => base64_encode($token),
            'access' => $access ?? [],
            'abilities' => $permissions,
            // 'branchDomains' => $branches,
        ] + $additional;
    }

    public function loginAndGetToken()
    {
        return $this->createToken(request()->ip())->plainTextToken;
    }
}
