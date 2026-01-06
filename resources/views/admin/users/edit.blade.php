@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit User</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User</a></li>
                    <li class="breadcrumb-item" aria-current="page">Edit</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-edit"></i> Form Edit User</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') ?? $user->name }}" required placeholder="Masukkan nama lengkap">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') ?? $user->email }}" required placeholder="Masukkan email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="admin" {{ (old('role') ?? $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="kaprodi" {{ (old('role') ?? $user->role) === 'kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                            <option value="dosen" {{ (old('role') ?? $user->role) === 'dosen' ? 'selected' : '' }}>Dosen Pembimbing</option>
                            <option value="pembimbing_lapangan" {{ (old('role') ?? $user->role) === 'pembimbing_lapangan' ? 'selected' : '' }}>Pembimbing Lapangan</option>
                            <option value="mahasiswa" {{ (old('role') ?? $user->role) === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="company-field" style="display: none;">
                        <label for="company_id" class="form-label">Perusahaan <span class="text-danger">*</span></label>
                        <select id="company_id" name="company_id" class="form-select @error('company_id') is-invalid @enderror">
                            <option value="">Pilih perusahaan...</option>
                            @foreach(\App\Models\Company::all() as $company)
                                <option value="{{ $company->id }}" {{ (old('company_id') ?? $user->company_id) == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="ti ti-info-circle"></i> Perusahaan tempat pembimbing lapangan bekerja
                        </div>
                    </div>

                    <hr>

                    <div class="alert alert-info" role="alert">
                        <i class="ti ti-info-circle"></i>
                        <strong>Info:</strong> Kosongkan field password jika tidak ingin mengubah password
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               placeholder="Masukkan password baru">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="ti ti-info-circle"></i> Minimal 8 karakter
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                               placeholder="Masukkan ulang password baru">
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-x"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const companyField = document.getElementById('company-field');
    const companySelect = document.getElementById('company_id');

    function toggleCompanyField() {
        if (roleSelect.value === 'pembimbing_lapangan') {
            companyField.style.display = 'block';
            companySelect.required = true;
        } else {
            companyField.style.display = 'none';
            companySelect.required = false;
            companySelect.value = '';
        }
    }

    // Check on page load
    toggleCompanyField();

    roleSelect.addEventListener('change', toggleCompanyField);
});
</script>
@endpush
