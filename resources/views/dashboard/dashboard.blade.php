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

<div class="p-6 space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-blue-50 rounded-xl shadow-sm transition-transform duration-200 hover:-translate-y-1">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-semibold text-blue-600">{{ $totalReagen }}</h2>
                        <p class="text-gray-600 text-sm">Reagent Variants</p>
                    </div>
                    <i class="bi bi-boxes text-4xl opacity-70 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-green-50 rounded-xl shadow-sm transition-transform duration-200 hover:-translate-y-1">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-semibold text-green-600">{{ $totalQuantity ?? 0 }}</h2>
                        <p class="text-gray-600 text-sm">Total Reagent</p>
                    </div>
                    <i class="bi bi-clipboard2-data text-4xl opacity-70 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-cyan-50 rounded-xl shadow-sm transition-transform duration-200 hover:-translate-y-1">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-semibold text-cyan-600">{{ $totalQuantityIn ?? 0 }}</h2>
                        <p class="text-gray-600 text-sm">Reagent In</p>
                    </div>
                    <i class="bi bi-box-arrow-in-down text-4xl opacity-70 text-cyan-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-amber-50 rounded-xl shadow-sm transition-transform duration-200 hover:-translate-y-1">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-semibold text-amber-600">{{ $totalQuantityOut ?? 0 }}</h2>
                        <p class="text-gray-600 text-sm">Reagent Taken</p>
                    </div>
                    <i class="bi bi-box-arrow-right text-4xl opacity-70 text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6">
            <h5 class="text-lg font-medium mb-4">Reagen Analysis</h5>
            <select id="reagentSelect" class="w-full p-3 mb-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select Reagent</option>
            </select>
            <div class="h-[400px] w-full">
                <canvas id="combinedReagentChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Zero Stock Table -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6">
                <h5 class="text-lg font-medium mb-4">Zero Stock</h5>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catalog No.</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($zeroStockReagen as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm">{{ $item->noCatalog }}</td>
                                <td class="px-4 py-2 text-sm">{{ $item->reagen->nameReagen }}</td>
                                <td class="px-4 py-2 text-sm">{{ $item->quantity }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Reagen Order Table -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6">
                <h5 class="text-lg font-medium mb-4">Reagen Order</h5>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catalog No.</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($reagenOrder as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm">{{ $order->noCatalog }}</td>
                                <td class="px-4 py-2 text-sm">{{ $order->nameReagen }}</td>
                                <td class="px-4 py-2 text-sm">{{ $order->quantity }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Expired Reagent Table -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h5 class="text-lg font-medium">Regent Expired</h5>
                    <a href="{{ route('reagen.expired') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        <i class="bi bi-eye mr-2"></i> View All
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catalog No.</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expired Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($reagenED as $expired)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm">{{ $expired->noCatalog }}</td>
                                <td class="px-4 py-2 text-sm">{{ $expired->reagen ? $expired->reagen->nameReagen : 'Unknown' }}</td>
                                <td class="px-4 py-2 text-sm">{{ $expired->quantity }}</td>
                                <td class="px-4 py-2 text-sm">{{ $expired->expiredDate }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
