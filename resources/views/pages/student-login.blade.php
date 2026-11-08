@extends('layouts.app')

@section('title', 'Student Login Portal | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center">
            <h1>Student Self-Service Portal</h1>
            <p class="text-white-50">Access your digital ID card, batch schedule, fee receipts, exam admit card, and verified certificates.</p>
        </div>
    </div>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-white">
                        <div class="text-center mb-4">
                            <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="mb-3" style="max-height: 75px; width: auto; object-fit: contain;">
                            <h3 class="fw-bold text-dark mb-1">Student Sign In</h3>
                            <small class="text-muted">Enter your Registration No. / Roll No. and Password</small>
                        </div>

                        <form action="{{ route('student.login.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Registration No. / Roll No. / Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-id-badge text-muted"></i></span>
                                    <input type="text" name="login_id" class="form-control" placeholder="e.g. TEC2601001 or student email" value="{{ old('login_id') }}" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <a href="{{ route('password.request') }}" class="text-primary small text-decoration-none">Forgot Password?</a>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                                    <input type="password" name="password" id="studentPasswordInput" class="form-control border-start-0 border-end-0" placeholder="••••••••" required>
                                    <button class="btn btn-outline-secondary border-start-0 bg-light text-muted" type="button" id="toggleStudentPassword" style="border-color: #dee2e6;" title="Show/Hide Password">
                                        <i class="fa-solid fa-eye" id="toggleStudentIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="rememberMe">
                                <label class="form-check-label small text-muted" for="rememberMe">Keep me logged in on this device</label>
                            </div>

                            <button type="submit" class="btn btn-dce-primary w-100 py-2 fw-bold fs-6">
                                <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Sign In to Portal
                            </button>
                        </form>

                        <div class="mt-4 pt-3 border-top text-center">
                            <p class="text-muted small mb-2">Haven't received your student password yet?</p>
                            <span class="small text-secondary">
                                <i class="fa-solid fa-circle-info text-info me-1"></i> Your password is sent to your registered WhatsApp mobile upon admission, or you can contact your authorized centre administrator.
                            </span>
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
    const toggleBtn = document.getElementById('toggleStudentPassword');
    const pwdInput = document.getElementById('studentPasswordInput');
    const icon = document.getElementById('toggleStudentIcon');

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
