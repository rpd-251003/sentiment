@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa - ' . $student->name)

@php
    $evaluations = $student->evaluations;
    $totalEvaluations = $evaluations->count();

    // Sentiment Statistics
    $positiveCount = $evaluations->filter(fn($e) => $e->sentimentResult?->sentiment_label === 'positive')->count();
    $neutralCount = $evaluations->filter(fn($e) => $e->sentimentResult?->sentiment_label === 'neutral')->count();
    $negativeCount = $evaluations->filter(fn($e) => $e->sentimentResult?->sentiment_label === 'negative')->count();

    // Percentages
    $positivePercent = $totalEvaluations > 0 ? round(($positiveCount / $totalEvaluations) * 100, 1) : 0;
    $neutralPercent = $totalEvaluations > 0 ? round(($neutralCount / $totalEvaluations) * 100, 1) : 0;
    $negativePercent = $totalEvaluations > 0 ? round(($negativeCount / $totalEvaluations) * 100, 1) : 0;

    // Average Rating
    $avgRating = $evaluations->where('rating', '!=', null)->avg('rating');
    $avgRating = $avgRating ? round($avgRating, 1) : 0;

    // Evaluations by role
    $evalByRole = $evaluations->groupBy('evaluator_role');
@endphp

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
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Mahasiswa</a></li>
                    <li class="breadcrumb-item" aria-current="page">{{ $student->name }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- Student Profile Header -->
<div class="row">
    <div class="col-12">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avatar avatar-xl bg-white text-primary" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 2rem; font-weight: bold;">
                            {{ strtoupper(substr($student->name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="col">
                        <h3 class="text-white mb-1">{{ $student->name }}</h3>
                        <p class="mb-1 opacity-75"><i class="ti ti-id"></i> NIM: {{ $student->nim }}</p>
                        <p class="mb-0 opacity-75"><i class="ti ti-mail"></i> {{ $student->user->email ?? '-' }}</p>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-light btn-sm me-2">
                            <i class="ti ti-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="ti ti-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <!-- Total Evaluations -->
    <div class="col-md-3 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-primary">
                            <i class="ti ti-file-text f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Evaluasi</h6>
                        <h4 class="mb-0">{{ $totalEvaluations }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Rating -->
    <div class="col-md-3 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-warning">
                            <i class="ti ti-star f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Rata-rata Rating</h6>
                        <h4 class="mb-0">{{ $avgRating }}/10</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Positive Sentiment -->
    <div class="col-md-3 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-success">
                            <i class="ti ti-mood-smile f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Positif</h6>
                        <h4 class="mb-0">{{ $positiveCount }} <small class="text-muted">({{ $positivePercent }}%)</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Negative Sentiment -->
    <div class="col-md-3 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-danger">
                            <i class="ti ti-mood-sad f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Negatif</h6>
                        <h4 class="mb-0">{{ $negativeCount }} <small class="text-muted">({{ $negativePercent }}%)</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Sentiment Distribution Pie Chart -->
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="ti ti-chart-pie"></i> Distribusi Sentimen</h5>
            </div>
            <div class="card-body">
                <div id="sentimentPieChart"></div>
                @if($totalEvaluations == 0)
                <div class="text-center text-muted py-4">
                    <i class="ti ti-chart-pie" style="font-size: 3rem;"></i>
                    <p class="mt-2">Belum ada data evaluasi</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Rating Gauge Chart -->
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="ti ti-gauge"></i> Performance Score</h5>
            </div>
            <div class="card-body">
                <div id="ratingGaugeChart"></div>
                @if($totalEvaluations == 0)
                <div class="text-center text-muted py-4">
                    <i class="ti ti-gauge" style="font-size: 3rem;"></i>
                    <p class="mt-2">Belum ada data evaluasi</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Evaluations by Role -->
    <div class="col-lg-4 col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="ti ti-users"></i> Evaluasi Berdasarkan Role</h5>
            </div>
            <div class="card-body">
                @if($totalEvaluations > 0)
                <ul class="list-group list-group-flush">
                    @foreach(['admin' => 'Admin', 'dosen' => 'Dosen Pembimbing', 'pembimbing_lapangan' => 'Pembimbing Lapangan', 'mahasiswa' => 'Self Evaluation'] as $role => $label)
                        @php
                            $count = $evalByRole->get($role)?->count() ?? 0;
                            $percent = $totalEvaluations > 0 ? round(($count / $totalEvaluations) * 100) : 0;
                        @endphp
                        <li class="list-group-item px-0">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $label }}</span>
                                <span class="fw-bold">{{ $count }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar
                                    @if($role === 'admin') bg-primary
                                    @elseif($role === 'kaprodi') bg-success
                                    @elseif($role === 'dosen') bg-info
                                    @elseif($role === 'pembimbing_lapangan') bg-warning
                                    @else bg-secondary
                                    @endif"
                                    role="progressbar"
                                    style="width: {{ $percent }}%;"
                                    aria-valuenow="{{ $percent }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                @else
                <div class="text-center text-muted py-4">
                    <i class="ti ti-users" style="font-size: 3rem;"></i>
                    <p class="mt-2">Belum ada data evaluasi</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Student Info & Internship -->
<div class="row">
    <!-- Student Information -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="ti ti-user"></i> Informasi Mahasiswa</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <p class="text-muted mb-1">NIM</p>
                        <h6 class="mb-0">{{ $student->nim }}</h6>
                    </div>
                    <div class="col-6">
                        <p class="text-muted mb-1">Nama Lengkap</p>
                        <h6 class="mb-0">{{ $student->name }}</h6>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <p class="text-muted mb-1">Email</p>
                        <h6 class="mb-0">{{ $student->user->email ?? '-' }}</h6>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <p class="text-muted mb-1">Dosen Pembimbing</p>
                        @if($student->dosen)
                            <span class="badge bg-info">{{ $student->dosen->name }}</span>
                        @else
                            <span class="text-muted">Belum ditentukan</span>
                        @endif
                    </div>
                    <div class="col-6">
                        <p class="text-muted mb-1">Pembimbing Lapangan</p>
                        @if($student->pembimbingLapangan)
                            <span class="badge bg-warning">{{ $student->pembimbingLapangan->name }}</span>
                        @else
                            <span class="text-muted">Belum ditentukan</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Internship Information -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="ti ti-briefcase"></i> Informasi Magang</h5>
            </div>
            <div class="card-body">
                @if($student->internship)
                <div class="row mb-3">
                    <div class="col-12">
                        <p class="text-muted mb-1">Perusahaan</p>
                        <h6 class="mb-0">{{ $student->internship->company->name }}</h6>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <p class="text-muted mb-1">Periode Magang</p>
                        <h6 class="mb-0">
                            {{ $student->internship->start_date ? $student->internship->start_date->format('d M Y') : '-' }}
                            -
                            {{ $student->internship->end_date ? $student->internship->end_date->format('d M Y') : '-' }}
                        </h6>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <p class="text-muted mb-1">Pembimbing Lapangan</p>
                        <h6 class="mb-0">{{ $student->internship->pembimbingLapangan->name ?? '-' }}</h6>
                    </div>
                </div>
                @else
                <div class="alert alert-warning" role="alert">
                    <i class="ti ti-alert-triangle"></i> Mahasiswa ini belum memiliki data magang
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Evaluation History -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="ti ti-history"></i> Riwayat Evaluasi ({{ $totalEvaluations }})</h5>
            </div>
            <div class="card-body">
                @if($evaluations->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="ti ti-file-text" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Belum ada evaluasi</h5>
                    <p>Evaluasi akan muncul di sini setelah ditambahkan</p>
                </div>
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
                                <th>Score</th>
                                <th>Komentar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluations->sortByDesc('created_at') as $evaluation)
                            <tr>
                                <td>
                                    <small>{{ $evaluation->created_at->format('d M Y') }}</small><br>
                                    <small class="text-muted">{{ $evaluation->created_at->format('H:i') }}</small>
                                </td>
                                <td>{{ $evaluation->evaluator->name }}</td>
                                <td>
                                    @if($evaluation->evaluator_role === 'admin')
                                        <span class="badge bg-primary">Admin</span>
                                    @elseif($evaluation->evaluator_role === 'kaprodi')
                                        <span class="badge bg-success">Kaprodi</span>
                                    @elseif($evaluation->evaluator_role === 'dosen')
                                        <span class="badge bg-info">Dosen</span>
                                    @elseif($evaluation->evaluator_role === 'pembimbing_lapangan')
                                        <span class="badge bg-warning">Pembimbing</span>
                                    @else
                                        <span class="badge bg-secondary">Self</span>
                                    @endif
                                </td>
                                <td>
                                    @if($evaluation->rating)
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-star-filled text-warning me-1"></i>
                                            <strong>{{ $evaluation->rating }}/10</strong>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($evaluation->sentimentResult)
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
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($evaluation->sentimentResult)
                                        <span class="badge bg-light text-dark">
                                            {{ number_format($evaluation->sentimentResult->sentiment_score * 100, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ Str::limit($evaluation->comment_text, 50) }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('evaluations.show', $evaluation) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali ke Daftar Mahasiswa
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sentiment Pie Chart
    @if($totalEvaluations > 0)
    var sentimentPieOptions = {
        series: [{{ $positiveCount }}, {{ $neutralCount }}, {{ $negativeCount }}],
        chart: {
            type: 'donut',
            height: 280
        },
        labels: ['Positive', 'Neutral', 'Negative'],
        colors: ['#2ca87f', '#6c757d', '#dc2626'],
        legend: {
            position: 'bottom'
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Evaluasi',
                            fontSize: '14px',
                            formatter: function (w) {
                                return {{ $totalEvaluations }}
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
                return opts.w.config.series[opts.seriesIndex]
            }
        },
        tooltip: {
            y: {
                formatter: function(val, opts) {
                    var percent = (val / {{ $totalEvaluations }} * 100).toFixed(1);
                    return val + ' (' + percent + '%)'
                }
            }
        }
    };

    var sentimentPieChart = new ApexCharts(document.querySelector("#sentimentPieChart"), sentimentPieOptions);
    sentimentPieChart.render();

    // Rating Gauge Chart
    var ratingGaugeOptions = {
        series: [{{ $avgRating * 10 }}],
        chart: {
            type: 'radialBar',
            height: 280
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                hollow: {
                    size: '70%',
                },
                track: {
                    background: '#f0f0f0',
                    strokeWidth: '100%',
                },
                dataLabels: {
                    name: {
                        show: true,
                        fontSize: '16px',
                        offsetY: -10
                    },
                    value: {
                        show: true,
                        fontSize: '30px',
                        fontWeight: 'bold',
                        offsetY: 10,
                        formatter: function(val) {
                            return (val / 10).toFixed(1)
                        }
                    }
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'horizontal',
                shadeIntensity: 0.5,
                gradientToColors: ['#2ca87f'],
                inverseColors: true,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100]
            }
        },
        stroke: {
            lineCap: 'round'
        },
        labels: ['Rating']
    };

    var ratingGaugeChart = new ApexCharts(document.querySelector("#ratingGaugeChart"), ratingGaugeOptions);
    ratingGaugeChart.render();
    @endif
});
</script>
@endpush
