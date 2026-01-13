<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\KpEvaluation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DosenController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Get students supervised by this dosen
        $studentIds = Student::where('dosen_id', $user->id)->pluck('id');

        $stats = [
            'total_students' => $studentIds->count(),
            'total_evaluations' => KpEvaluation::whereIn('student_id', $studentIds)->count(),
            'total_pembimbing' => User::whereHas('supervisedStudents', function($q) use ($user) {
                $q->where('dosen_id', $user->id);
            })->where('role', 'pembimbing_lapangan')->distinct()->count(),
        ];

        // Recent evaluations for supervised students
        $recentEvaluations = KpEvaluation::with(['student', 'evaluator', 'sentimentResult'])
            ->whereIn('student_id', $studentIds)
            ->latest()
            ->take(10)
            ->get();

        // Sentiment distribution for supervised students only
        $sentimentDistribution = DB::table('sentiment_results')
            ->join('kp_evaluations', 'sentiment_results.kp_evaluation_id', '=', 'kp_evaluations.id')
            ->whereIn('kp_evaluations.student_id', $studentIds)
            ->select('sentiment_results.sentiment_label', DB::raw('count(*) as count'))
            ->groupBy('sentiment_results.sentiment_label')
            ->get();

        // Total counts by sentiment label (berdasarkan label dominan) - only for supervised students
        $totalPositive = DB::table('sentiment_results')
            ->join('kp_evaluations', 'sentiment_results.kp_evaluation_id', '=', 'kp_evaluations.id')
            ->whereIn('kp_evaluations.student_id', $studentIds)
            ->where('sentiment_results.sentiment_label', 'positive')
            ->count();

        $totalNeutral = DB::table('sentiment_results')
            ->join('kp_evaluations', 'sentiment_results.kp_evaluation_id', '=', 'kp_evaluations.id')
            ->whereIn('kp_evaluations.student_id', $studentIds)
            ->where('sentiment_results.sentiment_label', 'neutral')
            ->count();

        $totalNegative = DB::table('sentiment_results')
            ->join('kp_evaluations', 'sentiment_results.kp_evaluation_id', '=', 'kp_evaluations.id')
            ->whereIn('kp_evaluations.student_id', $studentIds)
            ->where('sentiment_results.sentiment_label', 'negative')
            ->count();

        $totalSentiments = $totalPositive + $totalNeutral + $totalNegative;

        // Total SCORES (sum of all individual scores) - only for supervised students
        $totalPositiveScore = DB::table('sentiment_results')
            ->join('kp_evaluations', 'sentiment_results.kp_evaluation_id', '=', 'kp_evaluations.id')
            ->whereIn('kp_evaluations.student_id', $studentIds)
            ->sum('sentiment_results.positive_score');

        $totalNeutralScore = DB::table('sentiment_results')
            ->join('kp_evaluations', 'sentiment_results.kp_evaluation_id', '=', 'kp_evaluations.id')
            ->whereIn('kp_evaluations.student_id', $studentIds)
            ->sum('sentiment_results.neutral_score');

        $totalNegativeScore = DB::table('sentiment_results')
            ->join('kp_evaluations', 'sentiment_results.kp_evaluation_id', '=', 'kp_evaluations.id')
            ->whereIn('kp_evaluations.student_id', $studentIds)
            ->sum('sentiment_results.negative_score');

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

        // Sentiment traffic (last 30 days) for supervised students only
        $sentimentTraffic = DB::table('sentiment_results')
            ->join('kp_evaluations', 'sentiment_results.kp_evaluation_id', '=', 'kp_evaluations.id')
            ->whereIn('kp_evaluations.student_id', $studentIds)
            ->select(
                DB::raw('DATE(sentiment_results.created_at) as date'),
                DB::raw('count(*) as total'),
                DB::raw('SUM(CASE WHEN sentiment_results.sentiment_label = "positive" THEN 1 ELSE 0 END) as positive'),
                DB::raw('SUM(CASE WHEN sentiment_results.sentiment_label = "neutral" THEN 1 ELSE 0 END) as neutral'),
                DB::raw('SUM(CASE WHEN sentiment_results.sentiment_label = "negative" THEN 1 ELSE 0 END) as negative')
            )
            ->where('sentiment_results.created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(sentiment_results.created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        return view('dosen.dashboard', compact('stats', 'recentEvaluations', 'sentimentDistribution', 'sentimentTraffic', 'sentimentStats'));
    }
}
