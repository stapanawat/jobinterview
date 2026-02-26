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
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
