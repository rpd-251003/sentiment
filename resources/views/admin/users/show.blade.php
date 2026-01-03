@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Detail User</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User</a></li>
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
                <h5 class="mb-0"><i class="ti ti-user"></i> Informasi User</h5>
                <div>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">
                        <i class="ti ti-edit"></i> Edit
                    </a>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="ti ti-trash"></i> Hapus
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Nama Lengkap</h6>
                        <p class="mb-0"><strong>{{ $user->name }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Email</h6>
                        <p class="mb-0">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Role</h6>
                        <p class="mb-0">
                            @if($user->role === 'admin')
                                <span class="badge bg-primary">Admin</span>
                            @elseif($user->role === 'dosen')
                                <span class="badge bg-info">Dosen Pembimbing</span>
                            @elseif($user->role === 'pembimbing_lapangan')
                                <span class="badge bg-warning">Pembimbing Lapangan</span>
                            @else
                                <span class="badge bg-secondary">Mahasiswa</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Terdaftar Sejak</h6>
                        <p class="mb-0">
                            {{ $user->created_at->format('d F Y') }}<br>
                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                        </p>
                    </div>
                </div>

                <hr>

                @if($user->role === 'dosen')
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Mahasiswa Bimbingan ({{ $user->supervisedStudents->count() }})</h6>
                        @if($user->supervisedStudents->isEmpty())
                            <p class="text-muted">Belum ada mahasiswa bimbingan</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->supervisedStudents as $student)
                                        <tr>
                                            <td>{{ $student->nim }}</td>
                                            <td><strong>{{ $student->name }}</strong></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @elseif($user->role === 'pembimbing_lapangan')
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Mahasiswa yang Dibimbing ({{ $user->fieldSupervisedInternships->count() }})</h6>
                        @if($user->fieldSupervisedInternships->isEmpty())
                            <p class="text-muted">Belum ada mahasiswa yang dibimbing</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Perusahaan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->fieldSupervisedInternships as $internship)
                                        <tr>
                                            <td>{{ $internship->student->nim }}</td>
                                            <td><strong>{{ $internship->student->name }}</strong></td>
                                            <td>{{ $internship->company->name }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @elseif($user->role === 'mahasiswa' && $user->student)
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Data Mahasiswa</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-2"><strong>NIM:</strong> {{ $user->student->nim }}</p>
                                <p class="mb-2"><strong>Dosen Pembimbing:</strong> {{ $user->student->dosen->name ?? '-' }}</p>
                                @if($user->student->internship)
                                <p class="mb-0"><strong>Perusahaan:</strong> {{ $user->student->internship->company->name }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mb-3">
                    <h6 class="text-muted mb-3">Evaluasi yang Dibuat ({{ $user->evaluations->count() }})</h6>
                    @if($user->evaluations->isEmpty())
                        <p class="text-muted">Belum ada evaluasi</p>
                    @else
                        <p class="text-muted">{{ $user->evaluations->count() }} evaluasi telah dibuat</p>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
