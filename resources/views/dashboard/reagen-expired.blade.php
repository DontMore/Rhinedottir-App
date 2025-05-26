@extends('layout.main')

@section('container')
<div class="container-fluid px-4 py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Expired Reagents List</h5>
                <div class="col-md-4">
                    <form action="{{ route('reagen.expired') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2" 
                               placeholder="Search reagen..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-light btn-sm">Search</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Catalog No.</th>
                            <th>Name</th>
                            <th>Batch</th>
                            <th>Qty</th>
                            <th>Expired Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reagenExpired as $index => $expired)
                        <tr>
                            <td>{{ $reagenExpired->firstItem() + $index }}</td>
                            <td>{{ $expired->noCatalog }}</td>
                            <td>{{ $expired->reagen ? $expired->reagen->nameReagen : 'Unknown' }}</td>
                            <td>{{ $expired->batch }}</td>
                            <td>{{ $expired->quantity }}</td>
                            <td>{{ $expired->expiredDate }}</td>
                            <td>
                                <span class="badge bg-{{ $expired->status_color }}">
                                    {{ $expired->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $reagenExpired->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
