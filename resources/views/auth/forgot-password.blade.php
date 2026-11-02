@extends('layouts.app')

@section('title', 'Forgot Password - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="py-5" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.05), rgba(6, 182, 212, 0.05)); min-height: 70vh;">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white text-center py-4" style="background: linear-gradient(135deg, #3730a3, #4f46e5) !important;">
                        <i class="fa-solid fa-key fa-2x mb-2 text-warning"></i>
                        <h4 class="fw-bold mb-1">Reset Password</h4>
                        <p class="small text-white-50 mb-0">Enter your Email, Student Roll No, or Mobile Number</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="fa-solid fa-circle-check text-success fs-5 mt-1"></i>
                                    <div>{{ session('success') }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-triangle-exclamation text-danger fs-5"></i>
                                    <div>{{ session('error') }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold text-secondary">Registered Email, Roll No, or Mobile <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user-shield text-primary"></i></span>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="e.g. ROL-2026-0001 / Mobile / Email">
                                </div>
                                <div class="form-text small text-muted mt-2">
                                    <i class="fa-solid fa-circle-info me-1 text-primary"></i> Students can use their Roll No or Registered Mobile. Franchise centres can use Centre Code or Email.
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg fw-semibold shadow-sm">
                                    <i class="fa-solid fa-paper-plane me-2"></i>Generate Reset Link
                                </button>
                            </div>

                            <div class="text-center pt-3 border-top">
                                <a href="{{ route('login') }}" class="text-decoration-none text-muted small me-3">
                                    <i class="fa-solid fa-arrow-left me-1"></i>Centre Login
                                </a>
                                <span class="text-muted">|</span>
                                <a href="{{ route('student.login') }}" class="text-decoration-none text-muted small ms-3">
                                    Student Login<i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
