@extends('layouts.app')

@section('title', 'Edit Mahasiswa')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Mahasiswa</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Mahasiswa</a></li>
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
                <h5 class="mb-0"><i class="ti ti-edit"></i> Form Edit Mahasiswa</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.students.update', $student) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="user_id" class="form-label">Akun User <span class="text-danger">*</span></label>
                        <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ (old('user_id') ?? $student->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') ?? $student->name }}" required placeholder="Masukkan nama lengkap">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nim" class="form-label">NIM <span class="text-danger">*</span></label>
                        <input type="text" id="nim" name="nim" class="form-control @error('nim') is-invalid @enderror"
                               value="{{ old('nim') ?? $student->nim }}" required placeholder="Masukkan NIM">
                        @error('nim')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="dosen_id" class="form-label">Dosen Pembimbing</label>
                        <select id="dosen_id" name="dosen_id" class="form-select @error('dosen_id') is-invalid @enderror">
                            <option value="">Pilih dosen pembimbing...</option>
                            @foreach($dosens as $dosen)
                                <option value="{{ $dosen->id }}"
                                    {{ (old('dosen_id') ?? $student->dosen_id) == $dosen->id ? 'selected' : '' }}>
                                    {{ $dosen->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('dosen_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="ti ti-briefcase"></i> Informasi Magang</h6>

                    <div class="mb-3">
                        <label for="company_id" class="form-label">Perusahaan Magang</label>
                        <select id="company_id" name="company_id" class="form-select @error('company_id') is-invalid @enderror">
                            <option value="">Pilih perusahaan...</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}"
                                    {{ (old('company_id') ?? $student->internship?->company_id) == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="ti ti-info-circle"></i> Opsional - tempat mahasiswa magang
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pembimbing_lapangan_id" class="form-label">Pembimbing Lapangan</label>
                        <select id="pembimbing_lapangan_id" name="pembimbing_lapangan_id" class="form-select @error('pembimbing_lapangan_id') is-invalid @enderror"
                                {{ !$student->internship?->company_id ? 'disabled' : '' }}>
                            <option value="">{{ !$student->internship?->company_id ? 'Pilih perusahaan terlebih dahulu...' : 'Pilih pembimbing lapangan...' }}</option>
                        </select>
                        @error('pembimbing_lapangan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="ti ti-info-circle"></i> Pilih perusahaan terlebih dahulu untuk melihat pembimbing lapangan
                        </div>
                    </div>

                    @php
                        $startDate = $student->internship?->start_date;
                        $endDate = $student->internship?->end_date;
                        $startMonth = $startDate ? $startDate->format('m') : null;
                        $startYear = $startDate ? $startDate->format('Y') : null;
                        $endMonth = $endDate ? $endDate->format('m') : null;
                        $endYear = $endDate ? $endDate->format('Y') : null;
                    @endphp

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_month" class="form-label">Bulan Mulai Magang</label>
                                <select id="start_month" name="start_month" class="form-select @error('start_month') is-invalid @enderror">
                                    <option value="">Pilih bulan...</option>
                                    @foreach(['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $month)
                                        <option value="{{ $num }}" {{ (old('start_month') ?? $startMonth) == $num ? 'selected' : '' }}>{{ $month }}</option>
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
                                        <option value="{{ $year }}" {{ (old('start_year') ?? $startYear) == $year ? 'selected' : '' }}>{{ $year }}</option>
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
                                        <option value="{{ $num }}" {{ (old('end_month') ?? $endMonth) == $num ? 'selected' : '' }}>{{ $month }}</option>
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
                                        <option value="{{ $year }}" {{ (old('end_year') ?? $endYear) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                                @error('end_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
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
    const companySelect = document.getElementById('company_id');
    const pembimbingSelect = document.getElementById('pembimbing_lapangan_id');
    const currentPembimbingId = {{ $student->pembimbing_lapangan_id ?? 'null' }};

    // Load initial data if company is already selected
    if (companySelect.value) {
        loadPembimbingLapangan(companySelect.value, currentPembimbingId);
    }

    companySelect.addEventListener('change', function() {
        const companyId = this.value;
        loadPembimbingLapangan(companyId);
    });

    function loadPembimbingLapangan(companyId, selectedId = null) {
        // Reset pembimbing select
        pembimbingSelect.innerHTML = '<option value="">Loading...</option>';
        pembimbingSelect.disabled = true;

        if (companyId) {
            // Fetch pembimbing lapangan for selected company
            fetch(`{{ url('admin/api/pembimbing-lapangan') }}/${companyId}`)
                .then(response => response.json())
                .then(data => {
                    pembimbingSelect.innerHTML = '<option value="">Pilih pembimbing lapangan...</option>';

                    if (data.length > 0) {
                        data.forEach(pembimbing => {
                            const option = document.createElement('option');
                            option.value = pembimbing.id;
                            option.textContent = pembimbing.name;
                            if (selectedId && pembimbing.id === selectedId) {
                                option.selected = true;
                            }
                            pembimbingSelect.appendChild(option);
                        });
                        pembimbingSelect.disabled = false;
                    } else {
                        pembimbingSelect.innerHTML = '<option value="">Tidak ada pembimbing lapangan untuk perusahaan ini</option>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    pembimbingSelect.innerHTML = '<option value="">Error loading data</option>';
                });
        } else {
            pembimbingSelect.innerHTML = '<option value="">Pilih perusahaan terlebih dahulu...</option>';
        }
    }
});
</script>
@endpush
