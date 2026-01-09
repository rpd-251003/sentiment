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

        return view('evaluations.index');
    }

    public function datatables(Request $request)
    {
        $this->authorize('viewAny', KpEvaluation::class);

        $user = auth()->user();
        $query = KpEvaluation::with(['student', 'evaluator', 'sentimentResult']);

        // Apply role-based filters
        if ($user->isDosen()) {
            $query->whereHas('student', fn($q) => $q->where('dosen_id', $user->id));
        } elseif ($user->isPembimbingLapangan()) {
            $query->where('evaluator_id', $user->id);
        } elseif ($user->isMahasiswa()) {
            $query->whereHas('student', fn($q) => $q->where('user_id', $user->id));
        }

        // Get DataTables parameters
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchValue = $request->input('search.value', '');
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');

        // Column mapping for ordering
        $columns = [
            0 => 'students.name',
            1 => 'evaluators.name',
            2 => 'evaluator_role',
            3 => 'rating',
            4 => 'sentiment_results.sentiment_label',
            5 => 'kp_evaluations.created_at'
        ];

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->whereHas('student', function($sq) use ($searchValue) {
                    $sq->where('name', 'like', "%{$searchValue}%")
                       ->orWhere('nim', 'like', "%{$searchValue}%");
                })
                ->orWhereHas('evaluator', function($eq) use ($searchValue) {
                    $eq->where('name', 'like', "%{$searchValue}%");
                })
                ->orWhere('evaluator_role', 'like', "%{$searchValue}%")
                ->orWhere('comment_text', 'like', "%{$searchValue}%");
            });
        }

        // Get total records before filtering
        $totalRecords = KpEvaluation::count();

        // Get filtered records count
        $filteredRecords = $query->count();

        // Apply ordering
        if (isset($columns[$orderColumn])) {
            $orderColumnName = $columns[$orderColumn];

            if ($orderColumnName === 'students.name') {
                $query->join('students', 'kp_evaluations.student_id', '=', 'students.id')
                      ->orderBy('students.name', $orderDir)
                      ->select('kp_evaluations.*');
            } elseif ($orderColumnName === 'evaluators.name') {
                $query->join('users as evaluators', 'kp_evaluations.evaluator_id', '=', 'evaluators.id')
                      ->orderBy('evaluators.name', $orderDir)
                      ->select('kp_evaluations.*');
            } elseif ($orderColumnName === 'sentiment_results.sentiment_label') {
                $query->leftJoin('sentiment_results', 'kp_evaluations.id', '=', 'sentiment_results.kp_evaluation_id')
                      ->orderBy('sentiment_results.sentiment_label', $orderDir)
                      ->select('kp_evaluations.*');
            } else {
                $query->orderBy($orderColumnName, $orderDir);
            }
        } else {
            $query->latest();
        }

        // Apply pagination
        $evaluations = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $data = $evaluations->map(function($evaluation) {
            return [
                'student' => [
                    'name' => $evaluation->student->name,
                    'nim' => $evaluation->student->nim
                ],
                'evaluator' => $evaluation->evaluator->name,
                'evaluator_role' => $evaluation->evaluator_role,
                'rating' => $evaluation->rating,
                'sentiment' => $evaluation->sentimentResult ? [
                    'label' => $evaluation->sentimentResult->sentiment_label,
                    'score' => $evaluation->sentimentResult->sentiment_score
                ] : null,
                'created_at' => [
                    'date' => $evaluation->created_at->format('d M Y'),
                    'time' => $evaluation->created_at->format('H:i')
                ],
                'id' => $evaluation->id,
                'can_update' => auth()->user()->can('update', $evaluation)
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
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
