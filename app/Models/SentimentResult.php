<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SentimentResult extends Model
{
    protected $fillable = [
        'kp_evaluation_id',
        'sentiment_label',
        'sentiment_score',
    ];

    protected $casts = [
        'sentiment_score' => 'decimal:4',
    ];

    public function kpEvaluation(): BelongsTo
    {
        return $this->belongsTo(KpEvaluation::class);
    }
}
