@extends('layouts.student')

@section('title', 'Change Password - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.profile') }}" class="text-decoration-none">Profile</a></li>
                <li class="breadcrumb-item active" aria-current="page">Change Password</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="fa-solid fa-key text-primary me-2"></i>Change Account Password
        </h2>
        <p class="text-muted small mb-0">Update your student portal login credentials to keep your profile and academic documents secure.</p>
    </div>
    <a href="{{ route('student.profile') }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-semibold">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Profile
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card border border-light-subtle shadow-sm rounded-3 p-4 p-md-5 bg-white">
            <form action="{{ route('student.change-password.update') }}" method="POST">
                @csrf

                <!-- Current Password -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">Current Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="current_password" id="current_password" class="form-control border-start-0 border-end-0 @error('current_password') is-invalid @enderror" placeholder="Enter current password" required>
                        <button type="button" class="btn btn-light border border-start-0 text-muted" onclick="togglePass('current_password', this)">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">New Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-shield-halved"></i></span>
                        <input type="password" name="password" id="new_password" class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" placeholder="Minimum 8 characters" required minlength="8">
                        <button type="button" class="btn btn-light border border-start-0 text-muted" onclick="togglePass('new_password', this)">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small">Confirm New Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-check-double"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 border-end-0" placeholder="Re-type new password" required minlength="8">
                        <button type="button" class="btn btn-light border border-start-0 text-muted" onclick="togglePass('password_confirmation', this)">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Security Instructions -->
                <div class="alert alert-light border rounded-3 p-3 mb-4">
                    <div class="small fw-bold text-dark mb-1">
                        <i class="fa-solid fa-circle-check text-success me-1"></i> Password Security Requirements:
                    </div>
                    <ul class="text-muted small ps-3 mb-0" style="font-size: 0.78rem;">
                        <li>At least 8 characters in length.</li>
                        <li>Include numbers, uppercase letters, and special symbols for stronger protection.</li>
                        <li>Do not share your password with fellow students or unauthorized persons.</li>
                    </ul>
                </div>

                <!-- Action Button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary rounded-3 py-2 fw-semibold">
                        <i class="fa-solid fa-key me-2"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection
