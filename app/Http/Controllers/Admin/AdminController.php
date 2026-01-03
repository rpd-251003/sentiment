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

        $sentimentDistribution = DB::table('sentiment_results')
            ->select('sentiment_label', DB::raw('count(*) as count'))
            ->groupBy('sentiment_label')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentEvaluations', 'sentimentDistribution'));
    }
}
