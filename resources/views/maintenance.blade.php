@extends('layouts.app')

@section('title', 'Under Maintenance')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-lg">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="ti ti-tool" style="font-size: 6rem; color: #FFA500;"></i>
                </div>
                <h2 class="mb-3">Under Maintenance</h2>
                <p class="text-muted mb-4">
                    Sistem sedang dalam pemeliharaan. Mohon maaf atas ketidaknyamanannya.<br>
                    Silakan coba lagi nanti.
                </p>
                <div class="alert alert-warning d-inline-block">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Admin dan Kaprodi</strong> tetap dapat mengakses sistem.
                </div>
                <div class="mt-4">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-logout"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
