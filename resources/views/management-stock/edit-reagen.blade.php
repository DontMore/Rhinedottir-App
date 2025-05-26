@extends('layout.main')

@section('container')
<div class="container-fluid px-4 py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Reagen</h5>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
        
        <div class="card-body">
            <form method="POST" action="{{ route('data.update', $data->noCatalog) }}">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Catalog Number</label>
                            <input type="text" class="form-control bg-light" name="noCatalog" value="{{ $data->noCatalog }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Reagen Name</label>
                            <input type="text" class="form-control" name="nameReagen" value="{{ $data->nameReagen }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Brand</label>
                            <input type="text" class="form-control" name="merk" value="{{ $data->merk }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Pack Size</label>
                            <input type="text" class="form-control" name="packSize" value="{{ $data->packSize }}" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label d-block mb-3">Hazard Symbols</label>
                        <div class="hazard-symbols-container">
                            <!-- Toxic Symbol -->
                            <div class="hazard-symbol-item">
                                <input type="checkbox" class="hazard-checkbox" 
                                       id="customCheck1" name="hazardOptions[]" value="Toxic" 
                                       {{ in_array('Toxic', $hazardOptions) ? 'checked' : '' }}>
                                <label class="hazard-label" for="customCheck1">
                                    <img src="{{ asset('public/images/toxic.png') }}" alt="Toxic">
                                    <span>Toxic</span>
                                </label>
                            </div>

                            <!-- Corrosive Symbol -->
                            <div class="hazard-symbol-item">
                                <input type="checkbox" class="hazard-checkbox" 
                                       id="customCheck2" name="hazardOptions[]" value="Corrosive" 
                                       {{ in_array('Corrosive', $hazardOptions) ? 'checked' : '' }}>
                                <label class="hazard-label" for="customCheck2">
                                    <img src="{{ asset('public/images/corrosive.png') }}" alt="Corrosive">
                                    <span>Corrosive</span>
                                </label>
                            </div>

                            <!-- Explosive Symbol -->
                            <div class="hazard-symbol-item">
                                <input type="checkbox" class="hazard-checkbox" 
                                       id="customCheck3" name="hazardOptions[]" value="Explosive" 
                                       {{ in_array('Explosive', $hazardOptions) ? 'checked' : '' }}>
                                <label class="hazard-label" for="customCheck3">
                                    <img src="{{ asset('public/images/explosive.png') }}" alt="Explosive">
                                    <span>Explosive</span>
                                </label>
                            </div>

                            <!-- Carcinogen Symbol -->
                            <div class="hazard-symbol-item">
                                <input type="checkbox" class="hazard-checkbox" 
                                       id="customCheck4" name="hazardOptions[]" value="Carcinogen" 
                                       {{ in_array('Carcinogen', $hazardOptions) ? 'checked' : '' }}>
                                <label class="hazard-label" for="customCheck4">
                                    <img src="{{ asset('public/images/carcinogen.png') }}" alt="Carcinogen">
                                    <span>Carcinogen</span>
                                </label>
                            </div>

                            <!-- Environment Symbol -->
                            <div class="hazard-symbol-item">
                                <input type="checkbox" class="hazard-checkbox" 
                                       id="customCheck5" name="hazardOptions[]" value="Environment" 
                                       {{ in_array('Environment', $hazardOptions) ? 'checked' : '' }}>
                                <label class="hazard-label" for="customCheck5">
                                    <img src="{{ asset('public/images/Environmental-Hazard.png') }}" alt="Environment">
                                    <span>Environment</span>
                                </label>
                            </div>

                            <!-- Flammable Symbol -->
                            <div class="hazard-symbol-item">
                                <input type="checkbox" class="hazard-checkbox" 
                                       id="customCheck6" name="hazardOptions[]" value="Flammable" 
                                       {{ in_array('Flammable', $hazardOptions) ? 'checked' : '' }}>
                                <label class="hazard-label" for="customCheck6">
                                    <img src="{{ asset('public/images/flammable.png') }}" alt="Flammable">
                                    <span>Flammable</span>
                                </label>
                            </div>

                            <!-- Irritant Symbol -->
                            <div class="hazard-symbol-item">
                                <input type="checkbox" class="hazard-checkbox" 
                                       id="customCheck7" name="hazardOptions[]" value="Irritant" 
                                       {{ in_array('Irritant', $hazardOptions) ? 'checked' : '' }}>
                                <label class="hazard-label" for="customCheck7">
                                    <img src="{{ asset('public/images/irritant.png') }}" alt="Irritant">
                                    <span>Irritant</span>
                                </label>
                            </div>

                            <!-- Oxidising Symbol -->
                            <div class="hazard-symbol-item">
                                <input type="checkbox" class="hazard-checkbox" 
                                       id="customCheck8" name="hazardOptions[]" value="Oxidising" 
                                       {{ in_array('Oxidising', $hazardOptions) ? 'checked' : '' }}>
                                <label class="hazard-label" for="customCheck8">
                                    <img src="{{ asset('public/images/oxidising.png') }}" alt="Oxidising">
                                    <span>Oxidising</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">MSDS Link</label>
                            <input type="url" class="form-control" name="msds" value="{{ $data->msds }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="price" value="{{ $data->price }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light">Reset</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}
.custom-option {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}
.custom-option:hover {
    background-color: #f8f9fa;
}
.form-check-input:checked ~ label .custom-option {
    border-color: #0d6efd;
    background-color: #e7f1ff;
}

.hazard-symbols-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
}

.hazard-symbol-item {
    position: relative;
    text-align: center;
}

.hazard-checkbox {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.hazard-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.5rem;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.hazard-label img {
    width: 80px;
    height: 80px;
    margin-bottom: 0.5rem;
    transition: transform 0.2s ease;
}

.hazard-label span {
    font-size: 0.875rem;
    color: #6c757d;
}

.hazard-checkbox:checked + .hazard-label {
    border-color: #0d6efd;
    background-color: #e7f1ff;
}

.hazard-checkbox:checked + .hazard-label img {
    transform: scale(1.1);
}

.hazard-label:hover {
    background-color: #e9ecef;
}

@media (max-width: 768px) {
    .hazard-symbols-container {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    }
    
    .hazard-label img {
        width: 60px;
        height: 60px;
    }
}
</style>
@endsection
