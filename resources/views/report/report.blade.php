@extends('layout.main')

@section('container')
<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">
            <i class="bi bi-file-earmark-text"></i> Report Management
        </h4>
    </div>

    <div class="row g-4">
        <!-- Logbook Report Card -->
        <div class="col-lg-6 col-xl-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-journal-text text-primary fs-2"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-0">Logbook Report</h5>
                        </div>
                    </div>
                    <p class="card-text text-muted small mb-4">
                        Laporan aktivitas penggunaan reagen untuk melacak progres dan memastikan akuntabilitas.
                    </p>
                    <a href="#" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Historical Report Card -->
        <div class="col-lg-6 col-xl-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clock-history text-success fs-2"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-0">Historical Report</h5>
                        </div>
                    </div>
                    <p class="card-text text-muted small mb-4">
                        Laporan riwayat penggunaan dan penerimaan reagen untuk analisis historis.
                    </p>
                    <a href="#" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Reagen List Card -->
        <div class="col-lg-6 col-xl-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-list-check text-info fs-2"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-0">Reagen List</h5>
                        </div>
                    </div>
                    <p class="card-text text-muted small mb-4">
                        Daftar lengkap reagen dengan detail katalog, nama, merk, dan ukuran kemasan.
                    </p>
                    <a href="#" class="btn btn-info btn-sm w-100 text-white">
                        <i class="bi bi-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Expired Reagen Card -->
        <div class="col-lg-6 col-xl-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle text-danger fs-2"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-0">Expired Reagen</h5>
                        </div>
                    </div>
                    <p class="card-text text-muted small mb-4">
                        Laporan reagen kedaluwarsa untuk pengelolaan inventaris yang lebih baik.
                    </p>
                    <a href="#" class="btn btn-danger btn-sm w-100">
                        <i class="bi bi-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s ease-in-out;
}
.card:hover {
    transform: translateY(-5px);
}
</style>
@endsection
