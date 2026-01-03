@extends('layouts.app')

@section('title', 'Detail Perusahaan')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Detail Perusahaan</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">Perusahaan</a></li>
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
                <h5 class="mb-0"><i class="ti ti-building"></i> Informasi Perusahaan</h5>
                <div>
                    <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-sm btn-primary">
                        <i class="ti ti-edit"></i> Edit
                    </a>
                    <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus perusahaan ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="ti ti-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Nama Perusahaan</h6>
                    <p class="mb-0"><strong>{{ $company->name }}</strong></p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-2">Alamat</h6>
                        <p class="mb-0">{{ $company->address ?? '-' }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Telepon</h6>
                        <p class="mb-0">{{ $company->phone ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Email</h6>
                        <p class="mb-0">{{ $company->email ?? '-' }}</p>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <h6 class="text-muted mb-3">Mahasiswa Magang ({{ $company->internships->count() }})</h6>
                    @if($company->internships->isEmpty())
                        <p class="text-muted">Belum ada mahasiswa yang magang di perusahaan ini</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>NIM</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>Pembimbing Lapangan</th>
                                        <th>Periode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($company->internships as $internship)
                                    <tr>
                                        <td>{{ $internship->student->nim }}</td>
                                        <td><strong>{{ $internship->student->name }}</strong></td>
                                        <td>
                                            @if($internship->pembimbingLapangan)
                                                <span class="badge bg-warning">{{ $internship->pembimbingLapangan->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $internship->start_date ? $internship->start_date->format('d M Y') : '-' }} -
                                                {{ $internship->end_date ? $internship->end_date->format('d M Y') : '-' }}
                                            </small>
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
                <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
