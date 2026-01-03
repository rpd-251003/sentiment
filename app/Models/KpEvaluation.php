<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KpEvaluation extends Model
{
    protected $fillable = [
        'student_id',
        'evaluator_id',
        'evaluator_role',
        'rating',
        'comment_text',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function sentimentResult(): HasOne
    {
        return $this->hasOne(SentimentResult::class);
    }
}
