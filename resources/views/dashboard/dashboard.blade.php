@extends('layout.main')

@section('container')
<!-- Move scripts to head section with proper loading order -->
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    let combinedChartInstance = null;

    // Fetch and populate the reagent dropdown
    $.get("{{ url('/reagent-list') }}", function(data) {
        const select = $('#reagentSelect');
        data.forEach(function(reagent) {
            select.append(new Option(reagent.nameReagen, reagent.noCatalog));
        });
    });

    // Combined Chart Update Function
    function updateCombinedChart(noCatalog, nameReagen) {
        Promise.all([
            $.get("{{ url('/chart-data') }}", { noCatalog: noCatalog }),
            $.get("{{ url('/logbook-chart-data') }}", { noCatalog: noCatalog })
        ]).then(function([reagenInData, reagenOutData]) {
            const ctx = document.getElementById('combinedReagentChart').getContext('2d');
            
            if (combinedChartInstance) {
                combinedChartInstance.destroy();
            }

            const labels = reagenInData.map(item => item.month);
            const inQuantities = reagenInData.map(item => item.total_quantity);
            const outQuantities = reagenOutData.map(item => item.total_quantity);

            combinedChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Reagen In',
                        data: inQuantities,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Reagen Out',
                        data: outQuantities,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    layout: {
                        padding: {
                            left: 10,
                            right: 10
                        }
                    }
                }
            });
        });
    }

    // Event Listener
    $('#reagentSelect').change(function() {
        const noCatalog = $(this).val();
        const nameReagen = $(this).find('option:selected').text();
        updateCombinedChart(noCatalog, nameReagen);
    });
});
</script>
@endpush

<style>
    .stats-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .stats-icon {
        font-size: 2.5rem;
        opacity: 0.7;
    }
    .stats-card h2 {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .stats-card p {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0;
    }
    .table-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .table-card .card-body {
        padding: 1.5rem;
    }
    .table-card p {
        font-size: 1.1rem;
        font-weight: 500;
        color: #495057;
    }
    .chart-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .reagenSelectStyle {
        width: 100%;
        padding: 0.75rem;
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        font-size: 0.9rem;
    }
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
        margin: 20px 0;
    }
    canvas {
        width: 100% !important;
        height: 100% !important;
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stats-card bg-primary bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="text-primary">{{ $totalReagen }}</h2>
                            <p>Reagent Variants</p>
                        </div>
                        <i class="bi bi-boxes stats-icon text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-xl-3">
            <div class="card stats-card bg-success bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="text-success">{{ $totalQuantity ?? 0 }}</h2>
                            <p>Total Reagent</p>
                        </div>
                        <i class="bi bi-clipboard2-data stats-icon text-success"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stats-card bg-info bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="text-info">{{ $totalQuantityIn ?? 0 }}</h2>
                            <p>Reagent In</p>
                        </div>
                        <i class="bi bi-box-arrow-in-down stats-icon text-info"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stats-card bg-warning bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="text-warning">{{ $totalQuantityOut ?? 0 }}</h2>
                            <p>Reagent Taken</p>
                        </div>
                        <i class="bi bi-box-arrow-right stats-icon text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card chart-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Reagen Analysis</h5>
                    <select id="reagentSelect" class="reagenSelectStyle">
                        <option value="">Select Reagent</option>
                    </select>
                    <div class="chart-container">
                        <canvas id="combinedReagentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card table-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Zero Stock</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Catalog No.</th>
                                    <th>Name</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($zeroStockReagen as $item)
                                <tr>
                                    <td>{{ $item->noCatalog }}</td>
                                    <td>{{ $item->reagen->nameReagen }}</td>
                                    <td>{{ $item->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card table-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Reagen Order</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Catalog No.</th>
                                    <th>Name</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reagenOrder as $order)
                                <tr>
                                    <td>{{ $order->noCatalog }}</td>
                                    <td>{{ $order->nameReagen }}</td>
                                    <td>{{ $order->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card table-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Regent Expired</h5>
                        <a href="{{ route('reagen.expired') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye"></i> View All
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Catalog No.</th>
                                    <th>Name</th>
                                    <th>Qty</th>
                                    <th>Expired Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reagenED as $expired)
                                <tr>
                                    <td>{{ $expired->noCatalog }}</td>
                                    <td>{{ $expired->reagen ? $expired->reagen->nameReagen : 'Unknown' }}</td>
                                    <td>{{ $expired->quantity }}</td>
                                    <td>{{ $expired->expiredDate}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
