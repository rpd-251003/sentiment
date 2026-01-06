<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentInternship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'kaprodi', 'dosen', 'pembimbing_lapangan', 'mahasiswa'])],
            'company_id' => ['nullable', 'exists:companies,id', 'required_if:role,pembimbing_lapangan'],
            // Student fields
            'nim' => ['nullable', 'string', 'max:50', 'unique:students,nim', 'required_if:role,mahasiswa'],
            'dosen_id' => ['nullable', 'exists:users,id'],
            'student_company_id' => ['nullable', 'exists:companies,id'],
            'student_pembimbing_lapangan_id' => ['nullable', 'exists:users,id'],
            'start_month' => ['nullable', 'string'],
            'start_year' => ['nullable', 'integer'],
            'end_month' => ['nullable', 'string'],
            'end_year' => ['nullable', 'integer'],
        ]);

        DB::beginTransaction();
        try {
            $validated['password'] = Hash::make($validated['password']);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => $validated['role'],
                'company_id' => $validated['company_id'] ?? null,
            ]);

            // Create student if role is mahasiswa
            if ($validated['role'] === 'mahasiswa') {
                $student = Student::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'nim' => $validated['nim'],
                    'dosen_id' => $validated['dosen_id'] ?? null,
                    'pembimbing_lapangan_id' => $validated['student_pembimbing_lapangan_id'] ?? null,
                ]);

                // Create internship if company is selected
                if (!empty($validated['student_company_id'])) {
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
                        'company_id' => $validated['student_company_id'],
                        'pembimbing_lapangan_id' => $validated['student_pembimbing_lapangan_id'] ?? null,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'kaprodi', 'dosen', 'pembimbing_lapangan', 'mahasiswa'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'company_id' => ['nullable', 'exists:companies,id', 'required_if:role,pembimbing_lapangan'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Clear company_id if role is not pembimbing_lapangan
        if ($validated['role'] !== 'pembimbing_lapangan') {
            $validated['company_id'] = null;
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
