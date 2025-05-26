@extends('layout.main')

@section('container')
<div class="container-fluid px-4 py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add New Reagen</h5>
            <a href="{{ route('management-stock.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
        
        <div class="card-body">
            <form action="add-reagen" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Catalog Number</label>
                            <input type="text" class="form-control" name="noCatalog" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Reagen Name</label>
                            <input type="text" class="form-control" name="nameReagen" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Brand</label>
                            <input type="text" class="form-control" name="merk" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Pack Size</label>
                            <input type="text" class="form-control" name="packSize" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label d-block mb-3">Hazard Symbol</label>
                        <div class="row g-3 mb-3">
                            @php
                            $hazards = [
                                ['id' => 1, 'name' => 'Toxic', 'image' => 'toxic.png'],
                                ['id' => 2, 'name' => 'Corrosive', 'image' => 'corrosive.png'],
                                ['id' => 3, 'name' => 'Explosive', 'image' => 'explosive.png'],
                                ['id' => 4, 'name' => 'Carcinogen', 'image' => 'carcinogen.png'],
                                ['id' => 5, 'name' => 'Environment', 'image' => 'Environmental-Hazard.png'],
                                ['id' => 6, 'name' => 'Flammable', 'image' => 'flammable.png'],
                                ['id' => 7, 'name' => 'Irritant', 'image' => 'irritant.png'],
                                ['id' => 8, 'name' => 'Oxidising', 'image' => 'oxidising.png']
                            ];
                            @endphp

                            @foreach($hazards as $hazard)
                            <div class="col-6 col-md-3">
                                <div class="form-check custom-checkbox">
                                    <input type="checkbox" class="form-check-input" 
                                           id="hazard{{ $hazard['id'] }}" 
                                           name="hazardOptions[]" 
                                           value="{{ $hazard['name'] }}">
                                    <label class="form-check-label" for="hazard{{ $hazard['id'] }}">
                                        <img src="{{ asset('public/images/' . $hazard['image']) }}" 
                                             alt="{{ $hazard['name'] }}" 
                                             class="img-fluid mb-2" 
                                             style="max-width: 80px;">
                                        <span class="d-block small">{{ $hazard['name'] }}</span>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">MSDS</label>
                            <input type="text" class="form-control" name="msds" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="price" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light">Reset</button>
                            <button type="submit" class="btn btn-primary">Save Reagen</button>
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
.form-check-label {
    cursor: pointer;
    text-align: center;
}
.form-check-input:checked ~ .form-check-label img {
    border: 2px solid #0d6efd;
    padding: 3px;
    border-radius: 8px;
}
.form-check {
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.form-check-input {
    margin: 0;
    position: absolute;
    opacity: 0;
}
</style>
@endsection
