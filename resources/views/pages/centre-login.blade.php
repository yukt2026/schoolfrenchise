@extends('layouts.app')

@section('title', 'Centre / Admin Portal Login | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center">
            <h1>Centre & Admin Management Portal</h1>
            <p class="text-white-50">Authorized franchise centre management, student admissions, fee collection, exam marks and certificate issuance.</p>
        </div>
    </div>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-white">
                        <div class="text-center mb-4">
                            <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="mb-3" style="max-height: 75px; width: auto; object-fit: contain;">
                            <h3 class="fw-bold text-dark mb-1">Centre / Admin Sign In</h3>
                            <small class="text-muted">Enter your registered email or mobile and password</small>
                        </div>

                        <form action="{{ route('login.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Official Email / Mobile <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-muted"></i></span>
                                    <input type="text" name="email" class="form-control" placeholder="centre@branch.com or mobile" value="{{ old('email') }}" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label">Secret Password <span class="text-danger">*</span></label>
                                    <a href="{{ route('password.request') }}" class="text-primary small text-decoration-none">Reset Password?</a>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                                    <input type="password" name="password" id="centrePasswordInput" class="form-control border-start-0 border-end-0" placeholder="••••••••" required>
                                    <button class="btn btn-outline-secondary border-start-0 bg-light text-muted" type="button" id="toggleCentrePassword" style="border-color: #dee2e6;" title="Show/Hide Password">
                                        <i class="fa-solid fa-eye" id="toggleCentreIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="rememberCentre">
                                <label class="form-check-label small text-muted" for="rememberCentre">Remember my login session</label>
                            </div>

                            <button type="submit" class="btn btn-dce-accent w-100 py-2 fw-bold fs-6">
                                <i class="fa-solid fa-key me-2"></i> Authenticate to Dashboard
                            </button>
                        </form>

                        <div class="mt-4 pt-3 border-top text-center">
                            <p class="text-muted small mb-2">Want to open a new authorized study centre?</p>
                            <a href="{{ route('franchise.apply') }}" class="btn btn-sm btn-outline-primary fw-bold">
                                <i class="fa-solid fa-file-signature me-1"></i> Apply for Franchise Affiliation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleCentrePassword');
    const pwdInput = document.getElementById('centrePasswordInput');
    const icon = document.getElementById('toggleCentreIcon');

    if (toggleBtn && pwdInput && icon) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = pwdInput.type === 'password';
            pwdInput.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
        });
    }
});
</script>
@endpush
