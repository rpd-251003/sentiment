<?php

namespace App\Http\Controllers;

use App\Models\KpEvaluation;
use App\Models\Student;
use App\Models\SentimentResult;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KpEvaluationController extends Controller
{
    protected SentimentAnalysisService $sentimentService;

    public function __construct(SentimentAnalysisService $sentimentService)
    {
        $this->sentimentService = $sentimentService;
    }

    public function index()
    {
        $this->authorize('viewAny', KpEvaluation::class);

        $user = auth()->user();
        $query = KpEvaluation::with(['student', 'evaluator', 'sentimentResult']);

        if ($user->isDosen()) {
            $query->whereHas('student', fn($q) => $q->where('dosen_id', $user->id));
        } elseif ($user->isPembimbingLapangan()) {
            // Pembimbing lapangan hanya bisa lihat evaluasi yang dia buat sendiri
            $query->where('evaluator_id', $user->id);
        } elseif ($user->isMahasiswa()) {
            $query->whereHas('student', fn($q) => $q->where('user_id', $user->id));
        }

        $evaluations = $query->latest()->paginate(15);

        return view('evaluations.index', compact('evaluations'));
    }

    public function create()
    {
        $this->authorize('create', KpEvaluation::class);

        $user = auth()->user();
        $students = $this->getAuthorizedStudents($user);

        return view('evaluations.create', compact('students'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', KpEvaluation::class);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'rating' => 'nullable|integer|min:1|max:10',
            'comment_text' => 'required|string|max:5000',
        ]);

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            DB::beginTransaction();
            try {
                // Step 1: Create evaluation
                $evaluation = KpEvaluation::create([
                    'student_id' => $validated['student_id'],
                    'evaluator_id' => auth()->id(),
                    'evaluator_role' => auth()->user()->role,
                    'rating' => $validated['rating'],
                    'comment_text' => $validated['comment_text'],
                ]);

                // Step 2: Analyze sentiment via API
                $sentimentResult = $this->sentimentService->analyze($validated['comment_text']);

                // Step 3: Save sentiment result
                SentimentResult::create([
                    'kp_evaluation_id' => $evaluation->id,
                    'sentiment_label' => $sentimentResult['sentiment_label'],
                    'sentiment_score' => $sentimentResult['sentiment_score'],
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Evaluasi berhasil disimpan dengan analisis sentimen!',
                    'redirect_url' => route('evaluations.show', $evaluation->id),
                    'data' => [
                        'evaluation_id' => $evaluation->id,
                        'sentiment_label' => $sentimentResult['sentiment_label'],
                        'sentiment_score' => $sentimentResult['sentiment_score'],
                    ]
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan evaluasi: ' . $e->getMessage()
                ], 500);
            }
        }

        // Fallback to normal form submission
        DB::beginTransaction();
        try {
            $evaluation = KpEvaluation::create([
                'student_id' => $validated['student_id'],
                'evaluator_id' => auth()->id(),
                'evaluator_role' => auth()->user()->role,
                'rating' => $validated['rating'],
                'comment_text' => $validated['comment_text'],
            ]);

            $sentimentResult = $this->sentimentService->analyze($validated['comment_text']);

            SentimentResult::create([
                'kp_evaluation_id' => $evaluation->id,
                'sentiment_label' => $sentimentResult['sentiment_label'],
                'sentiment_score' => $sentimentResult['sentiment_score'],
            ]);

            DB::commit();

            return redirect()->route('evaluations.show', $evaluation)
                ->with('success', 'Evaluation submitted successfully with sentiment analysis.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to analyze sentiment: ' . $e->getMessage());
        }
    }

    public function show(KpEvaluation $evaluation)
    {
        $this->authorize('view', $evaluation);

        $evaluation->load(['student', 'evaluator', 'sentimentResult']);

        return view('evaluations.show', compact('evaluation'));
    }

    public function edit(KpEvaluation $evaluation)
    {
        $this->authorize('update', $evaluation);

        $user = auth()->user();
        $students = $this->getAuthorizedStudents($user);

        return view('evaluations.edit', compact('evaluation', 'students'));
    }

    public function update(Request $request, KpEvaluation $evaluation)
    {
        $this->authorize('update', $evaluation);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'rating' => 'nullable|integer|min:1|max:10',
            'comment_text' => 'required|string|max:5000',
        ]);

        DB::beginTransaction();
        try {
            $evaluation->update([
                'student_id' => $validated['student_id'],
                'rating' => $validated['rating'],
                'comment_text' => $validated['comment_text'],
            ]);

            $sentimentResult = $this->sentimentService->analyze($validated['comment_text']);

            $evaluation->sentimentResult()->updateOrCreate(
                ['kp_evaluation_id' => $evaluation->id],
                [
                    'sentiment_label' => $sentimentResult['sentiment_label'],
                    'sentiment_score' => $sentimentResult['sentiment_score'],
                ]
            );

            DB::commit();

            return redirect()->route('evaluations.show', $evaluation)
                ->with('success', 'Evaluation updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update evaluation: ' . $e->getMessage());
        }
    }

    public function destroy(KpEvaluation $evaluation)
    {
        $this->authorize('delete', $evaluation);

        $evaluation->delete();

        return redirect()->route('evaluations.index')
            ->with('success', 'Evaluation deleted successfully.');
    }

    protected function getAuthorizedStudents($user)
    {
        $query = Student::query();

        if ($user->isDosen()) {
            $query->where('dosen_id', $user->id);
        } elseif ($user->isPembimbingLapangan()) {
            $query->whereHas('internship', fn($q) => $q->where('pembimbing_lapangan_id', $user->id));
        } elseif ($user->isMahasiswa()) {
            $query->where('user_id', $user->id);
        }

        return $query->get();
    }
}
