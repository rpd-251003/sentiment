@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Dashboard Mahasiswa</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page">Dashboard</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    @php
        $student = auth()->user()->student;
        $myEvaluations = $student ? $student->evaluations : collect([]);
        $selfEvaluation = $myEvaluations->where('evaluator_role', 'mahasiswa')->first();

        // Calculate sentiment distribution
        $sentiments = $myEvaluations->pluck('sentimentResult')->filter();
        $totalSentiments = $sentiments->count();

        $positiveCount = $sentiments->where('sentiment_label', 'positive')->count();
        $negativeCount = $sentiments->where('sentiment_label', 'negative')->count();
        $neutralCount = $sentiments->where('sentiment_label', 'neutral')->count();

        $positivePercent = $totalSentiments > 0 ? round(($positiveCount / $totalSentiments) * 100, 1) : 0;
        $negativePercent = $totalSentiments > 0 ? round(($negativeCount / $totalSentiments) * 100, 1) : 0;
        $neutralPercent = $totalSentiments > 0 ? round(($neutralCount / $totalSentiments) * 100, 1) : 0;
    @endphp

    <!-- Profile Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avtar avtar-xl">
                            <i class="ti ti-user" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h4 class="mb-1">{{ $student->name ?? auth()->user()->name }}</h4>
                        <p class="text-muted mb-1">NIM: {{ $student->nim ?? '-' }}</p>
                        <p class="text-muted mb-1">Dosen Pembimbing: {{ $student->dosen->name ?? '-' }}</p>
                        <p class="text-muted mb-0">Perusahaan: {{ $student->internship->company->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Evaluasi</h6>
                <h4 class="mb-3">{{ $myEvaluations->count() }} <span class="badge bg-light-primary border border-primary"><i class="ti ti-clipboard-check"></i></span></h4>
                <p class="mb-0 text-muted text-sm">Evaluasi yang Anda terima</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Self Evaluation</h6>
                <h4 class="mb-3">
                    @if($selfEvaluation)
                        <span class="badge bg-success"><i class="ti ti-check"></i> Sudah</span>
                    @else
                        <span class="badge bg-warning"><i class="ti ti-alert-circle"></i> Belum</span>
                    @endif
                </h4>
                <p class="mb-0 text-muted text-sm">Status evaluasi diri</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Sentimen Rata-rata</h6>
                <h4 class="mb-3">
                    @if($positivePercent > $negativePercent && $positivePercent > $neutralPercent)
                        <span class="badge bg-success"><i class="ti ti-mood-smile"></i> Positive</span>
                    @elseif($negativePercent > $positivePercent && $negativePercent > $neutralPercent)
                        <span class="badge bg-danger"><i class="ti ti-mood-sad"></i> Negative</span>
                    @else
                        <span class="badge bg-secondary"><i class="ti ti-mood-neutral"></i> Neutral</span>
                    @endif
                </h4>
                <p class="mb-0 text-muted text-sm">
                    <span class="text-success">{{ $positivePercent }}%</span> /
                    <span class="text-secondary">{{ $neutralPercent }}%</span> /
                    <span class="text-danger">{{ $negativePercent }}%</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Evaluations Table -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-list"></i> Riwayat Evaluasi</h5>
            </div>
            <div class="card-body">
                @if($myEvaluations->isEmpty())
                    <p class="text-muted text-center py-4">Belum ada evaluasi</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Evaluator</th>
                                    <th>Role</th>
                                    <th>Rating</th>
                                    <th>Sentimen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myEvaluations as $evaluation)
                                <tr>
                                    <td>{{ $evaluation->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $evaluation->evaluator->name }}</td>
                                    <td>
                                        @if($evaluation->evaluator_role === 'admin')
                                            <span class="badge bg-primary">Admin</span>
                                        @elseif($evaluation->evaluator_role === 'kaprodi')
                                            <span class="badge bg-success">Kaprodi</span>
                                        @elseif($evaluation->evaluator_role === 'dosen')
                                            <span class="badge bg-info">Dosen</span>
                                        @elseif($evaluation->evaluator_role === 'pembimbing_lapangan')
                                            <span class="badge bg-warning">Pembimbing Lapangan</span>
                                        @else
                                            <span class="badge bg-secondary">Mahasiswa</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($evaluation->rating)
                                            <span class="badge bg-light-primary">{{ $evaluation->rating }}/10</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($evaluation->sentimentResult)
                                            @if($evaluation->sentimentResult->sentiment_label === 'positive')
                                                <span class="badge bg-success">Positive</span>
                                            @elseif($evaluation->sentimentResult->sentiment_label === 'negative')
                                                <span class="badge bg-danger">Negative</span>
                                            @else
                                                <span class="badge bg-secondary">Neutral</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('evaluations.show', $evaluation->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-eye"></i> Lihat
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-bolt"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                @if(!$selfEvaluation)
                    <a href="{{ route('evaluations.create') }}?student_id={{ $student->id }}" class="btn btn-primary me-2">
                        <i class="ti ti-plus"></i> Buat Self Evaluation
                    </a>
                @else
                    <a href="{{ route('evaluations.show', $selfEvaluation->id) }}" class="btn btn-outline-primary me-2">
                        <i class="ti ti-eye"></i> Lihat Self Evaluation
                    </a>
                @endif
                <a href="{{ route('evaluations.index') }}" class="btn btn-outline-primary">
                    <i class="ti ti-list"></i> Lihat Semua Evaluasi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
