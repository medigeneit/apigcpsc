<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;

class Role extends SpatieRole
{
    use HasFactory;

    public $timestamps = false;
    // public $timestamps = false
    public function __construct()
    {
        parent::__construct(['guard_name' => 'web']);
    }


    protected $appends = ['type_name'];

    static $TYPES = [
        1 => "Admin",
        2 => "Mentor"
    ];

    public function getTypeNameAttribute()
    {
        if ($this->type)
            return self::$TYPES[$this->type];
    }
}
