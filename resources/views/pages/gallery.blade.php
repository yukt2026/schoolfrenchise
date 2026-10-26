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
                    @php
                        $badgeClass = match($item->category) {
                            'Certificates' => 'bg-warning text-dark',
                            'Students'     => 'bg-success text-white',
                            'Computer Lab' => 'bg-primary text-white',
                            'Events'       => 'bg-info text-dark',
                            default        => 'bg-dark bg-opacity-75 text-white',
                        };
                    @endphp
                    <div class="col-6 col-lg-4 gallery-item" data-category="{{ Str::slug($item->category) }}">
                        <div class="card border border-light-subtle shadow-sm rounded-4 overflow-hidden h-100 dce-gallery-card bg-white position-relative">
                            <div class="position-relative overflow-hidden" style="height: 220px; background-color: #0f172a; cursor: pointer;" onclick="openMainGalleryLightbox('{{ $item->image_url }}', '{{ addslashes($item->title) }}', '{{ addslashes($item->category) }}')">
                                <img src="{{ $item->image_url }}" 
                                     alt="{{ $item->title }}" 
                                     class="w-100 h-100 object-fit-cover dce-gallery-img"
                                     loading="lazy"
                                     onerror="this.onerror=null;this.src='https://placehold.co/600x400?text=TECH+ERA+Gallery';">
                                
                                <span class="badge position-absolute top-0 start-0 m-2.5 shadow-sm {{ $badgeClass }}" style="font-size: 0.7rem; font-weight: 700; border-radius: 6px;">
                                    {{ $item->category }}
                                </span>

                                <div class="dce-gallery-zoom-overlay">
                                    <span class="btn btn-light btn-sm rounded-circle shadow" style="width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-magnifying-glass-plus text-primary fs-5"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body p-2.5 p-md-3 bg-white d-flex flex-column justify-content-between">
                                <h6 class="fw-bold text-dark mb-1 small fs-md-6 text-truncate" title="{{ $item->title }}">{{ $item->title }}</h6>
                                <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.74rem;">
                                    <span><i class="fa-solid fa-tag text-primary me-1"></i> {{ $item->category }}</span>
                                    <span class="text-primary fw-semibold cursor-pointer" onclick="openMainGalleryLightbox('{{ $item->image_url }}', '{{ addslashes($item->title) }}', '{{ addslashes($item->category) }}')">
                                        Zoom <i class="fa-solid fa-arrow-up-right-from-square ms-0.5"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Modal for Gallery Lightbox -->
    <div class="modal fade" id="mainGalleryLightboxModal" tabindex="-1" aria-labelledby="mainGalleryLightboxTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 bg-dark text-white py-2.5 px-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark" id="mainGalleryModalBadge">Gallery</span>
                        <h6 class="modal-title fs-6 fw-bold text-white mb-0" id="mainGalleryLightboxTitle">Photo View</h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-black text-center position-relative" style="min-height: 280px; display: flex; align-items: center; justify-content: center;">
                    <img id="mainGalleryModalImage" src="" alt="" class="img-fluid" style="max-height: 75vh; width: auto; object-fit: contain;">
                </div>
                <div class="modal-footer border-0 bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                    <small class="text-muted"><i class="fa-solid fa-certificate text-warning me-1"></i> {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }} &bull; Verified Campus Photo</small>
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openMainGalleryLightbox(url, title, category) {
            const modalEl = document.getElementById('mainGalleryLightboxModal');
            if (!modalEl) return;
            const imgEl = document.getElementById('mainGalleryModalImage');
            const titleEl = document.getElementById('mainGalleryLightboxTitle');
            const badgeEl = document.getElementById('mainGalleryModalBadge');
            
            if (imgEl) imgEl.src = url;
            if (titleEl) titleEl.textContent = title;
            if (badgeEl) badgeEl.textContent = category;
            
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active', 'btn-primary'));
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.add('btn-outline-secondary'));
                this.classList.add('active', 'btn-primary');
                this.classList.remove('btn-outline-secondary');

                const filter = this.getAttribute('data-filter');
                document.querySelectorAll('.gallery-item').forEach(item => {
                    if (filter === 'all' || item.getAttribute('data-category') === filter) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
