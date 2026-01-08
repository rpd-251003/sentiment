@extends('layouts.app')

@section('title', 'Application Settings')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Application Settings</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item" aria-current="page">Settings</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-md-10 mx-auto">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-x"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <!-- General Settings -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-settings"></i> General Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_name" class="form-label">Application Name <span class="text-danger">*</span></label>
                                <input type="text" id="app_name" name="app_name"
                                       class="form-control @error('app_name') is-invalid @enderror"
                                       value="{{ old('app_name', $settings['app_name']) }}" required>
                                @error('app_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="ti ti-info-circle"></i> Nama aplikasi yang ditampilkan di browser title
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_logo" class="form-label">Application Logo/Text <span class="text-danger">*</span></label>
                                <input type="text" id="app_logo" name="app_logo"
                                       class="form-control @error('app_logo') is-invalid @enderror"
                                       value="{{ old('app_logo', $settings['app_logo']) }}" required>
                                @error('app_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="ti ti-info-circle"></i> Text logo yang ditampilkan di sidebar
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="maintenance_mode" name="maintenance_mode" value="1"
                                   {{ old('maintenance_mode', $settings['maintenance_mode']) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="maintenance_mode">
                                <strong>Maintenance Mode</strong>
                            </label>
                        </div>
                        <div class="form-text ms-4">
                            <i class="ti ti-alert-triangle text-warning"></i>
                            Ketika aktif, hanya Admin dan Kaprodi yang bisa mengakses dashboard.
                            Role lain akan melihat halaman maintenance.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sentiment Analysis API Settings -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-brain"></i> Sentiment Analysis API Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="hf_api_url" class="form-label">Hugging Face API URL <span class="text-danger">*</span></label>
                        <input type="url" id="hf_api_url" name="hf_api_url"
                               class="form-control @error('hf_api_url') is-invalid @enderror"
                               value="{{ old('hf_api_url', $settings['hf_api_url']) }}" required>
                        @error('hf_api_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="ti ti-info-circle"></i> URL endpoint untuk Hugging Face sentiment analysis model
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="hf_token" class="form-label">Hugging Face Token <span class="text-danger">*</span></label>
                        <input type="text" id="hf_token" name="hf_token"
                               class="form-control @error('hf_token') is-invalid @enderror"
                               value="{{ old('hf_token', $settings['hf_token']) }}" required>
                        @error('hf_token')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="ti ti-info-circle"></i> Token autentikasi untuk mengakses Hugging Face API
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy"></i> Simpan Pengaturan
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-x"></i> Batal
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
