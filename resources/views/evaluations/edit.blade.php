@extends('layouts.app')

@section('title', 'Edit Evaluasi')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Evaluasi</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('evaluations.index') }}">Evaluasi</a></li>
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
                <h5 class="mb-0"><i class="ti ti-edit"></i> Edit Form Evaluasi</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('evaluations.update', $evaluation) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="student_id" class="form-label">Mahasiswa <span class="text-danger">*</span></label>
                        <select id="student_id" name="student_id" class="form-select @error('student_id') is-invalid @enderror" required disabled>
                            <option value="{{ $evaluation->student->id }}" selected>
                                {{ $evaluation->student->name }} ({{ $evaluation->student->nim }})
                            </option>
                        </select>
                        <input type="hidden" name="student_id" value="{{ $evaluation->student->id }}">
                        @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="ti ti-info-circle"></i> Mahasiswa tidak dapat diubah setelah evaluasi dibuat
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating (1-10)</label>
                        <select id="rating" name="rating" class="form-select @error('rating') is-invalid @enderror">
                            <option value="">Tidak ada rating</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}"
                                    {{ (old('rating') ?? $evaluation->rating) == $i ? 'selected' : '' }}>
                                    {{ $i }} - {{ $i <= 3 ? 'Kurang' : ($i <= 6 ? 'Cukup' : ($i <= 8 ? 'Baik' : 'Sangat Baik')) }}
                                </option>
                            @endfor
                        </select>
                        @error('rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="ti ti-info-circle"></i> Rating bersifat opsional
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comment_text" class="form-label">Komentar Evaluasi <span class="text-danger">*</span></label>
                        <textarea id="comment_text" name="comment_text" rows="6"
                                  class="form-control @error('comment_text') is-invalid @enderror"
                                  placeholder="Masukkan komentar evaluasi Anda di sini..." required>{{ old('comment_text') ?? $evaluation->comment_text }}</textarea>
                        <div class="form-text">
                            <i class="ti ti-bulb"></i> Komentar akan dianalisis ulang secara otomatis jika diubah
                        </div>
                        @error('comment_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    @if($evaluation->sentimentResult)
                    <div class="alert alert-warning" role="alert">
                        <i class="ti ti-alert-triangle"></i>
                        <strong>Perhatian:</strong> Jika Anda mengubah komentar, sistem akan melakukan analisis sentimen ulang secara otomatis. Hasil sentimen sebelumnya akan digantikan.
                    </div>
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="mb-2">Hasil Sentimen Saat Ini:</h6>
                            @if($evaluation->sentimentResult->sentiment_label === 'positive')
                                <span class="badge bg-success">
                                    <i class="ti ti-mood-smile"></i> Positive
                                </span>
                            @elseif($evaluation->sentimentResult->sentiment_label === 'negative')
                                <span class="badge bg-danger">
                                    <i class="ti ti-mood-sad"></i> Negative
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="ti ti-mood-neutral"></i> Neutral
                                </span>
                            @endif
                            <small class="text-muted ms-2">
                                (Score: {{ number_format($evaluation->sentimentResult->sentiment_score * 100, 2) }}%)
                            </small>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('evaluations.show', $evaluation) }}" class="btn btn-outline-secondary">
                            <i class="ti ti-x"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
