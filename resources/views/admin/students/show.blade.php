@extends('layouts.app')

@section('title', 'Detail Mahasiswa')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Detail Mahasiswa</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Mahasiswa</a></li>
                    <li class="breadcrumb-item" aria-current="page">Detail</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-user"></i> Informasi Mahasiswa</h5>
                <div>
                    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-primary">
                        <i class="ti ti-edit"></i> Edit
                    </a>
                    <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="ti ti-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">NIM</h6>
                        <p class="mb-0"><strong>{{ $student->nim }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Nama Lengkap</h6>
                        <p class="mb-0"><strong>{{ $student->name }}</strong></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Email</h6>
                        <p class="mb-0">{{ $student->user->email ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Dosen Pembimbing</h6>
                        <p class="mb-0">
                            @if($student->dosen)
                                <span class="badge bg-info">{{ $student->dosen->name }}</span>
                            @else
                                <span class="text-muted">Belum ditentukan</span>
                            @endif
                        </p>
                    </div>
                </div>

                <hr>

                @if($student->internship)
                <div class="mb-4">
                    <h6 class="text-muted mb-3">Informasi Magang</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Perusahaan:</strong> {{ $student->internship->company->name }}</p>
                                    <p class="mb-2"><strong>Periode:</strong> {{ $student->internship->start_date ? $student->internship->start_date->format('d M Y') : '-' }} - {{ $student->internship->end_date ? $student->internship->end_date->format('d M Y') : '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Pembimbing Lapangan:</strong> {{ $student->internship->pembimbingLapangan->name ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="alert alert-warning" role="alert">
                    <i class="ti ti-alert-triangle"></i> Mahasiswa ini belum memiliki data magang
                </div>
                @endif

                <hr>

                <div class="mb-3">
                    <h6 class="text-muted mb-3">Riwayat Evaluasi ({{ $student->evaluations->count() }})</h6>
                    @if($student->evaluations->isEmpty())
                        <p class="text-muted">Belum ada evaluasi</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Evaluator</th>
                                        <th>Role</th>
                                        <th>Sentimen</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->evaluations as $evaluation)
                                    <tr>
                                        <td>{{ $evaluation->created_at->format('d M Y') }}</td>
                                        <td>{{ $evaluation->evaluator->name }}</td>
                                        <td>
                                            @if($evaluation->evaluator_role === 'admin')
                                                <span class="badge bg-primary">Admin</span>
                                            @elseif($evaluation->evaluator_role === 'dosen')
                                                <span class="badge bg-info">Dosen</span>
                                            @elseif($evaluation->evaluator_role === 'pembimbing_lapangan')
                                                <span class="badge bg-warning">Pembimbing</span>
                                            @else
                                                <span class="badge bg-secondary">Mahasiswa</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($evaluation->sentimentResult)
                                                @if($evaluation->sentimentResult->sentiment_label === 'positive')
                                                    <span class="badge bg-success">Positive</span>
                                                @elseif($evaluation->sentimentResult->sentiment_label === 'negative')
                                                    <span class="badge bg-danger">Negative</span>
                                                @else
                                                    <span class="badge bg-secondary">Neutral</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('evaluations.show', $evaluation) }}" class="btn btn-xs btn-outline-primary">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
