@extends('layout.main')

@section('container')
<div class="container-fluid px-4 py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Stock Opname Data List</h4>
        </div>
        
        <div class="card-body">
            <!-- Search Form -->
            <form action="" method="GET" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="month" class="form-label">Month</label>
                        <select class="form-select" name="month" id="month">
                            <option value="">All Months</option>
                            @foreach(range(1, 12) as $month)
                                <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create()->month($month)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-select" name="year" id="year">
                            <option value="">All Years</option>
                            @foreach(range(date('Y'), date('Y')-5) as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Search
                        </button>
                        <a href="{{ route('stock.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="30%">Month</th>
                            <th width="25%">Year</th>
                            <th width="20%">Status</th>
                            <th width="20%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $item->month_name }}</td>
                                <td>{{ $item->year }}</td>
                                <td>
                                    <span class="badge {{ $item->status == 1 ? 'bg-success' : 'bg-warning' }}">
                                        {{ $item->status == 1 ? 'Complete' : 'Not Complete' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('soDetail', ['month' => $item->month, 'year' => $item->year]) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (min-width: 1200px) {
        .container-fluid {
            max-width: 95%;
        }
    }
</style>
@endsection
