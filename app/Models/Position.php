<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'shop_name',
        'location',
        'description',
        'duties',
        'salary',
        'extra_pay',
        'working_hours',
        'days_off',
        'benefits',
        'qualifications',
    ];
}
