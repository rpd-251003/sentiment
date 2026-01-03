<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'nim',
        'dosen_id',
        'pembimbing_lapangan_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function pembimbingLapangan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing_lapangan_id');
    }

    public function internship(): HasOne
    {
        return $this->hasOne(StudentInternship::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(KpEvaluation::class);
    }
}
