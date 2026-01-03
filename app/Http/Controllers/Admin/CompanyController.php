<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount('internships')->latest()->paginate(15);
        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        Company::create($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function show(Company $company)
    {
        $company->load(['internships.student.user', 'internships.pembimbingLapangan']);
        return view('admin.companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $company->update($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Perusahaan berhasil diupdate.');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', 'Perusahaan berhasil dihapus.');
    }
}
