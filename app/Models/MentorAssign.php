<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use PhpParser\Node\Expr\FuncCall;
use Spatie\Permission\Models\Role;

class MentorAssign extends Model
{
    use HasFactory,SoftDeletes;

    // public $timestamps  = false;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'mentor_id', 'id');
    }
    public function type()
    {
        return $this->belongsTo(Role::class, 'support_types', 'id');
    }
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
