<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imagemap extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'id_group',
        'coordinate',
        'name',
        'description',
        'device_type',
        'status',
        'meta',
        'shape',
        'id_asset',
    ];
}
