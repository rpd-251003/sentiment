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

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted mb-0">Pembimbing Lapangan ({{ $company->pembimbingLapangans->count() }})</h6>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPembimbingModal">
                            <i class="ti ti-plus"></i> Tambah Pembimbing
                        </button>
                    </div>
                    @if($company->pembimbingLapangans->isEmpty())
                        <p class="text-muted">Belum ada pembimbing lapangan untuk perusahaan ini</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($company->pembimbingLapangans as $pembimbing)
                                    <tr>
                                        <td><strong>{{ $pembimbing->name }}</strong></td>
                                        <td>{{ $pembimbing->email }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.edit', $pembimbing) }}" class="btn btn-xs btn-outline-primary">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $pembimbing) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pembimbing lapangan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
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

<!-- Modal Add Pembimbing Lapangan -->
<div class="modal fade" id="addPembimbingModal" tabindex="-1" aria-labelledby="addPembimbingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <input type="hidden" name="role" value="pembimbing_lapangan">
                <input type="hidden" name="company_id" value="{{ $company->id }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="addPembimbingModalLabel">
                        <i class="ti ti-plus"></i> Tambah Pembimbing Lapangan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" required placeholder="Masukkan nama lengkap">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" required placeholder="Masukkan email">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" id="password" name="password" class="form-control" required placeholder="Minimal 8 karakter">
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Masukkan ulang password">
                    </div>

                    <div class="alert alert-info" role="alert">
                        <i class="ti ti-info-circle"></i>
                        Pembimbing lapangan ini akan otomatis terhubung dengan perusahaan <strong>{{ $company->name }}</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
