<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentsViewController extends Controller
{
    public function index()
    {
        return view('students.index');
    }

    public function datatables(Request $request)
    {
        try {
            $user = Auth::user();

        // Build query based on user role
        $query = Student::with(['dosen', 'pembimbingLapangan', 'internship.company']);

        // If user is dosen, only show students they supervise
        if ($user->role === 'dosen') {
            $query->where('dosen_id', $user->id);
        }
        // Admin and kaprodi can see all students (no filter needed)

        // Get DataTables parameters
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchValue = $request->input('search.value', '');
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        // Column mapping for ordering
        $columns = [
            0 => 'name',
            1 => 'dosen.name',
            2 => 'pembimbing.name',
            3 => 'companies.name',
            4 => null, // evaluations_count - not sortable
            5 => null, // latest_evaluation - not sortable
        ];

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('nim', 'like', "%{$searchValue}%")
                    ->orWhereHas('dosen', function ($sq) use ($searchValue) {
                        $sq->where('name', 'like', "%{$searchValue}%");
                    })
                    ->orWhereHas('pembimbingLapangan', function ($sq) use ($searchValue) {
                        $sq->where('name', 'like', "%{$searchValue}%");
                    })
                    ->orWhereHas('internship.company', function ($sq) use ($searchValue) {
                        $sq->where('name', 'like', "%{$searchValue}%");
                    });
            });
        }

        // Get total records (before filtering)
        if ($user->role === 'dosen') {
            $totalRecords = Student::where('dosen_id', $user->id)->count();
        } else {
            $totalRecords = Student::count();
        }

        // Get filtered records count
        $filteredRecords = $query->count();

        // Apply ordering
        if (isset($columns[$orderColumn])) {
            $orderColumnName = $columns[$orderColumn];

            if ($orderColumn == 1) {
                // Dosen name ordering
                $query->leftJoin('users as dosen', 'students.dosen_id', '=', 'dosen.id')
                    ->orderBy('dosen.name', $orderDir)
                    ->select('students.*');
            } elseif ($orderColumn == 2) {
                // Pembimbing name ordering
                $query->leftJoin('users as pembimbing', 'students.pembimbing_lapangan_id', '=', 'pembimbing.id')
                    ->orderBy('pembimbing.name', $orderDir)
                    ->select('students.*');
            } elseif ($orderColumn == 3) {
                // Company name ordering
                $query->leftJoin('student_internships', 'students.id', '=', 'student_internships.student_id')
                    ->leftJoin('companies', 'student_internships.company_id', '=', 'companies.id')
                    ->orderBy('companies.name', $orderDir)
                    ->select('students.*');
            } elseif ($orderColumnName) {
                $query->orderBy($orderColumnName, $orderDir);
            }
        } else {
            // Default ordering by name
            $query->orderBy('name', 'asc');
        }

        // Apply pagination
        $students = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $data = $students->map(function ($student) {
            $latest = $student->evaluations()->latest()->first();

            return [
                'student_info' => [
                    'name' => $student->name,
                    'nim' => $student->nim
                ],
                'dosen_name' => $student->dosen ? $student->dosen->name : null,
                'pembimbing_name' => $student->pembimbingLapangan ? $student->pembimbingLapangan->name : null,
                'company_name' => $student->internship && $student->internship->company
                    ? $student->internship->company->name
                    : null,
                'evaluations_count' => $student->evaluations()->count(),
                'latest_evaluation' => $latest ? [
                    'date' => $latest->created_at->format('d/m/Y'),
                    'time' => $latest->created_at->format('H:i'),
                    'evaluator' => $latest->evaluator->name
                ] : null,
                'actions' => $student->id,
            ];
        });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Students datatables error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
