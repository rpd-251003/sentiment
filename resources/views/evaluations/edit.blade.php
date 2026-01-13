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
                        <label for="comment_nilai" class="form-label">Komentar Nilai <span class="text-danger">*</span></label>
                        <textarea id="comment_nilai" name="comment_nilai" rows="4"
                                  class="form-control @error('comment_nilai') is-invalid @enderror"
                                  placeholder="Masukkan komentar penilaian kinerja mahasiswa..." required>{{ old('comment_nilai') ?? $evaluation->comment_nilai }}</textarea>
                        <div class="form-text">
                            <i class="ti ti-bulb"></i> Komentar penilaian akan dianalisis ulang jika diubah
                        </div>
                        @error('comment_nilai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    <div class="mb-3">
                        <label for="comment_masukan" class="form-label">Komentar Masukan <span class="text-danger">*</span></label>
                        <textarea id="comment_masukan" name="comment_masukan" rows="4"
                                  class="form-control @error('comment_masukan') is-invalid @enderror"
                                  placeholder="Masukkan saran dan masukan untuk mahasiswa..." required>{{ old('comment_masukan') ?? $evaluation->comment_masukan }}</textarea>
                        <div class="form-text">
                            <i class="ti ti-bulb"></i> Komentar masukan akan dianalisis ulang jika diubah
                        </div>
                        @error('comment_masukan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    @if($evaluation->sentimentResults->count() > 0)
                    <div class="alert alert-warning" role="alert">
                        <i class="ti ti-alert-triangle"></i>
                        <strong>Perhatian:</strong> Jika Anda mengubah komentar, sistem akan melakukan analisis sentimen ulang secara otomatis untuk KEDUA komentar. Hasil sentimen sebelumnya akan digantikan.
                    </div>
                    <div class="row">
                        @foreach($evaluation->sentimentResults as $result)
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="mb-2">Sentimen {{ ucfirst($result->comment_type) }}:</h6>
                                    @if($result->sentiment_label === 'positive')
                                        <span class="badge bg-success">
                                            <i class="ti ti-mood-smile"></i> Positive
                                        </span>
                                    @elseif($result->sentiment_label === 'negative')
                                        <span class="badge bg-danger">
                                            <i class="ti ti-mood-sad"></i> Negative
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="ti ti-mood-neutral"></i> Neutral
                                        </span>
                                    @endif
                                    <div class="mt-2 small">
                                        <div><i class="ti ti-mood-smile text-success"></i> Positive: {{ number_format($result->positive_score * 100, 2) }}%</div>
                                        <div><i class="ti ti-mood-neutral text-secondary"></i> Neutral: {{ number_format($result->neutral_score * 100, 2) }}%</div>
                                        <div><i class="ti ti-mood-sad text-danger"></i> Negative: {{ number_format($result->negative_score * 100, 2) }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
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
