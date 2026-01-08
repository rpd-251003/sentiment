@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('content')
@php
    $maintenanceMode = \App\Models\AppSetting::get('maintenance_mode', '0');
@endphp

@if($maintenanceMode == '1')
    <!-- Maintenance Mode -->
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="ti ti-tool" style="font-size: 6rem; color: #FFA500;"></i>
                    </div>
                    <h2 class="mb-3">Under Maintenance</h2>
                    <p class="text-muted mb-4">
                        Sistem sedang dalam pemeliharaan. Mohon maaf atas ketidaknyamanannya.<br>
                        Silakan coba lagi nanti.
                    </p>
                    <div class="alert alert-warning d-inline-block">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Admin dan Kaprodi</strong> tetap dapat mengakses sistem.
                    </div>
                    <div class="mt-4">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-logout"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Normal Dashboard -->
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Dashboard Dosen Pembimbing</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">Dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->

    <div class="row">
    @php
        $myStudents = auth()->user()->supervisedStudents;
        $myEvaluations = auth()->user()->evaluations;
    @endphp

    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Mahasiswa Bimbingan</h6>
                <h4 class="mb-3">{{ $myStudents->count() }} <span class="badge bg-light-primary border border-primary"><i class="ti ti-school"></i></span></h4>
                <p class="mb-0 text-muted text-sm">Mahasiswa yang Anda bimbing</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Evaluasi Saya</h6>
                <h4 class="mb-3">{{ $myEvaluations->count() }} <span class="badge bg-light-success border border-success"><i class="ti ti-clipboard-check"></i></span></h4>
                <p class="mb-0 text-muted text-sm">Evaluasi yang telah Anda buat</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Butuh Evaluasi</h6>
                @php
                    $needsEvaluation = $myStudents->filter(function($student) {
                        return $student->evaluations()->where('evaluator_id', auth()->id())->count() === 0;
                    })->count();
                @endphp
                <h4 class="mb-3">{{ $needsEvaluation }} <span class="badge bg-light-warning border border-warning"><i class="ti ti-alert-circle"></i></span></h4>
                <p class="mb-0 text-muted text-sm">Mahasiswa belum dievaluasi</p>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-users"></i> Daftar Mahasiswa Bimbingan</h5>
            </div>
            <div class="card-body">
                @if($myStudents->isEmpty())
                    <p class="text-muted text-center py-4">Belum ada mahasiswa bimbingan</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Perusahaan</th>
                                    <th>Status Evaluasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myStudents as $student)
                                <tr>
                                    <td>{{ $student->nim }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->internship->company->name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $evaluated = $student->evaluations()->where('evaluator_id', auth()->id())->exists();
                                        @endphp
                                        @if($evaluated)
                                            <span class="badge bg-success">Sudah Dievaluasi</span>
                                        @else
                                            <span class="badge bg-warning">Belum Dievaluasi</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$evaluated)
                                            @can('create', App\Models\KpEvaluation::class)
                                            <a href="{{ route('evaluations.create') }}?student_id={{ $student->id }}" class="btn btn-sm btn-primary">
                                                <i class="ti ti-plus"></i> Evaluasi
                                            </a>
                                            @else
                                            <span class="badge bg-light-secondary">Tidak dapat evaluasi</span>
                                            @endcan
                                        @else
                                            <a href="{{ route('evaluations.index') }}" class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-eye"></i> Lihat
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-bolt"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                @can('create', App\Models\KpEvaluation::class)
                <a href="{{ route('evaluations.create') }}" class="btn btn-primary me-2">
                    <i class="ti ti-plus"></i> Buat Evaluasi Baru
                </a>
                @endcan
                <a href="{{ route('evaluations.index') }}" class="btn btn-outline-primary">
                    <i class="ti ti-list"></i> Lihat Semua Evaluasi
                </a>
            </div>
        </div>
    </div>
    </div>
@endif
@endsection
