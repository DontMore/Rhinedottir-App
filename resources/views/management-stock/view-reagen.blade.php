@extends('layout.main')

@section('container')
<div class="container-fluid px-4 py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Reagen Details</h5>
            <div class="d-flex gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <a href="{{ route('data.edit', ['noCatalog' => $data->noCatalog]) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Catalog Number</span>
                            <strong>{{ $data->noCatalog }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Reagen Name</span>
                            <strong>{{ $data->nameReagen }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Brand</span>
                            <strong>{{ $data->merk }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Pack Size</span>
                            <strong>{{ $data->packSize }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">MSDS</span>
                            <a href="{{ $data->msds }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i> View MSDS
                            </a>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Price</span>
                            <strong>Rp {{ number_format((float)$data->price, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Hazard Symbols</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
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
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Stock History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Batch Number</th>
                            <th>Quantity</th>
                            <th>Expired Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reagenIn as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                                <td>{{ $item->batch }}</td>
                                <td><span class="badge bg-success">{{ $item->quantity }}</span></td>
                                <td>
                                    <span class="badge bg-{{ \Carbon\Carbon::parse($item->expiredDate)->isPast() ? 'danger' : 'info' }}">
                                        {{ \Carbon\Carbon::parse($item->expiredDate)->format('d M Y') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-primary btn-sm me-1 btn-show-modal" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#exampleModal"
                                            data-expired="{{ \Carbon\Carbon::parse($item->expiredDate)->format('d F Y') }}"
                                            data-id="{{ $item->Id }}">
                                        <i class="bi bi-qr-code"></i> Label
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm d-inline-flex align-items-center gap-1" 
                                            onclick="confirmDelete({{ $item->Id }})">
                                        <i class="bi bi-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                    <form id="delete-form-{{ $item->Id }}" 
                                          action="{{ route('management-stock.delete-stock', $item->Id) }}" 
                                          method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center p-3">
                {{ $reagenIn->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Label Reagen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3" id="modalBody">
        <div class="label-container">
            <div class="label-qr">
                <div id="qrcode"></div>
            </div>
            <div class="label-content">
                <div class="label-header">
                    <div class="company-logo">
                        <img src="{{ asset('images/logo_b7.png') }}" alt="Logo" class="img-fluid">
                    </div>
                    <div class="company-name">LABORATORIUM QC-ANDEV</div>
                </div>
                <div class="label-info">
                    <div class="info-row">
                        <span class="info-label">Nama Reagen</span>
                        <span class="info-value">: {{ $data->nameReagen }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Expired Date</span>
                        <span class="info-value">: <span id="expired"></span></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tanggal Buka</span>
                        <span class="info-value">: </span>
                    </div>
                </div>
                <div class="label-footer">
                    Distribution List - Lampiran 1:WI-QO-QC-1018.02
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" id="downloadBtn" class="btn btn-primary">
            <i class="bi bi-download"></i> Download Label
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
$(document).ready(function() {
    $(".btn-show-modal").on('click', function() {
        const expired = $(this).data('expired');
        const id = $(this).data('id');
        
        $("#expired").text(expired);
        $("#qrcode").empty();
        
        // Using qrcode-generator library
        var typeNumber = 4;
        var errorCorrectionLevel = 'L';
        var qr = qrcode(typeNumber, errorCorrectionLevel);
        qr.addData("https://reagen.onexternal.com/qrcode/" + id);
        qr.make();
        $("#qrcode").html(qr.createImgTag(6));
    });
    
    $("#downloadBtn").on('click', function() {
        const element = document.getElementById('modalBody');
        html2canvas(element, {
            scale: 2,
            backgroundColor: '#ffffff',
            logging: false
        }).then(canvas => {
            const image = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.download = 'reagen_label.png';
            link.href = image;
            link.click();
        });
    });
});
</script>
@endpush

<style>
.card {
    border: none;
    border-radius: 10px;
}
.list-group-item {
    padding: 1rem;
    border-left: 0;
    border-right: 0;
}
.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}
.badge {
    padding: 0.5em 0.8em;
}
.modal-content {
    border: none;
    border-radius: 10px;
}
.hazard-icon {
    width: 80px;
    height: 80px;
    object-fit: contain;
    transition: transform 0.2s;
}
.hazard-icon:hover {
    transform: scale(1.1);
}

.label-container {
    display: flex;
    gap: 1rem;
    border: 1px solid #dee2e6;
    background: white;
}

.label-qr {
    padding: 1rem;
    border-right: 1px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
}

.label-qr img {
    width: 180px !important;
    height: 180px !important;
}

.label-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.label-header {
    display: flex;
    align-items: center;
    padding: 0.5rem;
    border-bottom: 1px solid #dee2e6;
}

.company-logo {
    width: 120px;
    padding: 0.5rem;
}

.company-name {
    flex: 1;
    font-weight: bold;
    text-align: center;
    font-size: 1.1rem;
}

.label-info {
    padding: 1rem;
    flex: 1;
    border-bottom: 1px solid #dee2e6;
}

.info-row {
    display: flex;
    margin-bottom: 0.5rem;
}

.info-label {
    width: 120px;
}

.label-footer {
    padding: 0.5rem;
    text-align: right;
    font-size: 0.875rem;
    color: #6c757d;
}

@media (max-width: 768px) {
    .label-container {
        flex-direction: column;
    }
    
    .label-qr {
        border-right: 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    .info-row {
        flex-direction: column;
    }
    
    .info-label {
        width: auto;
        font-weight: 500;
    }
    
    .company-name {
        font-size: 1rem;
    }
}
</style>

@endsection
