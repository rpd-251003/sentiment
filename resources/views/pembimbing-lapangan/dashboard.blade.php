@extends('layouts.app')

@section('title', 'Dashboard Pembimbing Lapangan')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Dashboard Pembimbing Lapangan</h5>
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
        $myInternships = auth()->user()->fieldSupervisedInternships;
        $myEvaluations = auth()->user()->evaluations;
    @endphp

    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Mahasiswa Magang</h6>
                <h4 class="mb-3">{{ $myInternships->count() }} <span class="badge bg-light-primary border border-primary"><i class="ti ti-briefcase"></i></span></h4>
                <p class="mb-0 text-muted text-sm">Mahasiswa yang Anda dampingi</p>
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
                    $needsEvaluation = $myInternships->filter(function($internship) {
                        return $internship->student->evaluations()->where('evaluator_id', auth()->id())->count() === 0;
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
                <h5 class="mb-0"><i class="ti ti-briefcase"></i> Daftar Mahasiswa Magang</h5>
            </div>
            <div class="card-body">
                @if($myInternships->isEmpty())
                    <p class="text-muted text-center py-4">Belum ada mahasiswa magang</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Perusahaan</th>
                                    <th>Dosen Pembimbing</th>
                                    <th>Status Evaluasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myInternships as $internship)
                                <tr>
                                    <td>{{ $internship->student->nim }}</td>
                                    <td>{{ $internship->student->name }}</td>
                                    <td>{{ $internship->company->name }}</td>
                                    <td>{{ $internship->student->dosen->name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $evaluated = $internship->student->evaluations()->where('evaluator_id', auth()->id())->exists();
                                        @endphp
                                        @if($evaluated)
                                            <span class="badge bg-success">Sudah Dievaluasi</span>
                                        @else
                                            <span class="badge bg-warning">Belum Dievaluasi</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$evaluated)
                                            <a href="{{ route('evaluations.create') }}?student_id={{ $internship->student_id }}" class="btn btn-sm btn-primary">
                                                <i class="ti ti-plus"></i> Evaluasi
                                            </a>
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
                <a href="{{ route('evaluations.create') }}" class="btn btn-primary me-2">
                    <i class="ti ti-plus"></i> Buat Evaluasi Baru
                </a>
                <a href="{{ route('evaluations.index') }}" class="btn btn-outline-primary">
                    <i class="ti ti-list"></i> Lihat Semua Evaluasi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
