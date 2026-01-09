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
                <div class="table-responsive">
                    <table id="evaluationsTable" class="table table-hover" style="width:100%">
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
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<!-- jQuery - Required by DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#evaluationsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('evaluations.datatables') }}',
            type: 'GET'
        },
        columns: [
            {
                data: 'student',
                name: 'student',
                orderable: true,
                render: function(data) {
                    return '<strong>' + data.name + '</strong><br>' +
                           '<small class="text-muted">' + data.nim + '</small>';
                }
            },
            {
                data: 'evaluator',
                name: 'evaluator',
                orderable: true
            },
            {
                data: 'evaluator_role',
                name: 'evaluator_role',
                orderable: true,
                render: function(data) {
                    const badges = {
                        'admin': '<span class="badge bg-primary">Admin</span>',
                        'kaprodi': '<span class="badge bg-success">Kaprodi</span>',
                        'dosen': '<span class="badge bg-info">Dosen</span>',
                        'pembimbing_lapangan': '<span class="badge bg-warning">Pembimbing Lapangan</span>',
                        'mahasiswa': '<span class="badge bg-secondary">Mahasiswa</span>'
                    };
                    return badges[data] || data;
                }
            },
            {
                data: 'rating',
                name: 'rating',
                orderable: true,
                render: function(data) {
                    if (data) {
                        return '<span class="badge bg-light-primary border border-primary">' +
                               data + '/10</span>';
                    }
                    return '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'sentiment',
                name: 'sentiment',
                orderable: true,
                render: function(data) {
                    if (!data) {
                        return '<span class="text-muted">-</span>';
                    }

                    const icons = {
                        'positive': '<i class="ti ti-mood-smile"></i>',
                        'negative': '<i class="ti ti-mood-sad"></i>',
                        'neutral': '<i class="ti ti-mood-neutral"></i>'
                    };

                    const colors = {
                        'positive': 'bg-success',
                        'negative': 'bg-danger',
                        'neutral': 'bg-secondary'
                    };

                    const label = data.label.charAt(0).toUpperCase() + data.label.slice(1);
                    return '<span class="badge ' + colors[data.label] + '">' +
                           icons[data.label] + ' ' + label +
                           '</span>';
                }
            },
            {
                data: 'created_at',
                name: 'created_at',
                orderable: true,
                render: function(data) {
                    return '<small class="text-muted">' +
                           data.date + '<br>' +
                           data.time +
                           '</small>';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let buttons = '<a href="/evaluations/' + row.id + '" class="btn btn-sm btn-outline-primary">' +
                                 '<i class="ti ti-eye"></i> Lihat</a> ';

                    if (row.can_update) {
                        buttons += '<a href="/evaluations/' + row.id + '/edit" class="btn btn-sm btn-outline-secondary">' +
                                  '<i class="ti ti-edit"></i> Edit</a>';
                    }

                    return buttons;
                }
            }
        ],
        order: [[5, 'desc']], // Order by date descending
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            search: '<i class="ti ti-search"></i>',
            searchPlaceholder: 'Cari evaluasi...',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ evaluasi',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(difilter dari _MAX_ total evaluasi)',
            paginate: {
                first: '<i class="ti ti-chevrons-left"></i>',
                last: '<i class="ti ti-chevrons-right"></i>',
                next: '<i class="ti ti-chevron-right"></i>',
                previous: '<i class="ti ti-chevron-left"></i>'
            },
            emptyTable: 'Belum ada data evaluasi',
            zeroRecords: 'Tidak ada data yang cocok'
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });
});
</script>
@endpush
