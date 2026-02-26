<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $fillable = [
        'applicant_id',
        'interview_date',
        'interview_time',
        'location',
        'status',
        'reminder_sent',
        'day_before_confirmed',
        'day_before_reminder_sent',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
