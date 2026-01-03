@extends('layouts.app')

@section('title', 'Kelola Mahasiswa')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Kelola Mahasiswa</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item" aria-current="page">Kelola Mahasiswa</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-school"></i> Daftar Mahasiswa</h5>
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Tambah Mahasiswa
                </a>
            </div>
            <div class="card-body">
                @if($students->isEmpty())
                    <div class="text-center py-5">
                        <i class="ti ti-school" style="font-size: 4rem; color: #ccc;"></i>
                        <h5 class="mt-3 text-muted">Belum ada mahasiswa</h5>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Dosen Pembimbing</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                <tr>
                                    <td><strong>{{ $student->nim }}</strong></td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->user->email ?? '-' }}</td>
                                    <td>
                                        @if($student->dosen)
                                            <span class="badge bg-info">{{ $student->dosen->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-info">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
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
            @if(!$students->isEmpty())
            <div class="card-footer">
                {{ $students->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
