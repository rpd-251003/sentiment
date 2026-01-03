@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Dashboard</h5>
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
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Mahasiswa</h6>
                <h4 class="mb-3">{{ $stats['total_students'] }} <span class="badge bg-light-primary border border-primary"><i class="ti ti-school"></i></span></h4>
                <p class="mb-0 text-muted text-sm">Mahasiswa terdaftar dalam sistem</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Evaluasi</h6>
                <h4 class="mb-3">{{ $stats['total_evaluations'] }} <span class="badge bg-light-success border border-success"><i class="ti ti-clipboard-check"></i></span></h4>
                <p class="mb-0 text-muted text-sm">Evaluasi yang telah diinput</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Dosen Pembimbing</h6>
                <h4 class="mb-3">{{ $stats['total_dosen'] }} <span class="badge bg-light-info border border-info"><i class="ti ti-user-check"></i></span></h4>
                <p class="mb-0 text-muted text-sm">Dosen pembimbing aktif</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Pembimbing Lapangan</h6>
                <h4 class="mb-3">{{ $stats['total_pembimbing'] }} <span class="badge bg-light-warning border border-warning"><i class="ti ti-building"></i></span></h4>
                <p class="mb-0 text-muted text-sm">Pembimbing dari perusahaan</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-chart-bar"></i> Distribusi Sentimen</h5>
            </div>
            <div class="card-body">
                @if($sentimentDistribution->isEmpty())
                    <p class="text-muted text-center py-4">Belum ada data sentimen</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sentiment</th>
                                    <th>Jumlah</th>
                                    <th>Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = $sentimentDistribution->sum('count'); @endphp
                                @foreach($sentimentDistribution as $item)
                                <tr>
                                    <td>
                                        <span class="badge
                                            @if(str_contains(strtolower($item->sentiment_label), 'positive')) bg-success
                                            @elseif(str_contains(strtolower($item->sentiment_label), 'negative')) bg-danger
                                            @else bg-secondary @endif">
                                            {{ ucfirst($item->sentiment_label) }}
                                        </span>
                                    </td>
                                    <td>{{ $item->count }}</td>
                                    <td>{{ $total > 0 ? round(($item->count / $total) * 100, 1) : 0 }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-clock"></i> Evaluasi Terbaru</h5>
            </div>
            <div class="card-body">
                @if($recentEvaluations->isEmpty())
                    <p class="text-muted text-center py-4">Belum ada evaluasi</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($recentEvaluations->take(5) as $eval)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $eval->student->name }}</h6>
                                    <small class="text-muted">
                                        oleh {{ $eval->evaluator->name }}
                                        ({{ ucfirst(str_replace('_', ' ', $eval->evaluator_role)) }})
                                    </small>
                                </div>
                                <div>
                                    @if($eval->sentimentResult)
                                        <span class="badge
                                            @if(str_contains(strtolower($eval->sentimentResult->sentiment_label), 'positive')) bg-success
                                            @elseif(str_contains(strtolower($eval->sentimentResult->sentiment_label), 'negative')) bg-danger
                                            @else bg-secondary @endif">
                                            {{ ucfirst($eval->sentimentResult->sentiment_label) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-bolt"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('evaluations.create') }}" class="btn btn-primary me-2">
                    <i class="ti ti-plus"></i> Buat Evaluasi Baru
                </a>
                <a href="{{ route('evaluations.index') }}" class="btn btn-outline-primary">
                    <i class="ti ti-list"></i> Lihat Semua Evaluasi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
