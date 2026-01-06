@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Kelola User</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item" aria-current="page">Kelola User</li>
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
                <h5 class="mb-0"><i class="ti ti-users"></i> Daftar User</h5>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Tambah User
                </a>
            </div>
            <div class="card-body">
                @if($users->isEmpty())
                    <div class="text-center py-5">
                        <i class="ti ti-users" style="font-size: 4rem; color: #ccc;"></i>
                        <h5 class="mt-3 text-muted">Belum ada user</h5>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td><strong>{{ $user->name }}</strong></td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role === 'admin')
                                            <span class="badge bg-primary">Admin</span>
                                        @elseif($user->role === 'kaprodi')
                                            <span class="badge bg-success">Kaprodi</span>
                                        @elseif($user->role === 'dosen')
                                            <span class="badge bg-info">Dosen</span>
                                        @elseif($user->role === 'pembimbing_lapangan')
                                            <span class="badge bg-warning">Pembimbing Lapangan</span>
                                        @else
                                            <span class="badge bg-secondary">Mahasiswa</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $user->created_at->format('d M Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @if(!$users->isEmpty())
            <div class="card-footer">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
