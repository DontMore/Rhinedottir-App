<!-- logbook-history.blade.php -->

@extends('layout.main')

@section('container')
<div class="container-fluid px-3 px-lg-4 py-3 py-lg-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Reagen Details</h5>
            <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="list-group mb-3">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Catalog Number</span>
                            <span class="fw-medium">{{ $reagen->noCatalog }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Reagent Name</span>
                            <span class="fw-medium">{{ $reagen->nameReagen }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Brand</span>
                            <span class="fw-medium">{{ $reagen->merk }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Current Stock</span>
                            <span class="badge bg-{{ optional($reagen->stockReagen)->quantity > 0 ? 'success' : 'danger' }} rounded-pill">
                                {{ optional($reagen->stockReagen)->quantity ?? '0' }}
                            </span>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Hazard Symbols</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @php
                                    $hazardOptions = explode(',', $reagen->hazardOptions);
                                @endphp
                                @foreach($hazardOptions as $hazard)
                                    <div class="col-4 text-center">
                                        @php
                                            $imagePath = match($hazard) {
                                                'Environment' => 'Environmental-Hazard',
                                                default => strtolower($hazard)
                                            };
                                        @endphp
                                        <img src="{{ asset('public/images/' . $imagePath . '.png') }}"
                                             alt="{{ $hazard }}"
                                             class="hazard-icon mb-2">
                                        <div class="small text-muted">{{ $hazard }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Batch Information</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach ($reagen->reagenIn as $stock)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <span class="badge bg-info">{{ $stock->batch }}</span>
                                        <small class="text-muted">
                                            Expires: {{ \Carbon\Carbon::parse($stock->expiredDate)->format('d M Y') }}
                                        </small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Usage History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Quantity</th>
                            <th>User</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logbookReagens as $logbook)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-calendar3 text-muted"></i>
                                        <span>{{ \Carbon\Carbon::parse($logbook->formatted_created_at)->format('d M Y H:i') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $logbook->quantity_taken }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-person text-muted"></i>
                                        <span>{{ $logbook->user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $logbook->note ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center p-3">
                {{ $logbookReagens->appends(['noCatalog' => $reagen->noCatalog])->links() }}
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}
.list-group-item {
    border-left: 0;
    border-right: 0;
    padding: 1rem;
}
.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}
.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}
.hazard-icon {
    width: 60px;
    height: 60px;
    object-fit: contain;
    transition: transform 0.2s;
}
.hazard-icon:hover {
    transform: scale(1.1);
}
</style>
@endsection
