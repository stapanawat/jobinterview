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
        'current_residence',
        'current_occupation',
        'age',
        'education_level',
        'number_of_children',
        'can_drive_motorcycle',
        'pros_and_cons',
        'life_dream',
        'emergency_contact',
        'preferred_working_hours',
        'job_description',
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
