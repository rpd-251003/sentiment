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
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Rating</h6>
                        @if($evaluation->rating)
                            <div class="mb-2">
                                <span class="badge bg-light-primary border border-primary" style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                                    {{ $evaluation->rating }}/10
                                </span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                     style="width: {{ ($evaluation->rating / 10) * 100 }}%"
                                     aria-valuenow="{{ $evaluation->rating }}" aria-valuemin="0" aria-valuemax="10">
                                </div>
                            </div>
                        @else
                            <p class="text-muted mb-0">Tidak ada rating</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Analisis Sentimen</h6>
                        @if($evaluation->sentimentResult)
                            <div>
                                @if($evaluation->sentimentResult->sentiment_label === 'positive')
                                    <span class="badge bg-success" style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                                        <i class="ti ti-mood-smile"></i> Positive
                                    </span>
                                @elseif($evaluation->sentimentResult->sentiment_label === 'negative')
                                    <span class="badge bg-danger" style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                                        <i class="ti ti-mood-sad"></i> Negative
                                    </span>
                                @else
                                    <span class="badge bg-secondary" style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                                        <i class="ti ti-mood-neutral"></i> Neutral
                                    </span>
                                @endif
                                <br>
                                <small class="text-muted mt-2 d-block">
                                    Confidence Score: {{ number_format($evaluation->sentimentResult->sentiment_score * 100, 2) }}%
                                </small>
                            </div>
                        @else
                            <p class="text-muted mb-0">Analisis sentimen tidak tersedia</p>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <h6 class="text-muted mb-3">Komentar Evaluasi</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-0" style="white-space: pre-wrap; line-height: 1.8;">{{ $evaluation->comment_text }}</p>
                        </div>
                    </div>
                </div>

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
