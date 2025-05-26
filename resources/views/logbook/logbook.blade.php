@extends('layout.main')

@section('container')
<div class="container-fluid px-3 px-lg-4 py-3 py-lg-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="row g-3 align-items-center justify-content-between">
                <div class="col-12 col-md-3">
                    <h4 class="mb-0 fs-5">Reagen Logbook</h4>
                </div>
                <div class="col-12 col-md-6">
                    <form action="{{ route('logbook.index') }}" method="GET">
                        <div class="search-wrapper ms-md-auto" style="max-width: 500px;">
                            <div class="position-relative">
                                <input type="text" 
                                       class="form-control form-control-sm ps-4" 
                                       name="keyword" 
                                       placeholder="Search reagen..."
                                       value="{{ request('keyword') }}">
                                <button class="btn btn-sm search-button" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0 p-lg-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="d-none d-lg-table-cell" width="5%">No</th>
                            <th width="20%">Catalog</th>
                            <th width="30%">Name</th>
                            <th class="d-none d-lg-table-cell" width="15%">Brand</th>
                            <th width="10%">Stock</th>
                            <th width="25%">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="fs-sm">
                        @foreach($reagens as $index => $reagen)
                            <tr>
                                <td class="d-none d-lg-table-cell">{{ $reagens->firstItem() + $index }}</td>
                                <td>
                                    <div class="text-secondary small">{{ $reagen->noCatalog }}</div>
                                    <div class="d-lg-none text-muted smaller">{{ $reagen->merk }}</div>
                                </td>
                                <td class="fw-medium">{{ $reagen->nameReagen }}</td>
                                <td class="d-none d-lg-table-cell">{{ $reagen->merk }}</td>
                                <td>
                                    <span class="badge bg-{{ $reagen->stockReagen && $reagen->stockReagen->quantity > 0 ? 'success' : 'danger' }}">
                                        {{ $reagen->stockReagen ? $reagen->stockReagen->quantity : '0' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('data.history', ['noCatalog' => $reagen->noCatalog]) }}" 
                                           class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                                            <i class="bi bi-clock-history"></i>
                                            <span class="btn-text">History</span>
                                        </a>
                                        <a href="{{ route('data.take', ['noCatalog' => $reagen->noCatalog]) }}" 
                                           class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                            <i class="bi bi-box-arrow-right"></i>
                                            <span class="btn-text">Take</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center p-3">
                {{ $reagens->links() }}
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}
.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}
.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}
.btn-sm {
    padding: 0.4rem 0.8rem;
}
.search-wrapper {
    width: 100%;
}

.search-wrapper .form-control {
    height: 38px;
    border-radius: 20px;
    padding-right: 40px;
    border: 1px solid #dee2e6;
    background: #f8f9fa;
}

.search-wrapper .form-control:focus {
    background: #fff;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
    border-color: #86b7fe;
}

.search-button {
    position: absolute;
    right: 3px;
    top: 3px;
    bottom: 3px;
    border: none;
    background: transparent;
    color: #6c757d;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.search-button:hover {
    background: #e9ecef;
    color: #0d6efd;
}

@media (max-width: 768px) {
    .table {
        font-size: 0.875rem;
    }
    .btn-text {
        font-size: 0.75rem;
        white-space: nowrap;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        line-height: 1.2;
    }
    .search-wrapper .form-control {
        height: 34px;
        font-size: 0.875rem;
    }
    
    .search-button {
        width: 28px;
        height: 28px;
    }
}
.fs-sm {
    font-size: 0.875rem;
}
</style>
@endsection
