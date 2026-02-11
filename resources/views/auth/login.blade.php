@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="card my-5">
    <div class="card-body">
        <div class="text-center mb-4">
            <img src="{{ asset('images/unsada-logo.png') }}" alt="Universitas Darma Persada" class="mb-3" style="max-width: 120px; height: auto;">
            <h3 class="mb-0"><b>Sistem Evaluasi KP</b></h3>
            <p class="text-muted">Universitas Darma Persada</p>
            <p class="text-muted small">Silakan login untuk melanjutkan</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       placeholder="Email Address" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="Password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex mt-1 justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input input-primary" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label text-muted" for="remember">Remember me</label>
                </div>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-login me-2"></i>Login
                </button>
            </div>
        </form>

        <div class="mt-4">
            <div class="alert alert-info">
                <strong>Test Credentials:</strong><br>
                Admin: admin@example.com / password<br>
                Dosen: dosen1@example.com / password<br>
                Mahasiswa: mahasiswa1@example.com / password
            </div>
        </div>
    </div>
</div>
@endsection
