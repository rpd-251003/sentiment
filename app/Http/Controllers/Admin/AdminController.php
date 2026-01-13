<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KpEvaluation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_evaluations' => KpEvaluation::count(),
            'total_dosen' => User::where('role', 'dosen')->count(),
            'total_pembimbing' => User::where('role', 'pembimbing_lapangan')->count(),
        ];

        $recentEvaluations = KpEvaluation::with(['student', 'evaluator', 'sentimentResult'])
            ->latest()
            ->take(10)
            ->get();

        // Sentiment distribution for pie chart (all sentiment results)
        $sentimentDistribution = DB::table('sentiment_results')
            ->select('sentiment_label', DB::raw('count(*) as count'))
            ->groupBy('sentiment_label')
            ->get();

        // Total counts by sentiment label (berdasarkan label dominan)
        $totalPositive = DB::table('sentiment_results')->where('sentiment_label', 'positive')->count();
        $totalNeutral = DB::table('sentiment_results')->where('sentiment_label', 'neutral')->count();
        $totalNegative = DB::table('sentiment_results')->where('sentiment_label', 'negative')->count();
        $totalSentiments = $totalPositive + $totalNeutral + $totalNegative;

        // Total SCORES (sum of all individual scores from all sentiment results)
        $totalPositiveScore = DB::table('sentiment_results')->sum('positive_score');
        $totalNeutralScore = DB::table('sentiment_results')->sum('neutral_score');
        $totalNegativeScore = DB::table('sentiment_results')->sum('negative_score');
        $totalAllScores = $totalPositiveScore + $totalNeutralScore + $totalNegativeScore;

        // Calculate percentages
        $sentimentStats = [
            'positive' => [
                'count' => $totalPositive,
                'percentage' => $totalSentiments > 0 ? round(($totalPositive / $totalSentiments) * 100, 2) : 0,
                'total_score' => round($totalPositiveScore, 2),
                'score_percentage' => $totalAllScores > 0 ? round(($totalPositiveScore / $totalAllScores) * 100, 2) : 0
            ],
            'neutral' => [
                'count' => $totalNeutral,
                'percentage' => $totalSentiments > 0 ? round(($totalNeutral / $totalSentiments) * 100, 2) : 0,
                'total_score' => round($totalNeutralScore, 2),
                'score_percentage' => $totalAllScores > 0 ? round(($totalNeutralScore / $totalAllScores) * 100, 2) : 0
            ],
            'negative' => [
                'count' => $totalNegative,
                'percentage' => $totalSentiments > 0 ? round(($totalNegative / $totalSentiments) * 100, 2) : 0,
                'total_score' => round($totalNegativeScore, 2),
                'score_percentage' => $totalAllScores > 0 ? round(($totalNegativeScore / $totalAllScores) * 100, 2) : 0
            ],
            'total' => $totalSentiments,
            'total_all_scores' => round($totalAllScores, 2)
        ];

        // Sentiment traffic (last 30 days) for line chart
        $sentimentTraffic = DB::table('sentiment_results')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total'),
                DB::raw('SUM(CASE WHEN sentiment_label = "positive" THEN 1 ELSE 0 END) as positive'),
                DB::raw('SUM(CASE WHEN sentiment_label = "neutral" THEN 1 ELSE 0 END) as neutral'),
                DB::raw('SUM(CASE WHEN sentiment_label = "negative" THEN 1 ELSE 0 END) as negative')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentEvaluations', 'sentimentDistribution', 'sentimentTraffic', 'sentimentStats'));
    }
}
