@extends('layouts.app')

@section('title', 'Daftar Evaluasi')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Daftar Evaluasi</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page">Evaluasi</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-list"></i> Semua Evaluasi</h5>
                @can('create', App\Models\KpEvaluation::class)
                <a href="{{ route('evaluations.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Buat Evaluasi Baru
                </a>
                @endcan
            </div>
            <div class="card-body">
                @if($evaluations->isEmpty())
                    <div class="text-center py-5">
                        <i class="ti ti-inbox" style="font-size: 4rem; color: #ccc;"></i>
                        <h5 class="mt-3 text-muted">Belum ada evaluasi</h5>
                        <p class="text-muted">Mulai dengan membuat evaluasi pertama Anda.</p>
                        @can('create', App\Models\KpEvaluation::class)
                        <a href="{{ route('evaluations.create') }}" class="btn btn-primary mt-2">
                            <i class="ti ti-plus"></i> Buat Evaluasi
                        </a>
                        @endcan
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th>Evaluator</th>
                                    <th>Role</th>
                                    <th>Rating</th>
                                    <th>Sentimen</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($evaluations as $evaluation)
                                <tr>
                                    <td>
                                        <strong>{{ $evaluation->student->name }}</strong><br>
                                        <small class="text-muted">{{ $evaluation->student->nim }}</small>
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
                                            <span class="badge bg-warning">Pembimbing Lapangan</span>
                                        @else
                                            <span class="badge bg-secondary">Mahasiswa</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($evaluation->rating)
                                            <span class="badge bg-light-primary border border-primary">
                                                {{ $evaluation->rating }}/10
                                            </span>
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
                                            <br>
                                            <small class="text-muted">
                                                Score: {{ number_format($evaluation->sentimentResult->sentiment_score, 3) }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $evaluation->created_at->format('d M Y') }}<br>
                                            {{ $evaluation->created_at->format('H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <a href="{{ route('evaluations.show', $evaluation) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-eye"></i> Lihat
                                        </a>
                                        @can('update', $evaluation)
                                        <a href="{{ route('evaluations.edit', $evaluation) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="ti ti-edit"></i> Edit
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @if(!$evaluations->isEmpty())
            <div class="card-footer">
                {{ $evaluations->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
