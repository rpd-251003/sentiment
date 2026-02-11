@extends('layouts.app')

@section('title', 'Buat Evaluasi')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Buat Evaluasi Baru</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('evaluations.index') }}">Evaluasi</a></li>
                    <li class="breadcrumb-item" aria-current="page">Buat Baru</li>
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
                <h5 class="mb-0"><i class="ti ti-plus"></i> Form Evaluasi</h5>
            </div>
            <div class="card-body">
                <form id="evaluationForm" method="POST" action="{{ route('evaluations.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="student_id" class="form-label">Mahasiswa <span class="text-danger">*</span></label>
                        <select id="student_id" name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                            <option value="">Pilih mahasiswa...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}"
                                    {{ (old('student_id') ?? $selectedStudentId ?? null) == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} ({{ $student->nim }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="ti ti-info-circle"></i> Pilih mahasiswa yang akan dievaluasi
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="rating" class="form-label">
                            Rating (1-100)
                            <span class="badge bg-secondary ms-2" id="ratingBadge">0 - Tidak ada rating</span>
                        </label>
                        <input type="hidden" name="rating" id="ratingInput" value="{{ old('rating', '') }}">

                        <div class="rating-slider-container">
                            <!-- Slider -->
                            <input type="range"
                                   class="form-range @error('rating') is-invalid @enderror"
                                   id="ratingSlider"
                                   min="0"
                                   max="100"
                                   step="1"
                                   value="{{ old('rating', 0) }}"
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
                                  placeholder="Masukkan komentar penilaian kinerja mahasiswa..." required>{{ old('comment_nilai') }}</textarea>
                        <div class="form-text">
                            <i class="ti ti-bulb"></i> Komentar penilaian kinerja dan hasil kerja mahasiswa
                        </div>
                        @error('comment_nilai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    <div class="mb-3">
                        <label for="comment_masukan" class="form-label">Komentar Masukan <span class="text-danger">*</span></label>
                        <textarea id="comment_masukan" name="comment_masukan" rows="4"
                                  class="form-control @error('comment_masukan') is-invalid @enderror"
                                  placeholder="Masukkan saran dan masukan untuk mahasiswa..." required>{{ old('comment_masukan') }}</textarea>
                        <div class="form-text">
                            <i class="ti ti-bulb"></i> Saran dan masukan untuk pengembangan mahasiswa
                        </div>
                        @error('comment_masukan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    <div class="alert alert-info" role="alert">
                        <i class="ti ti-info-circle"></i>
                        <strong>Catatan:</strong> Sistem akan melakukan analisis sentimen secara otomatis untuk <strong>KEDUA</strong> komentar (Nilai dan Masukan). Anda akan melihat hasil sentimen lengkap dengan skor positive, negative, dan neutral untuk setiap komentar.
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="ti ti-send"></i> Simpan Evaluasi
                        </button>
                        <a href="{{ route('evaluations.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-x"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Progress Modal -->
<div class="modal fade" id="progressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="ti ti-loader"></i> Memproses Evaluasi
                </h5>
            </div>
            <div class="modal-body">
                <!-- Step 1: Validating -->
                <div class="progress-step mb-3" id="step1">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm text-primary me-3" role="status" id="spinner1" style="display: none;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <i class="ti ti-circle me-3 text-muted" id="icon1"></i>
                        <div class="flex-grow-1">
                            <strong id="text1">Memvalidasi form...</strong>
                            <div class="text-muted small" id="desc1">Memeriksa data yang diinput</div>
                        </div>
                        <i class="ti ti-check text-success" id="check1" style="display: none; font-size: 1.5rem;"></i>
                    </div>
                </div>

                <!-- Step 2: Creating Evaluation -->
                <div class="progress-step mb-3" id="step2">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm text-primary me-3" role="status" id="spinner2" style="display: none;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <i class="ti ti-circle me-3 text-muted" id="icon2"></i>
                        <div class="flex-grow-1">
                            <strong id="text2">Menyimpan evaluasi...</strong>
                            <div class="text-muted small" id="desc2">Membuat record evaluasi di database</div>
                        </div>
                        <i class="ti ti-check text-success" id="check2" style="display: none; font-size: 1.5rem;"></i>
                    </div>
                </div>

                <!-- Step 3: Analyzing Sentiment -->
                <div class="progress-step mb-3" id="step3">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm text-primary me-3" role="status" id="spinner3" style="display: none;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <i class="ti ti-circle me-3 text-muted" id="icon3"></i>
                        <div class="flex-grow-1">
                            <strong id="text3">Analisis sentimen...</strong>
                            <div class="text-muted small" id="desc3">Menganalisis 2 komentar via Hugging Face API</div>
                        </div>
                        <i class="ti ti-check text-success" id="check3" style="display: none; font-size: 1.5rem;"></i>
                    </div>
                </div>

                <!-- Step 4: Saving Result -->
                <div class="progress-step mb-3" id="step4">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm text-primary me-3" role="status" id="spinner4" style="display: none;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <i class="ti ti-circle me-3 text-muted" id="icon4"></i>
                        <div class="flex-grow-1">
                            <strong id="text4">Menyimpan hasil...</strong>
                            <div class="text-muted small" id="desc4">Menyimpan 2 hasil analisis sentimen</div>
                        </div>
                        <i class="ti ti-check text-success" id="check4" style="display: none; font-size: 1.5rem;"></i>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="progress mt-4" style="height: 8px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar"
                         role="progressbar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('evaluationForm');
    const submitBtn = document.getElementById('submitBtn');
    const progressModal = new bootstrap.Modal(document.getElementById('progressModal'));

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

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Reset all steps
        resetProgress();

        // Show modal
        progressModal.show();

        // Disable submit button
        submitBtn.disabled = true;

        // Start processing
        processEvaluation();
    });

    function resetProgress() {
        for(let i = 1; i <= 4; i++) {
            document.getElementById('spinner' + i).style.display = 'none';
            document.getElementById('icon' + i).style.display = 'inline';
            document.getElementById('check' + i).style.display = 'none';
        }
        document.getElementById('progressBar').style.width = '0%';
    }

    function updateStep(step, status = 'loading') {
        const spinner = document.getElementById('spinner' + step);
        const icon = document.getElementById('icon' + step);
        const check = document.getElementById('check' + step);

        if(status === 'loading') {
            spinner.style.display = 'inline-block';
            icon.style.display = 'none';
            check.style.display = 'none';
        } else if(status === 'completed') {
            spinner.style.display = 'none';
            icon.style.display = 'none';
            check.style.display = 'inline';
        }

        // Update progress bar
        const progress = (step / 4) * 100;
        document.getElementById('progressBar').style.width = progress + '%';
    }

    async function processEvaluation() {
        try {
            // Step 1: Validating
            updateStep(1, 'loading');
            await sleep(500);

            // Validate form
            if(!form.checkValidity()) {
                throw new Error('Form validasi gagal');
            }

            updateStep(1, 'completed');
            await sleep(300);

            // Step 2, 3, 4: Submit via AJAX
            updateStep(2, 'loading');

            const formData = new FormData(form);

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            updateStep(2, 'completed');
            await sleep(300);

            updateStep(3, 'loading');
            await sleep(800); // Simulate API call time
            updateStep(3, 'completed');
            await sleep(300);

            updateStep(4, 'loading');

            const result = await response.json();

            if(!response.ok) {
                throw new Error(result.message || 'Terjadi kesalahan');
            }

            updateStep(4, 'completed');
            await sleep(500);

            // Success - redirect
            window.location.href = result.redirect_url;

        } catch(error) {
            progressModal.hide();
            submitBtn.disabled = false;

            alert('Error: ' + error.message);
        }
    }

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
});
</script>
@endpush
