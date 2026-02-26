<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $fillable = [
        'line_user_id',
        'name',
        'phone',
        'address',
        'position',
        'experience',
        'id_card_image',
        'photo',
        'line_display_name',
        'line_picture_url',
        'status',
        'pdpa_accepted',
    ];

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
