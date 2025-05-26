@extends('layout.main')

@section('container')
<div class="container-fluid px-4 py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Reagen Received History</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reagen In</li>
                </ol>
            </nav>
        </div>
        
        <div class="card-body">
            <div class="timeline-container">
                <!-- Header -->
                <div class="row fw-bold border-bottom pb-2 mb-3">
                    <div class="col-2"><small>Catalog Number</small></div>
                    <div class="col-3"><small>Reagen Name</small></div>
                    <div class="col-2"><small>Batch</small></div>
                    <div class="col-2"><small>Quantity</small></div>
                    <div class="col-3"><small>Received Date</small></div>
                </div>

                <!-- Timeline Items -->
                @foreach ($paginatedData as $date => $items)
                <div class="timeline-item mb-4">
                    <div class="timeline-date">
                        <span class="badge bg-info">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
                    </div>
                    <div class="timeline-content mt-2">
                        @foreach ($items as $reagen)
                        <div class="card mb-2 border-0 hover-shadow">
                            <div class="card-body py-2">
                                <a href="{{ route('data.view', ['noCatalog' => $reagen->noCatalog]) }}" 
                                   class="text-decoration-none text-dark">
                                    <div class="row align-items-center">
                                        <div class="col-2 text-secondary">{{ $reagen->noCatalog }}</div>
                                        <div class="col-3 fw-medium">{{ $reagen->reagen->nameReagen }}</div>
                                        <div class="col-2">{{ $reagen->batch }}</div>
                                        <div class="col-2">
                                            <span class="badge bg-info">
                                                <i class="bi bi-box-seam me-1"></i>
                                                {{ $reagen->quantity }}
                                            </span>
                                        </div>
                                        <div class="col-3">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3"></i> 
                                                {{ $reagen->created_at->format('d M Y H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $paginatedData->links() }}
            </div>
        </div>
    </div>
</div>

<style>
.timeline-container {
    position: relative;
    padding: 1rem;
}

.timeline-item {
    position: relative;
    padding-left: 1rem;
    border-left: 2px solid #e9ecef;
}

.timeline-date {
    position: relative;
    margin-left: -1.5rem;
}

.timeline-content {
    padding-left: 1rem;
}

.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    background-color: #f8f9fa !important;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.badge {
    padding: 0.5em 0.8em;
}
</style>
@endsection
