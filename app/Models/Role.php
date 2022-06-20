<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    protected $appends = ['type_name'];

    static $TYPES = [
        1 => "Admin",
        2 => "Mentor"
    ];

    public function getTypeNameAttribute()
    {
        if($this->type)
        return self::$TYPES[$this->type];
    }
}
