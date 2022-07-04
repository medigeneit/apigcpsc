<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegallyBindingContract extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'legally_binding_contracts';
    protected $guarded = [];

    public function scopeProperty($query, $value)
    {
        return $query->where('key', $value);
    }
}
