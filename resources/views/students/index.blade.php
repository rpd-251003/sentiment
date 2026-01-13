@extends('layouts.app')

@section('title', 'Daftar Mahasiswa Bimbingan')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Daftar Mahasiswa Bimbingan</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page">Mahasiswa</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-users"></i>
                    @if(auth()->user()->role === 'dosen')
                        Mahasiswa yang Saya Bimbing
                    @else
                        Semua Mahasiswa
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="studentsTable" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Mahasiswa</th>
                                <th>Dosen Pembimbing</th>
                                <th>Pembimbing Lapangan</th>
                                <th>Perusahaan</th>
                                <th>Jumlah Evaluasi</th>
                                <th>Evaluasi Terakhir</th>
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
    $('#studentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('students.datatables') }}',
            type: 'GET'
        },
        columns: [
            {
                data: 'student_info',
                name: 'student_info',
                orderable: true,
                render: function(data) {
                    return '<strong>' + data.name + '</strong><br>' +
                           '<small class="text-muted">NIM: ' + data.nim + '</small>';
                }
            },
            {
                data: 'dosen_name',
                name: 'dosen_name',
                orderable: true,
                render: function(data) {
                    return data ? '<span class="text-primary">' + data + '</span>' :
                           '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'pembimbing_name',
                name: 'pembimbing_name',
                orderable: true,
                render: function(data) {
                    return data ? '<span class="text-info">' + data + '</span>' :
                           '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'company_name',
                name: 'company_name',
                orderable: true,
                render: function(data) {
                    return data ? data : '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'evaluations_count',
                name: 'evaluations_count',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data) {
                    if (data > 0) {
                        return '<span class="badge bg-primary">' + data + '</span>';
                    }
                    return '<span class="badge bg-secondary">0</span>';
                }
            },
            {
                data: 'latest_evaluation',
                name: 'latest_evaluation',
                orderable: false,
                searchable: false,
                render: function(data) {
                    if (data) {
                        return '<small class="text-muted">' + data.date + '<br>' +
                               data.time + '<br>' +
                               '<strong>' + data.evaluator + '</strong></small>';
                    }
                    return '<span class="text-muted">Belum ada</span>';
                }
            },
            {
                data: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let buttons = '';

                    // View student evaluations
                    buttons += '<a href="/evaluations?student_id=' + data + '" ' +
                              'class="btn btn-sm btn-outline-primary" title="Lihat Evaluasi">' +
                              '<i class="ti ti-file-text"></i> Evaluasi</a> ';

                    @can('create', App\Models\KpEvaluation::class)
                    // Create new evaluation
                    buttons += '<a href="/evaluations/create?student_id=' + data + '" ' +
                              'class="btn btn-sm btn-outline-success" title="Buat Evaluasi Baru">' +
                              '<i class="ti ti-plus"></i> Buat</a>';
                    @endcan

                    return buttons;
                }
            }
        ],
        order: [[0, 'asc']], // Order by student name ascending
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            search: '<i class="ti ti-search"></i>',
            searchPlaceholder: 'Cari mahasiswa, dosen, pembimbing, atau perusahaan...',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ mahasiswa',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(difilter dari _MAX_ total mahasiswa)',
            paginate: {
                first: '<i class="ti ti-chevrons-left"></i>',
                last: '<i class="ti ti-chevrons-right"></i>',
                next: '<i class="ti ti-chevron-right"></i>',
                previous: '<i class="ti ti-chevron-left"></i>'
            },
            emptyTable: 'Belum ada data mahasiswa',
            zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });
});
</script>
@endpush
