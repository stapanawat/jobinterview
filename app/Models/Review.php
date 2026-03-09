<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'applicant_id',
        'reviewer_type',
        'rating',
        'comment',
        'rating_punctuality',
        'rating_showed_up',
        'rating_honesty',
        'rating_diligence',
        'rating_following_instructions',
        'rating_others',
        'rating_punctuality',
        'rating_showed_up',
        'rating_honesty',
        'rating_diligence',
        'rating_following_instructions',
        'rating_others',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
