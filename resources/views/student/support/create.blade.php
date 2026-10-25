@extends('layouts.student')

@section('title', 'Raise Support Ticket - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.support') }}" class="text-decoration-none">Support</a></li>
                <li class="breadcrumb-item active" aria-current="page">Raise Ticket</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="fa-solid fa-plus-circle text-primary me-2"></i>Raise a Support Ticket
        </h2>
        <p class="text-muted small mb-0">Describe your query or technical issue. The centre administration will review and respond promptly.</p>
    </div>
    <a href="{{ route('student.support') }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-semibold">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Tickets
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border border-light-subtle shadow-sm rounded-3 p-4 p-md-5 bg-white">
            <form action="{{ route('student.support.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Category & Priority -->
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark small">Department / Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                            <option value="">-- Select Query Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark small">Priority Level <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low (General Question)</option>
                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium (Standard Request)</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High (Urgent Attention)</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Subject -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">Subject / Short Summary <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" placeholder="e.g. Discrepancy in installment receipt or attendance record" value="{{ old('subject') }}" required maxlength="255">
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Detailed Description -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">Detailed Description <span class="text-danger">*</span></label>
                    <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Please provide specific details, dates, transaction references, or batch info to help us resolve your issue quickly." required maxlength="5000">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text small text-muted">Maximum 5,000 characters.</div>
                </div>

                <!-- File Attachment -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small">Supporting Document / Screenshot (Optional)</label>
                    <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                    @error('attachment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text small text-muted">Accepted formats: PDF, JPG, PNG. Maximum file size: 5 MB.</div>
                </div>

                <!-- Centre Note -->
                <div class="alert alert-light border rounded-3 p-3 mb-4 d-flex align-items-center gap-3">
                    <i class="fa-solid fa-building-columns text-primary fs-4"></i>
                    <div class="small text-muted">
                        This ticket will be routed to your assigned centre administration: <strong>{{ $student->centre->centre_name ?? 'Centre Staff' }}</strong>. You will receive notification updates in your portal.
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('student.support') }}" class="btn btn-outline-secondary rounded-2 px-4 fw-semibold">Cancel</a>
                    <button type="submit" class="btn btn-primary rounded-2 px-4 fw-semibold">
                        <i class="fa-solid fa-paper-plane me-1"></i> Submit Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
