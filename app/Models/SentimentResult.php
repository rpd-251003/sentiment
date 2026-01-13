<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SentimentResult extends Model
{
    protected $fillable = [
        'kp_evaluation_id',
        'comment_type',
        'sentiment_label',
        'sentiment_score',
        'positive_score',
        'negative_score',
        'neutral_score',
    ];

    protected $casts = [
        'sentiment_score' => 'decimal:4',
        'positive_score' => 'decimal:4',
        'negative_score' => 'decimal:4',
        'neutral_score' => 'decimal:4',
    ];

    public function kpEvaluation(): BelongsTo
    {
        return $this->belongsTo(KpEvaluation::class);
    }
}
