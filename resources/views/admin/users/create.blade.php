@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Tambah User</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User</a></li>
                    <li class="breadcrumb-item" aria-current="page">Tambah</li>
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
                <h5 class="mb-0"><i class="ti ti-plus"></i> Form Tambah User</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required placeholder="Masukkan nama lengkap">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required placeholder="Masukkan email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">Pilih role...</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="kaprodi" {{ old('role') === 'kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                            <option value="dosen" {{ old('role') === 'dosen' ? 'selected' : '' }}>Dosen Pembimbing</option>
                            <option value="pembimbing_lapangan" {{ old('role') === 'pembimbing_lapangan' ? 'selected' : '' }}>Pembimbing Lapangan</option>
                            <option value="mahasiswa" {{ old('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
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
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
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

                    <!-- Student Fields -->
                    <div id="student-fields" style="display: none;">
                        <hr class="my-4">
                        <h6 class="text-primary mb-3"><i class="ti ti-school"></i> Data Mahasiswa</h6>

                        <div class="mb-3">
                            <label for="nim" class="form-label">NIM <span class="text-danger">*</span></label>
                            <input type="text" id="nim" name="nim" class="form-control @error('nim') is-invalid @enderror"
                                   value="{{ old('nim') }}" placeholder="Masukkan NIM">
                            @error('nim')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="dosen_id" class="form-label">Dosen Pembimbing</label>
                            <select id="dosen_id" name="dosen_id" class="form-select @error('dosen_id') is-invalid @enderror">
                                <option value="">Pilih dosen pembimbing...</option>
                                @foreach(\App\Models\User::where('role', 'dosen')->get() as $dosen)
                                    <option value="{{ $dosen->id }}" {{ old('dosen_id') == $dosen->id ? 'selected' : '' }}>
                                        {{ $dosen->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dosen_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="student_company_id" class="form-label">Perusahaan Magang</label>
                            <select id="student_company_id" name="student_company_id" class="form-select @error('student_company_id') is-invalid @enderror">
                                <option value="">Pilih perusahaan...</option>
                                @foreach(\App\Models\Company::all() as $company)
                                    <option value="{{ $company->id }}" {{ old('student_company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="student_pembimbing_lapangan_id" class="form-label">Pembimbing Lapangan</label>
                            <select id="student_pembimbing_lapangan_id" name="student_pembimbing_lapangan_id" class="form-select @error('student_pembimbing_lapangan_id') is-invalid @enderror" disabled>
                                <option value="">Pilih perusahaan terlebih dahulu...</option>
                            </select>
                            @error('student_pembimbing_lapangan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_month" class="form-label">Bulan Mulai Magang</label>
                                    <select id="start_month" name="start_month" class="form-select @error('start_month') is-invalid @enderror">
                                        <option value="">Pilih bulan...</option>
                                        @foreach(['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $month)
                                            <option value="{{ $num }}" {{ old('start_month') == $num ? 'selected' : '' }}>{{ $month }}</option>
                                        @endforeach
                                    </select>
                                    @error('start_month')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_year" class="form-label">Tahun Mulai</label>
                                    <select id="start_year" name="start_year" class="form-select @error('start_year') is-invalid @enderror">
                                        <option value="">Pilih tahun...</option>
                                        @for($year = date('Y') + 1; $year >= date('Y') - 5; $year--)
                                            <option value="{{ $year }}" {{ old('start_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </select>
                                    @error('start_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_month" class="form-label">Bulan Selesai Magang</label>
                                    <select id="end_month" name="end_month" class="form-select @error('end_month') is-invalid @enderror">
                                        <option value="">Pilih bulan...</option>
                                        @foreach(['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $month)
                                            <option value="{{ $num }}" {{ old('end_month') == $num ? 'selected' : '' }}>{{ $month }}</option>
                                        @endforeach
                                    </select>
                                    @error('end_month')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_year" class="form-label">Tahun Selesai</label>
                                    <select id="end_year" name="end_year" class="form-select @error('end_year') is-invalid @enderror">
                                        <option value="">Pilih tahun...</option>
                                        @for($year = date('Y') + 5; $year >= date('Y') - 5; $year--)
                                            <option value="{{ $year }}" {{ old('end_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </select>
                                    @error('end_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="text-primary mb-3"><i class="ti ti-lock"></i> Password</h6>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               required placeholder="Masukkan password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="ti ti-info-circle"></i> Minimal 8 karakter
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                               required placeholder="Masukkan ulang password">
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy"></i> Simpan
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
    const studentFields = document.getElementById('student-fields');
    const nimInput = document.getElementById('nim');
    const studentCompanySelect = document.getElementById('student_company_id');
    const studentPembimbingSelect = document.getElementById('student_pembimbing_lapangan_id');

    function toggleFields() {
        // Toggle company field for pembimbing lapangan
        if (roleSelect.value === 'pembimbing_lapangan') {
            companyField.style.display = 'block';
            companySelect.required = true;
        } else {
            companyField.style.display = 'none';
            companySelect.required = false;
            companySelect.value = '';
        }

        // Toggle student fields for mahasiswa
        if (roleSelect.value === 'mahasiswa') {
            studentFields.style.display = 'block';
            nimInput.required = true;
        } else {
            studentFields.style.display = 'none';
            nimInput.required = false;
            nimInput.value = '';
            document.getElementById('dosen_id').value = '';
            studentCompanySelect.value = '';
            studentPembimbingSelect.value = '';
            studentPembimbingSelect.disabled = true;
        }
    }

    // Dynamic pembimbing lapangan based on company
    function loadPembimbingLapangan(companyId) {
        studentPembimbingSelect.innerHTML = '<option value="">Loading...</option>';
        studentPembimbingSelect.disabled = true;

        if (companyId) {
            fetch(`{{ url('admin/api/pembimbing-lapangan') }}/${companyId}`)
                .then(response => response.json())
                .then(data => {
                    studentPembimbingSelect.innerHTML = '<option value="">Pilih pembimbing lapangan...</option>';

                    if (data.length > 0) {
                        data.forEach(pembimbing => {
                            const option = document.createElement('option');
                            option.value = pembimbing.id;
                            option.textContent = pembimbing.name;
                            studentPembimbingSelect.appendChild(option);
                        });
                        studentPembimbingSelect.disabled = false;
                    } else {
                        studentPembimbingSelect.innerHTML = '<option value="">Tidak ada pembimbing lapangan untuk perusahaan ini</option>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    studentPembimbingSelect.innerHTML = '<option value="">Error loading data</option>';
                });
        } else {
            studentPembimbingSelect.innerHTML = '<option value="">Pilih perusahaan terlebih dahulu...</option>';
        }
    }

    // Check on page load
    toggleFields();

    roleSelect.addEventListener('change', toggleFields);
    studentCompanySelect.addEventListener('change', function() {
        loadPembimbingLapangan(this.value);
    });

    // Load pembimbing if company already selected (for validation errors)
    if (studentCompanySelect.value) {
        loadPembimbingLapangan(studentCompanySelect.value);
    }
});
</script>
@endpush
