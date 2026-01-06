<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SentimentAnalysisService
{
    protected string $apiUrl;
    protected string $hfToken;

    public function __construct()
    {
        $this->apiUrl  = "https://router.huggingface.co/hf-inference/models/w11wo/indonesian-roberta-base-sentiment-classifier";
        $this->hfToken = "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";
    }

    /**
     * Analyze sentiment using Hugging Face Inference API
     *
     * @param string $text
     * @return array{
     *   sentiment_label: string,
     *   sentiment_score: int,
     *   scores: array{positive: float, neutral: float, negative: float}
     * }
     * @throws \Exception
     */
    public function analyze(string $text): array
    {
        try {
            Log::info('Sentiment analysis started', [
                'input_preview' => Str::limit($text, 120),
                'input_length'  => strlen($text),
            ]);

            $response = Http::timeout(30)
                ->withToken($this->hfToken)
                ->post($this->apiUrl, [
                    'inputs' => $text,
                ]);

            if (!$response->successful()) {
                Log::error('HF Sentiment API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                throw new \Exception('Failed to call Hugging Face API');
            }

            $result = $response->json();

            if (!isset($result[0]) || !is_array($result[0])) {
                throw new \Exception('Invalid Hugging Face response format');
            }

            // Extract scores
            $scores = [
                'positive' => 0,
                'neutral'  => 0,
                'negative' => 0,
            ];

            foreach ($result[0] as $item) {
                if (isset($scores[$item['label']])) {
                    $scores[$item['label']] = (float) $item['score'];
                }
            }

            $positive = $scores['positive'];
            $neutral  = $scores['neutral'];
            $negative = $scores['negative'];

            $label = array_keys($scores, max($scores))[0];

            // Weighted rating (1–10)
            $weightedScore = ($positive * 10) + ($neutral * 5.5) + ($negative * 1);
            $rating = max(1, min(10, (int) round($weightedScore)));

            Log::info('Sentiment analysis result', [
                'label'   => $label,
                'rating'  => $rating,
                'scores'  => [
                    'positive' => round($positive, 2),
                    'neutral'  => round($neutral, 2),
                    'negative' => round($negative, 2),
                ],
            ]);

            return [
                'sentiment_label' => $label,
                'sentiment_score' => $rating,
                'scores' => [
                    'positive' => round($positive, 2),
                    'neutral'  => round($neutral, 2),
                    'negative' => round($negative, 2),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Sentiment analysis failed', [
                'message' => $e->getMessage(),
                'input_preview' => Str::limit($text, 120),
            ]);

            throw $e;
        }
    }
}
