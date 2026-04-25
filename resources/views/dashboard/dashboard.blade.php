@extends('layout.main')
@section('container')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* ✅ Modern Select2 Styling agar menyatu dengan Tailwind */
    .select2-container--default .select2-selection--single {
        height: 42px;
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        background-color: #fff;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px;
        padding-left: 0;
        color: #111827;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 10px;
    }

    .select2-dropdown {
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        margin-top: 4px;
        overflow: hidden;
    }

    .select2-results__option {
        padding: 8px 12px;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6;
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        let combinedChartInstance = null;

        // ✅ Fetch & Populate Dropdown
        $.get("{{ url('/reagent-list') }}", function(data) {
            const select = $('#reagentSelect');
            select.empty().append('<option value="">Select Reagent</option>');
            data.forEach(function(reagent) {
                select.append(new Option(reagent.nameReagen, reagent.noCatalog));
            });

            select.select2({
                placeholder: 'Search or select reagent...',
                allowClear: true,
                width: '100%'
            });
        });

        // ✅ Chart Update Function
        function updateCombinedChart(noCatalog) {
            if (!noCatalog) return;

            // Show loading state if needed (optional)
            const chartCanvas = document.getElementById('combinedReagentChart');
            chartCanvas.style.opacity = '0.5';

            Promise.all([
                $.get("{{ url('/chart-data') }}", {
                    noCatalog: noCatalog
                }),
                $.get("{{ url('/logbook-chart-data') }}", {
                    noCatalog: noCatalog
                })
            ]).then(function([reagenInData, reagenOutData]) {
                chartCanvas.style.opacity = '1';
                const ctx = chartCanvas.getContext('2d');
                if (combinedChartInstance) combinedChartInstance.destroy();

                // Create Gradients
                const gradientIn = ctx.createLinearGradient(0, 0, 0, 400);
                gradientIn.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
                gradientIn.addColorStop(1, 'rgba(16, 185, 129, 0)');

                const gradientOut = ctx.createLinearGradient(0, 0, 0, 400);
                gradientOut.addColorStop(0, 'rgba(239, 68, 68, 0.15)');
                gradientOut.addColorStop(1, 'rgba(239, 68, 68, 0)');

                combinedChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: reagenInData.map(item => item.month),
                        datasets: [{
                                label: 'Stock Inflow',
                                data: reagenInData.map(item => item.total_quantity),
                                borderColor: '#10b981',
                                borderWidth: 3,
                                backgroundColor: gradientIn,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#10b981',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointHoverBackgroundColor: '#10b981',
                                pointHoverBorderColor: '#fff',
                                pointHoverBorderWidth: 2
                            },
                            {
                                label: 'Stock Usage',
                                data: reagenOutData.map(item => item.total_quantity),
                                borderColor: '#ef4444',
                                borderWidth: 3,
                                backgroundColor: gradientOut,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#ef4444',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointHoverBackgroundColor: '#ef4444',
                                pointHoverBorderColor: '#fff',
                                pointHoverBorderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                top: 10,
                                bottom: 10
                            }
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 25,
                                    font: {
                                        size: 13,
                                        weight: '600',
                                        family: "'Inter', sans-serif"
                                    },
                                    color: '#4b5563'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1f2937',
                                titleFont: {
                                    size: 14,
                                    weight: 'bold',
                                    family: "'Inter', sans-serif"
                                },
                                bodyFont: {
                                    size: 13,
                                    family: "'Inter', sans-serif"
                                },
                                padding: 16,
                                cornerRadius: 12,
                                displayColors: true,
                                boxPadding: 8,
                                callbacks: {
                                    label: function(context) {
                                        return ` ${context.dataset.label}: ${context.parsed.y} units`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#9ca3af',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    padding: 10
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f3f4f6',
                                    drawBorder: false
                                },
                                ticks: {
                                    precision: 0,
                                    color: '#9ca3af',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    padding: 10
                                }
                            }
                        }
                    }
                });
            }).catch(err => {
                chartCanvas.style.opacity = '1';
                console.error("Chart fetch error:", err);
            });
        }

        // ✅ Event Listener
        $('#reagentSelect').on('change', function() {
            const noCatalog = $(this).val();
            if (noCatalog) {
                updateCombinedChart(noCatalog);
            } else {
                if (combinedChartInstance) {
                    combinedChartInstance.destroy();
                    combinedChartInstance = null;
                }
            }
        });
    });
</script>
@endpush

