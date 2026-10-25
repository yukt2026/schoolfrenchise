@extends('layouts.app')

@section('title', 'Photo & Event Gallery | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Campus & Practical Lab Gallery</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Gallery</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill uppercase mb-2">Moments of Excellence</span>
                <h2 class="fw-extrabold display-6">Life at {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h2>
                <p class="text-muted">Glimpses of computer labs, practical sessions, convocation ceremonies, and skill workshops.</p>
            </div>

            <!-- Categories Badges -->
            @if($categories->isNotEmpty())
            <div class="d-flex justify-content-center flex-wrap gap-2 mb-5">
                <button type="button" class="btn btn-primary btn-sm rounded-2 px-4 filter-btn active" data-filter="all">All Photos</button>
                @foreach($categories as $cat)
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-2 px-4 filter-btn" data-filter="{{ Str::slug($cat) }}">{{ $cat }}</button>
                @endforeach
            </div>
            @endif

            <!-- Gallery Grid -->
            <div class="row g-2.5 g-sm-3 g-lg-4" id="galleryContainer">
                @foreach($items as $item)
                    <div class="col-6 col-lg-4 gallery-item" data-category="{{ Str::slug($item->category) }}">
                        <div class="card border border-light-subtle shadow-sm rounded-4 overflow-hidden h-100 transition-all">
                            <div class="position-relative dce-gallery-thumb" style="min-height: 140px; background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%);">
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white p-2.5 p-md-3 text-center">
                                    <i class="fa-solid fa-camera-retro fs-3 fs-md-2 mb-1 mb-md-2 text-info opacity-75"></i>
                                    <h6 class="fw-bold mb-0 text-white small fs-md-6">{{ $item->title }}</h6>
                                    <span class="badge bg-warning text-dark mt-1 mt-md-2" style="font-size: 0.65rem;">{{ $item->category }}</span>
                                </div>
                            </div>
                            <div class="card-body p-2.5 p-md-3 bg-white">
                                <h6 class="fw-bold text-dark mb-1 small fs-md-6 text-truncate">{{ $item->title }}</h6>
                                <small class="text-muted d-block text-truncate" style="font-size: 0.72rem;"><i class="fa-solid fa-tag text-primary me-1"></i> {{ $item->category }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active', 'btn-primary'));
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.add('btn-outline-secondary'));
                this.classList.add('active', 'btn-primary');
                this.classList.remove('btn-outline-secondary');

                const filter = this.getAttribute('data-filter');
                document.querySelectorAll('.gallery-item').forEach(item => {
                    if (filter === 'all' || item.getAttribute('data-category') === filter) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
