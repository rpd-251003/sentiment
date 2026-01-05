<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\Company;
use App\Models\StudentInternship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['user', 'dosen', 'pembimbingLapangan'])->latest()->paginate(15);
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $users = User::where('role', 'mahasiswa')
            ->whereDoesntHave('student')
            ->get();
        $dosens = User::where('role', 'dosen')->get();
        $companies = Company::all();

        return view('admin.students.create', compact('users', 'dosens', 'companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id', 'unique:students,user_id'],
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:50', 'unique:students,nim'],
            'dosen_id' => ['nullable', 'exists:users,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'pembimbing_lapangan_id' => ['nullable', 'exists:users,id'],
            'start_month' => ['nullable', 'string'],
            'start_year' => ['nullable', 'integer'],
            'end_month' => ['nullable', 'string'],
            'end_year' => ['nullable', 'integer'],
        ]);

        DB::beginTransaction();
        try {
            $student = Student::create([
                'user_id' => $validated['user_id'],
                'name' => $validated['name'],
                'nim' => $validated['nim'],
                'dosen_id' => $validated['dosen_id'],
                'pembimbing_lapangan_id' => $validated['pembimbing_lapangan_id'] ?? null,
            ]);

            // Create internship if company is selected
            if (!empty($validated['company_id'])) {
                // Prepare dates from month and year
                $startDate = null;
                $endDate = null;

                if (!empty($validated['start_month']) && !empty($validated['start_year'])) {
                    $startDate = $validated['start_year'] . '-' . $validated['start_month'] . '-01';
                }

                if (!empty($validated['end_month']) && !empty($validated['end_year'])) {
                    // Get last day of the month
                    $endDate = date('Y-m-t', strtotime($validated['end_year'] . '-' . $validated['end_month'] . '-01'));
                }

                StudentInternship::create([
                    'student_id' => $student->id,
                    'company_id' => $validated['company_id'],
                    'pembimbing_lapangan_id' => $validated['pembimbing_lapangan_id'] ?? null,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.students.index')
                ->with('success', 'Mahasiswa berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Student $student)
    {
        $student->load(['user', 'dosen', 'pembimbingLapangan', 'internship.company', 'internship.pembimbingLapangan', 'evaluations.evaluator', 'evaluations.sentimentResult']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $users = User::where('role', 'mahasiswa')
            ->where(function($query) use ($student) {
                $query->whereDoesntHave('student')
                    ->orWhere('id', $student->user_id);
            })
            ->get();
        $dosens = User::where('role', 'dosen')->get();
        $companies = Company::all();
        $student->load('internship');

        return view('admin.students.edit', compact('student', 'users', 'dosens', 'companies'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id', 'unique:students,user_id,' . $student->id],
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:50', 'unique:students,nim,' . $student->id],
            'dosen_id' => ['nullable', 'exists:users,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'pembimbing_lapangan_id' => ['nullable', 'exists:users,id'],
            'start_month' => ['nullable', 'string'],
            'start_year' => ['nullable', 'integer'],
            'end_month' => ['nullable', 'string'],
            'end_year' => ['nullable', 'integer'],
        ]);

        DB::beginTransaction();
        try {
            $student->update([
                'user_id' => $validated['user_id'],
                'name' => $validated['name'],
                'nim' => $validated['nim'],
                'dosen_id' => $validated['dosen_id'],
                'pembimbing_lapangan_id' => $validated['pembimbing_lapangan_id'] ?? null,
            ]);

            // Update or create internship
            if (!empty($validated['company_id'])) {
                // Prepare dates from month and year
                $startDate = null;
                $endDate = null;

                if (!empty($validated['start_month']) && !empty($validated['start_year'])) {
                    $startDate = $validated['start_year'] . '-' . $validated['start_month'] . '-01';
                }

                if (!empty($validated['end_month']) && !empty($validated['end_year'])) {
                    // Get last day of the month
                    $endDate = date('Y-m-t', strtotime($validated['end_year'] . '-' . $validated['end_month'] . '-01'));
                }

                StudentInternship::updateOrCreate(
                    ['student_id' => $student->id],
                    [
                        'company_id' => $validated['company_id'],
                        'pembimbing_lapangan_id' => $validated['pembimbing_lapangan_id'] ?? null,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]
                );
            } else {
                // Delete internship if company is not selected
                StudentInternship::where('student_id', $student->id)->delete();
            }

            DB::commit();
            return redirect()->route('admin.students.index')
                ->with('success', 'Mahasiswa berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Mahasiswa berhasil dihapus.');
    }

    public function getPembimbingLapangan(Company $company)
    {
        $pembimbingLapangans = User::where('role', 'pembimbing_lapangan')
            ->where('company_id', $company->id)
            ->get(['id', 'name']);

        return response()->json($pembimbingLapangans);
    }
}
