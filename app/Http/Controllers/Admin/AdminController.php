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

        // Sentiment distribution for pie chart
        $sentimentDistribution = DB::table('sentiment_results')
            ->select('sentiment_label', DB::raw('count(*) as count'))
            ->groupBy('sentiment_label')
            ->get();

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

        return view('admin.dashboard', compact('stats', 'recentEvaluations', 'sentimentDistribution', 'sentimentTraffic'));
    }
}
