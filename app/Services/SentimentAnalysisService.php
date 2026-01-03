<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SentimentAnalysisService
{
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.sentiment.url');
    }

    /**
     * Analyze sentiment of the given text
     *
     * @param string $text
     * @return array{sentiment_label: string, sentiment_score: float}
     * @throws \Exception
     */
    public function analyze(string $text): array
    {
        try {
            $response = Http::timeout(30)
                ->post($this->apiUrl, [
                    'text' => $text,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'sentiment_label' => $data['sentiment_label'] ?? 'neutral',
                    'sentiment_score' => (float) ($data['sentiment_score'] ?? 0.0),
                ];
            }

            Log::error('Sentiment API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Failed to analyze sentiment: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Sentiment analysis failed', [
                'error' => $e->getMessage(),
                'text' => $text,
            ]);

            throw $e;
        }
    }
}
