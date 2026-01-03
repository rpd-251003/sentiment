<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
    ];

    public function internships(): HasMany
    {
        return $this->hasMany(StudentInternship::class);
    }

    public function pembimbingLapangans(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'pembimbing_lapangan');
    }
}
