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
    <div class="col-md-6 col-xl-6">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Mahasiswa</h6>
                <h4 class="mb-3">{{ $stats['total_students'] }} <span class="badge bg-light-primary border border-primary"><i class="ti ti-school"></i></span></h4>
                <p class="mb-0 text-muted text-sm">Mahasiswa terdaftar dalam sistem</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-6">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Evaluasi</h6>
                <h4 class="mb-3">{{ $stats['total_evaluations'] }} <span class="badge bg-light-success border border-success"><i class="ti ti-clipboard-check"></i></span></h4>
                <p class="mb-0 text-muted text-sm">Evaluasi yang telah diinput</p>
            </div>
        </div>
    </div>

    <!-- Sentiment Statistics Cards -->
    <div class="col-md-12">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body">
                <h5 class="text-white mb-3"><i class="ti ti-chart-bar"></i> Statistik Sentimen Analysis</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-white text-dark">
                            <div class="card-body text-center">
                                <i class="ti ti-database" style="font-size: 2rem; color: #6c757d;"></i>
                                <h3 class="mt-2 mb-0">{{ $sentimentStats['total'] }}</h3>
                                <p class="mb-0 text-muted">Total Hasil Sentimen</p>
                                <hr class="my-2">
                                <h4 class="mb-0 text-primary">{{ $sentimentStats['total_all_scores'] }}</h4>
                                <small class="text-muted">Total Semua Score</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <i class="ti ti-mood-smile" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0">{{ $sentimentStats['positive']['count'] }}</h3>
                                <p class="mb-0">Positive (Label)</p>
                                <small>{{ $sentimentStats['positive']['percentage'] }}% dari hasil</small>
                                <hr class="my-2" style="border-color: rgba(255,255,255,0.3);">
                                <h4 class="mb-0">{{ $sentimentStats['positive']['total_score'] }}</h4>
                                <small>Total Score: {{ $sentimentStats['positive']['score_percentage'] }}%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <i class="ti ti-mood-neutral" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0">{{ $sentimentStats['neutral']['count'] }}</h3>
                                <p class="mb-0">Neutral (Label)</p>
                                <small>{{ $sentimentStats['neutral']['percentage'] }}% dari hasil</small>
                                <hr class="my-2" style="border-color: rgba(255,255,255,0.3);">
                                <h4 class="mb-0">{{ $sentimentStats['neutral']['total_score'] }}</h4>
                                <small>Total Score: {{ $sentimentStats['neutral']['score_percentage'] }}%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <i class="ti ti-mood-sad" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0">{{ $sentimentStats['negative']['count'] }}</h3>
                                <p class="mb-0">Negative (Label)</p>
                                <small>{{ $sentimentStats['negative']['percentage'] }}% dari hasil</small>
                                <hr class="my-2" style="border-color: rgba(255,255,255,0.3);">
                                <h4 class="mb-0">{{ $sentimentStats['negative']['total_score'] }}</h4>
                                <small>Total Score: {{ $sentimentStats['negative']['score_percentage'] }}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Chart: Sentiment Traffic (30 Days) -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-chart-line"></i> Traffic Sentiment Analysis (30 Hari Terakhir)</h5>
            </div>
            <div class="card-body">
                @if($sentimentTraffic->isEmpty())
                    <p class="text-muted text-center py-4">Belum ada data traffic sentiment</p>
                @else
                    <div id="sentimentLineChart" style="min-height: 350px;"></div>
                @endif
            </div>
        </div>
    </div>

    <!-- Pie Chart: Sentiment Distribution -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-chart-pie"></i> Distribusi Sentimen</h5>
            </div>
            <div class="card-body">
                @if($sentimentDistribution->isEmpty())
                    <p class="text-muted text-center py-4">Belum ada data sentimen</p>
                @else
                    <div id="sentimentPieChart" style="min-height: 350px;"></div>
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
                @can('create', App\Models\KpEvaluation::class)
                <a href="{{ route('evaluations.create') }}" class="btn btn-primary me-2">
                    <i class="ti ti-plus"></i> Buat Evaluasi Baru
                </a>
                @endcan
                <a href="{{ route('evaluations.index') }}" class="btn btn-outline-primary">
                    <i class="ti ti-list"></i> Lihat Semua Evaluasi
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
    @if(!$sentimentDistribution->isEmpty())
    // Pie Chart: Sentiment Distribution
    const pieChartData = @json($sentimentDistribution);
    const pieLabels = pieChartData.map(item => item.sentiment_label.charAt(0).toUpperCase() + item.sentiment_label.slice(1));
    const pieSeries = pieChartData.map(item => parseInt(item.count));
    const pieColors = pieChartData.map(item => {
        if (item.sentiment_label.toLowerCase() === 'positive') return '#28a745';
        if (item.sentiment_label.toLowerCase() === 'negative') return '#dc3545';
        return '#6c757d';
    });

    const pieOptions = {
        series: pieSeries,
        chart: {
            type: 'pie',
            height: 350
        },
        labels: pieLabels,
        colors: pieColors,
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
                return opts.w.globals.series[opts.seriesIndex];
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " evaluasi";
                }
            }
        }
    };

    const pieChart = new ApexCharts(document.querySelector("#sentimentPieChart"), pieOptions);
    pieChart.render();
    @endif

    @if(!$sentimentTraffic->isEmpty())
    // Line Chart: Sentiment Traffic
    const trafficData = @json($sentimentTraffic);
    const dates = trafficData.map(item => item.date);
    const positiveData = trafficData.map(item => parseInt(item.positive));
    const neutralData = trafficData.map(item => parseInt(item.neutral));
    const negativeData = trafficData.map(item => parseInt(item.negative));

    const lineOptions = {
        series: [
            {
                name: 'Positive',
                data: positiveData
            },
            {
                name: 'Neutral',
                data: neutralData
            },
            {
                name: 'Negative',
                data: negativeData
            }
        ],
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: true
            }
        },
        colors: ['#28a745', '#6c757d', '#dc3545'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        markers: {
            size: 4,
            hover: {
                size: 6
            }
        },
        xaxis: {
            categories: dates,
            type: 'datetime',
            labels: {
                format: 'dd MMM'
            }
        },
        yaxis: {
            title: {
                text: 'Jumlah Sentiment'
            },
            labels: {
                formatter: function(val) {
                    return Math.round(val);
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right'
        },
        tooltip: {
            x: {
                format: 'dd MMM yyyy'
            },
            y: {
                formatter: function(val) {
                    return val + " sentiment";
                }
            }
        },
        grid: {
            borderColor: '#e7e7e7'
        }
    };

    const lineChart = new ApexCharts(document.querySelector("#sentimentLineChart"), lineOptions);
    lineChart.render();
    @endif
});
</script>
@endpush
