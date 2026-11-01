@extends('layouts.app')

@section('title', 'Online Fee Payment | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container">
            <h1>Online Student Fee Payment Portal</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Student Zone</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pay Online</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Step 1: Optional Roll No Lookup -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light">
                        <h5 class="fw-bold text-primary mb-2"><i class="fa-solid fa-user-check me-2"></i>Quick Student Lookup (Optional)</h5>
                        <p class="text-muted small mb-3">If you are an enrolled student, enter your Roll Number, Registration Number, or Registered Mobile Number to automatically fetch your details and outstanding course fees.</p>
                        <form action="{{ route('payment.index') }}" method="GET" class="row g-2">
                            <div class="col-sm-9">
                                <input type="text" name="roll_no" class="form-control" placeholder="Enter Roll No. / Registration No. / Mobile Number" value="{{ request('roll_no') }}">
                            </div>
                            <div class="col-sm-3">
                                <button type="submit" class="btn btn-primary w-100 fw-bold">
                                    <i class="fa-solid fa-search me-1"></i> Fetch
                                </button>
                            </div>
                        </form>
                    </div>

                    @if(session('search_not_found'))
                        <div class="alert alert-warning d-flex align-items-center gap-2 p-3 rounded-3 mb-4 shadow-sm" role="alert">
                            <i class="fa-solid fa-triangle-exclamation text-warning fs-4"></i>
                            <div>
                                <strong>Student Not Found:</strong> {{ session('search_not_found') }}
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger d-flex align-items-center gap-2 p-3 rounded-3 mb-4 shadow-sm" role="alert">
                            <i class="fa-solid fa-circle-exclamation text-danger fs-4"></i>
                            <div>
                                <strong>Payment Error:</strong> {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    @if(isset($student) && $student)
                        <div class="alert alert-success d-flex align-items-center justify-content-between p-3 rounded-3 mb-4 shadow-sm">
                            <div>
                                <strong class="d-block text-dark"><i class="fa-solid fa-circle-check text-success me-1"></i> Student Record Found: {{ $student->full_name }}</strong>
                                <small class="text-muted">Course: {{ $student->admissions->first()->course->course_name ?? 'IT Program' }} | Roll No: {{ $student->roll_no ?? 'Pending' }} | Centre: {{ $student->centre->centre_name ?? 'Head Office' }}</small>
                            </div>
                            @if(isset($pendingFee) && $pendingFee > 0)
                                <div class="text-end">
                                    <small class="text-muted d-block">Pending Balance:</small>
                                    <strong class="text-danger fs-5">₹{{ number_format($pendingFee, 2) }}</strong>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Step 2: Payment Checkout Form -->
                    <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                            <div class="rounded-circle bg-success text-white p-3 fs-3">
                                <i class="fa-solid fa-shield-heart"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0">Secure Payment Checkout</h3>
                                <small class="text-muted">Verified 256-bit Encrypted Transaction Gateway</small>
                            </div>
                        </div>

                        <form action="{{ route('payment.process') }}" method="POST">
                            @csrf
                            <input type="hidden" name="student_roll_no" value="{{ $student->roll_no ?? request('roll_no') }}">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Payer / Student Name <span class="text-danger">*</span></label>
                                    <input type="text" name="payer_name" class="form-control" placeholder="Full name of payer" value="{{ $student->full_name ?? old('payer_name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="payer_mobile" class="form-control" placeholder="10-digit mobile" value="{{ $student->mobile ?? old('payer_mobile') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address (Optional)</label>
                                    <input type="email" name="payer_email" class="form-control" placeholder="receipt@example.com" value="{{ $student->email ?? old('payer_email') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Payment Purpose <span class="text-danger">*</span></label>
                                    <select name="purpose" class="form-select" required>
                                        <option value="Course Tuition Fee" selected>Course Tuition Fee</option>
                                        <option value="Admission Registration Fee">Admission Registration Fee</option>
                                        <option value="Board Examination Fee">Board Examination Fee</option>
                                        <option value="Certificate Verification Fee">Certificate Verification Fee</option>
                                        <option value="Study Material & Books">Study Material & Books</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Payment Amount (INR ₹) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold">₹</span>
                                        <input type="number" step="0.01" min="10" name="amount" class="form-control fw-bold fs-5 text-primary" placeholder="0.00" value="{{ $pendingFee > 0 ? $pendingFee : (old('amount') ?? '1500.00') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="UPI" selected>UPI (Google Pay, PhonePe, Paytm, BHIM)</option>
                                        <option value="DebitCard">Debit Card (RuPay, Visa, MasterCard)</option>
                                        <option value="CreditCard">Credit Card</option>
                                        <option value="NetBanking">Net Banking (All Indian Banks)</option>
                                    </select>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-3 mb-3">
                                        <i class="fa-solid fa-lock text-success fs-3"></i>
                                        <small class="text-muted">
                                            Your transaction is processed via certified encryption. An official computerized tax receipt with transaction reference ID and verification signature will be generated immediately.
                                        </small>
                                    </div>
                                    <button type="submit" class="btn btn-dce-accent w-100 py-3 fw-bold fs-5">
                                        <i class="fa-solid fa-lock me-2"></i> Proceed to Verified Payment
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
