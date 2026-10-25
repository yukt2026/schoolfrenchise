@extends('layouts.centre')

@section('title', 'Raise Support Ticket - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4">
    <a href="{{ route('centre.support.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
        <i class="fa-solid fa-arrow-left me-1"></i>Back to Support Desk
    </a>
    <h3 class="fw-bold text-dark mb-1">
        <i class="fa-solid fa-circle-plus text-primary me-2"></i>Open Support Ticket
    </h3>
    <p class="text-muted small mb-0">Submit a query or request to Head Office for centre {{ $centre->centre_name }} ({{ $centre->centre_code }}).</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 rounded-3 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('centre.support.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department / Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select rounded-2" required>
                                <option value="">Select Category</option>
                                <option value="wallet" {{ old('category') == 'wallet' ? 'selected' : '' }}>Franchise Wallet & Top-up</option>
                                <option value="certificate" {{ old('category') == 'certificate' ? 'selected' : '' }}>Certificates & Marksheets Issuance</option>
                                <option value="exam" {{ old('category') == 'exam' ? 'selected' : '' }}>Examinations & Question Papers</option>
                                <option value="academic" {{ old('category') == 'academic' ? 'selected' : '' }}>Academic Courses & Study Material</option>
                                <option value="fees" {{ old('category') == 'fees' ? 'selected' : '' }}>Fee Structure & Concessions</option>
                                <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Technical & Portal Issues</option>
                                <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General Directorate Inquiry</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Priority Level <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select rounded-2" required>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium (Standard)</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subject / Short Summary <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control rounded-2" placeholder="e.g. Urgent verification of wallet recharge UTR-9281038" value="{{ old('subject') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Detailed Query / Problem Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control rounded-2" rows="5" placeholder="Provide full context, candidate roll numbers or transaction details if applicable..." required>{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Supporting File / Document Attachment <small class="text-muted">(Optional)</small></label>
                        <input type="file" name="attachment" class="form-control rounded-2" accept=".pdf,image/*,.doc,.docx">
                        <div class="form-text" style="font-size: 0.72rem;">PDF, Images, DOC up to 5MB.</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg rounded-2 px-5 fw-bold shadow-sm">
                        <i class="fa-solid fa-paper-plane me-1"></i>Submit Support Ticket
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
