<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Administration extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'administration';



    const type = [
        1 => 'Admin',
        2 => 'career counselor',
        3 => 'psychologist',
    ];

    

    public function mentors(Type $var = null)
    {
        return $this->hasMany(User::class,'id','user_id');

    }
}
