<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentInternship extends Model
{
    protected $fillable = [
        'student_id',
        'company_id',
        'pembimbing_lapangan_id',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function pembimbingLapangan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing_lapangan_id');
    }
}
