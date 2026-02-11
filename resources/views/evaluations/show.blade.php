@extends('layouts.app')

@section('title', 'Detail Evaluasi')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Detail Evaluasi</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('evaluations.index') }}">Evaluasi</a></li>
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
                <h5 class="mb-0"><i class="ti ti-file-text"></i> Informasi Evaluasi</h5>
                <div>
                    @can('update', $evaluation)
                    <a href="{{ route('evaluations.edit', $evaluation) }}" class="btn btn-sm btn-primary">
                        <i class="ti ti-edit"></i> Edit
                    </a>
                    @endcan
                    @can('delete', $evaluation)
                    <form action="{{ route('evaluations.destroy', $evaluation) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus evaluasi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="ti ti-trash"></i> Hapus
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6 class="text-muted mb-2">Mahasiswa</h6>
                        <p class="mb-0">
                            <strong>{{ $evaluation->student->name }}</strong><br>
                            <small class="text-muted">NIM: {{ $evaluation->student->nim }}</small>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-2">Evaluator</h6>
                        <p class="mb-0">
                            <strong>{{ $evaluation->evaluator->name }}</strong><br>
                            @if($evaluation->evaluator_role === 'admin')
                                <span class="badge bg-primary">Admin</span>
                            @elseif($evaluation->evaluator_role === 'kaprodi')
                                <span class="badge bg-success">Kaprodi</span>
                            @elseif($evaluation->evaluator_role === 'dosen')
                                <span class="badge bg-info">Dosen Pembimbing</span>
                            @elseif($evaluation->evaluator_role === 'pembimbing_lapangan')
                                <span class="badge bg-warning">Pembimbing Lapangan</span>
                            @else
                                <span class="badge bg-secondary">Mahasiswa</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-2">Tanggal</h6>
                        <p class="mb-0">
                            {{ $evaluation->created_at->format('d F Y') }}<br>
                            <small class="text-muted">{{ $evaluation->created_at->format('H:i') }} WIB</small>
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-2">Rating</h6>
                        @if($evaluation->rating)
                            <div class="mb-3">
                                @php
                                    $ratingValue = $evaluation->rating;
                                    $badgeClass = '';
                                    $emoji = '';
                                    $label = '';

                                    if ($ratingValue <= 25) {
                                        $badgeClass = 'bg-danger';
                                        $emoji = '😞';
                                        $label = 'Sangat Kurang';
                                    } elseif ($ratingValue <= 50) {
                                        $badgeClass = 'bg-warning';
                                        $emoji = '😐';
                                        $label = 'Kurang';
                                    } elseif ($ratingValue <= 75) {
                                        $badgeClass = 'bg-info';
                                        $emoji = '🙂';
                                        $label = 'Baik';
                                    } elseif ($ratingValue <= 90) {
                                        $badgeClass = 'bg-success';
                                        $emoji = '😊';
                                        $label = 'Sangat Baik';
                                    } else {
                                        $badgeClass = 'bg-success';
                                        $emoji = '🤩';
                                        $label = 'Luar Biasa';
                                    }
                                @endphp
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge {{ $badgeClass }}" style="font-size: 1.5rem; padding: 0.6rem 1.2rem;">
                                        {{ $emoji }} {{ $evaluation->rating }}/100
                                    </span>
                                    <span class="text-muted">{{ $label }}</span>
                                </div>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar {{ $badgeClass }}" role="progressbar"
                                     style="width: {{ $evaluation->rating }}%"
                                     aria-valuenow="{{ $evaluation->rating }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        @else
                            <p class="text-muted mb-0">➖ Tidak ada rating</p>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row mb-4">
                    <!-- Komentar Nilai -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3"><i class="ti ti-star"></i> Komentar Nilai</h6>
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <p class="mb-0" style="white-space: pre-wrap; line-height: 1.8;">{{ $evaluation->comment_nilai }}</p>
                            </div>
                        </div>

                        @php
                            $resultNilai = $evaluation->sentimentResults->where('comment_type', 'nilai')->first();
                        @endphp

                        @if($resultNilai)
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-gradient-primary text-white">
                                <strong><i class="ti ti-chart-bar"></i> Analisis Sentimen Nilai</strong>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    @if($resultNilai->sentiment_label === 'positive')
                                        <span class="badge bg-success" style="font-size: 1.1rem; padding: 0.4rem 0.8rem;">
                                            <i class="ti ti-mood-smile"></i> Positive
                                        </span>
                                    @elseif($resultNilai->sentiment_label === 'negative')
                                        <span class="badge bg-danger" style="font-size: 1.1rem; padding: 0.4rem 0.8rem;">
                                            <i class="ti ti-mood-sad"></i> Negative
                                        </span>
                                    @else
                                        <span class="badge bg-secondary" style="font-size: 1.1rem; padding: 0.4rem 0.8rem;">
                                            <i class="ti ti-mood-neutral"></i> Neutral
                                        </span>
                                    @endif
                                </div>
                                <div class="small">
                                    <div class="mb-2">
                                        <i class="ti ti-mood-smile text-success"></i> <strong>Positive:</strong>
                                        <span class="float-end">{{ number_format($resultNilai->positive_score * 100, 2) }}%</span>
                                        <div class="progress mt-1" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: {{ $resultNilai->positive_score * 100 }}%"></div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <i class="ti ti-mood-neutral text-secondary"></i> <strong>Neutral:</strong>
                                        <span class="float-end">{{ number_format($resultNilai->neutral_score * 100, 2) }}%</span>
                                        <div class="progress mt-1" style="height: 6px;">
                                            <div class="progress-bar bg-secondary" style="width: {{ $resultNilai->neutral_score * 100 }}%"></div>
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <i class="ti ti-mood-sad text-danger"></i> <strong>Negative:</strong>
                                        <span class="float-end">{{ number_format($resultNilai->negative_score * 100, 2) }}%</span>
                                        <div class="progress mt-1" style="height: 6px;">
                                            <div class="progress-bar bg-danger" style="width: {{ $resultNilai->negative_score * 100 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Komentar Masukan -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3"><i class="ti ti-message-circle"></i> Komentar Masukan</h6>
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <p class="mb-0" style="white-space: pre-wrap; line-height: 1.8;">{{ $evaluation->comment_masukan }}</p>
                            </div>
                        </div>

                        @php
                            $resultMasukan = $evaluation->sentimentResults->where('comment_type', 'masukan')->first();
                        @endphp

                        @if($resultMasukan)
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-gradient-primary text-white">
                                <strong><i class="ti ti-chart-bar"></i> Analisis Sentimen Masukan</strong>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    @if($resultMasukan->sentiment_label === 'positive')
                                        <span class="badge bg-success" style="font-size: 1.1rem; padding: 0.4rem 0.8rem;">
                                            <i class="ti ti-mood-smile"></i> Positive
                                        </span>
                                    @elseif($resultMasukan->sentiment_label === 'negative')
                                        <span class="badge bg-danger" style="font-size: 1.1rem; padding: 0.4rem 0.8rem;">
                                            <i class="ti ti-mood-sad"></i> Negative
                                        </span>
                                    @else
                                        <span class="badge bg-secondary" style="font-size: 1.1rem; padding: 0.4rem 0.8rem;">
                                            <i class="ti ti-mood-neutral"></i> Neutral
                                        </span>
                                    @endif
                                </div>
                                <div class="small">
                                    <div class="mb-2">
                                        <i class="ti ti-mood-smile text-success"></i> <strong>Positive:</strong>
                                        <span class="float-end">{{ number_format($resultMasukan->positive_score * 100, 2) }}%</span>
                                        <div class="progress mt-1" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: {{ $resultMasukan->positive_score * 100 }}%"></div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <i class="ti ti-mood-neutral text-secondary"></i> <strong>Neutral:</strong>
                                        <span class="float-end">{{ number_format($resultMasukan->neutral_score * 100, 2) }}%</span>
                                        <div class="progress mt-1" style="height: 6px;">
                                            <div class="progress-bar bg-secondary" style="width: {{ $resultMasukan->neutral_score * 100 }}%"></div>
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <i class="ti ti-mood-sad text-danger"></i> <strong>Negative:</strong>
                                        <span class="float-end">{{ number_format($resultMasukan->negative_score * 100, 2) }}%</span>
                                        <div class="progress mt-1" style="height: 6px;">
                                            <div class="progress-bar bg-danger" style="width: {{ $resultMasukan->negative_score * 100 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <hr>

                @if($evaluation->updated_at != $evaluation->created_at)
                <div class="alert alert-info" role="alert">
                    <i class="ti ti-info-circle"></i>
                    Terakhir diupdate: {{ $evaluation->updated_at->format('d F Y H:i') }} WIB
                </div>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('evaluations.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
