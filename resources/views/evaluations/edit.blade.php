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
                        <label for="rating" class="form-label">
                            Rating (1-100)
                            <span class="badge bg-secondary ms-2" id="ratingBadge">0 - Tidak ada rating</span>
                        </label>
                        <input type="hidden" name="rating" id="ratingInput" value="{{ old('rating', $evaluation->rating ?? '') }}">

                        <div class="rating-slider-container">
                            <!-- Slider -->
                            <input type="range"
                                   class="form-range @error('rating') is-invalid @enderror"
                                   id="ratingSlider"
                                   min="0"
                                   max="100"
                                   step="1"
                                   value="{{ old('rating', $evaluation->rating ?? 0) }}"
                                   style="height: 8px;">

                            <!-- Value Display -->
                            <div class="d-flex justify-content-between mt-2 text-muted small">
                                <span>0</span>
                                <span>25</span>
                                <span>50</span>
                                <span>75</span>
                                <span>100</span>
                            </div>

                            <!-- Visual Indicator -->
                            <div class="mt-3 p-3 rounded text-center" id="ratingIndicator" style="background: #f8f9fa; border: 2px dashed #dee2e6;">
                                <div class="rating-emoji mb-2" id="ratingEmoji" style="font-size: 3rem;">➖</div>
                                <div class="rating-label fw-bold" id="ratingLabel" style="font-size: 1.1rem; color: #6c757d;">Tidak ada rating</div>
                                <div class="rating-description text-muted small mt-1" id="ratingDescription">Geser slider untuk memberikan rating</div>
                            </div>
                        </div>

                        @error('rating')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="ti ti-info-circle"></i> Rating bersifat opsional. Set ke 0 jika tidak ingin memberikan rating.
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Rating Slider Handler
    const ratingSlider = document.getElementById('ratingSlider');
    const ratingInput = document.getElementById('ratingInput');
    const ratingBadge = document.getElementById('ratingBadge');
    const ratingIndicator = document.getElementById('ratingIndicator');
    const ratingEmoji = document.getElementById('ratingEmoji');
    const ratingLabel = document.getElementById('ratingLabel');
    const ratingDescription = document.getElementById('ratingDescription');

    function updateRatingDisplay(value) {
        const numValue = parseInt(value);

        // Update hidden input
        ratingInput.value = numValue === 0 ? '' : numValue;

        // Update badge
        if(numValue === 0) {
            ratingBadge.textContent = '0 - Tidak ada rating';
            ratingBadge.className = 'badge bg-secondary ms-2';
        } else {
            ratingBadge.textContent = numValue;
            if(numValue <= 25) {
                ratingBadge.className = 'badge bg-danger ms-2';
            } else if(numValue <= 50) {
                ratingBadge.className = 'badge bg-warning ms-2';
            } else if(numValue <= 75) {
                ratingBadge.className = 'badge bg-info ms-2';
            } else {
                ratingBadge.className = 'badge bg-success ms-2';
            }
        }

        // Update visual indicator
        let emoji, label, description, bgColor, borderColor, textColor;

        if(numValue === 0) {
            emoji = '➖';
            label = 'Tidak ada rating';
            description = 'Geser slider untuk memberikan rating';
            bgColor = '#f8f9fa';
            borderColor = '#dee2e6';
            textColor = '#6c757d';
        } else if(numValue <= 25) {
            emoji = '😞';
            label = 'Sangat Kurang';
            description = 'Kinerja sangat di bawah ekspektasi';
            bgColor = '#ffe5e5';
            borderColor = '#dc3545';
            textColor = '#dc3545';
        } else if(numValue <= 50) {
            emoji = '😐';
            label = 'Kurang';
            description = 'Kinerja di bawah ekspektasi, perlu banyak perbaikan';
            bgColor = '#fff3cd';
            borderColor = '#ffc107';
            textColor = '#856404';
        } else if(numValue <= 75) {
            emoji = '🙂';
            label = 'Baik';
            description = 'Kinerja sesuai ekspektasi';
            bgColor = '#d1ecf1';
            borderColor = '#0dcaf0';
            textColor = '#055160';
        } else if(numValue <= 90) {
            emoji = '😊';
            label = 'Sangat Baik';
            description = 'Kinerja melebihi ekspektasi';
            bgColor = '#d1f0dd';
            borderColor = '#198754';
            textColor = '#0f5132';
        } else {
            emoji = '🤩';
            label = 'Luar Biasa';
            description = 'Kinerja sangat luar biasa, exceptional!';
            bgColor = '#d1f0dd';
            borderColor = '#198754';
            textColor = '#0f5132';
        }

        ratingEmoji.textContent = emoji;
        ratingLabel.textContent = label;
        ratingLabel.style.color = textColor;
        ratingDescription.textContent = description;
        ratingIndicator.style.background = bgColor;
        ratingIndicator.style.borderColor = borderColor;
        ratingIndicator.style.borderStyle = 'solid';

        // Animate slider color
        const percentage = numValue / 100;
        const hue = percentage * 120; // 0 (red) to 120 (green)
        ratingSlider.style.accentColor = numValue === 0 ? '#6c757d' : `hsl(${hue}, 70%, 50%)`;
    }

    // Initialize display
    updateRatingDisplay(ratingSlider.value);

    // Listen to slider changes
    ratingSlider.addEventListener('input', function() {
        updateRatingDisplay(this.value);
    });
});
</script>
@endpush