<div class="space-y-6">
    {{-- 📊 Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
        $stats = [
            [
                'title' => 'Total Reagent',
                'value' => $totalQuantity ?? 0,
                'sub' => 'Current stock',
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-600',
                'icon' => 'bi-clipboard2-data'
            ],
            [
                'title' => 'Reagent In',
                'value' => $totalQuantityIn ?? 0,
                'sub' => 'This month',
                'bg' => 'bg-sky-50',
                'text' => 'text-sky-600',
                'icon' => 'bi-box-arrow-in-down'
            ],
            [
                'title' => 'Reagent Taken',
                'value' => $totalQuantityOut ?? 0,
                'sub' => 'This month',
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-600',
                'icon' => 'bi-box-arrow-right'
            ],
            [
                'title' => 'Reagent Variants',
                'value' => $totalReagen ?? 0,
                'sub' => 'Unique types',
                'bg' => 'bg-violet-50',
                'text' => 'text-violet-600',
                'icon' => 'bi-collection'
            ]
        ];
        @endphp

        @foreach($stats as $stat)
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">{{ $stat['title'] }}</p>
                    <h3 class="text-3xl font-extrabold {{ $stat['text'] }} mt-2 tracking-tight">{{ number_format($stat['value']) }}</h3>
                    <p class="text-xs font-medium text-gray-400 mt-1 flex items-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 mr-1.5"></span>
                        {{ $stat['sub'] }}
                    </p>
                </div>
                <div class="p-4 {{ $stat['bg'] }} rounded-2xl group-hover:scale-110 transition-transform duration-300">
                    <i class="bi {{ $stat['icon'] }} text-2xl {{ $stat['text'] }}"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 📈 Chart Section --}}
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Reagent Analysis</h2>
                <p class="text-sm text-gray-500 mt-1">Comparison of reagent inflow and usage trends</p>
            </div>
            <div class="w-full sm:w-80">
                <select id="reagentSelect" class="w-full"></select>
            </div>
        </div>
        <div class="h-80 w-full rounded-2xl p-2">
            <canvas id="combinedReagentChart"></canvas>
        </div>
    </div>

    {{-- 📋 Tables Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Zero Stock --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-white sticky top-0 z-10">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Zero Stock</h3>
                    <p class="text-xs text-gray-500">Out of stock reagents</p>
                </div>
                <span class="px-2.5 py-1 text-xs font-bold bg-red-50 text-red-600 rounded-lg border border-red-100">{{ $zeroStockReagen->count() }}</span>
            </div>
            <div class="overflow-y-auto max-h-80 custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50/50 text-gray-500 uppercase text-[10px] tracking-widest font-bold">
                        <tr>
                            <th class="px-6 py-3">Catalog</th>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3 text-right">Qty</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($zeroStockReagen as $item)
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $item->noCatalog }}</td>
                            <td class="px-6 py-4 text-gray-900 font-semibold truncate max-w-[120px]">{{ $item->reagen->nameReagen }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center justify-center w-8 h-6 rounded-md text-[11px] font-bold bg-red-100 text-red-700">0</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center mb-3">
                                        <i class="bi bi-check2-circle text-2xl text-emerald-500"></i>
                                    </div>
                                    <p class="text-gray-400 font-medium">All stocks available</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Reagent Orders --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-white sticky top-0 z-10">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Recent Orders</h3>
                    <p class="text-xs text-gray-500">Pending procurement</p>
                </div>
                <span class="px-2.5 py-1 text-xs font-bold bg-blue-50 text-blue-600 rounded-lg border border-blue-100">{{ $reagenOrder->count() }}</span>
            </div>
            <div class="overflow-y-auto max-h-80 custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50/50 text-gray-500 uppercase text-[10px] tracking-widest font-bold">
                        <tr>
                            <th class="px-6 py-3">Catalog</th>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3 text-right">Qty</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($reagenOrder as $order)
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $order->noCatalog }}</td>
                            <td class="px-6 py-4 text-gray-900 font-semibold truncate max-w-[120px]">{{ $order->nameReagen }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center justify-center min-w-[32px] px-1.5 h-6 rounded-md text-[11px] font-bold bg-blue-100 text-blue-700">{{ $order->quantity }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                        <i class="bi bi-box text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-gray-400 font-medium">No pending orders</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Expiring Soon --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-white sticky top-0 z-10">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Expiring Soon</h3>
                    <p class="text-xs text-gray-500">Critical expiration dates</p>
                </div>
                <a href="{{ route('reagen.expired') }}" class="p-1.5 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors group">
                    <i class="bi bi-arrow-right text-gray-400 group-hover:text-blue-500"></i>
                </a>
            </div>
            <div class="overflow-y-auto max-h-80 custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50/50 text-gray-500 uppercase text-[10px] tracking-widest font-bold">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3 text-right">Exp. Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($reagenED as $expired)
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-gray-900 font-semibold truncate max-w-[150px]">{{ $expired->reagen ? $expired->reagen->nameReagen : 'Unknown' }}</span>
                                    <span class="text-[10px] text-gray-500 font-mono">{{ $expired->noCatalog }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-[11px] font-bold bg-orange-50 text-orange-700 border border-orange-100">
                                    {{ \Carbon\Carbon::parse($expired->expiredDate)->format('d M Y') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                        <i class="bi bi-clock text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-gray-400 font-medium">No expiring reagents</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection