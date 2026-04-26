@extends('layout.main')
@section('container')
<style>
    /* Reset default Select2 container width */
    .select2-container { width: 100% !important; }

    /* Selection Box */
    .select2-container--default .select2-selection--single {
        height: 42px; padding: 0 12px; border: 1px solid #e5e7eb; border-radius: 0.75rem; background: #ffffff; transition: all 0.2s ease;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px; padding-left: 0; color: #111827; font-size: 0.875rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; right: 12px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #9ca3af transparent transparent transparent; border-width: 5px 4px 0 4px; margin-left: -4px;
    }

    /* Dropdown */
    .select2-dropdown {
        border: 1px solid #e5e7eb; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.06); margin-top: 6px; overflow: hidden; padding: 4px;
    }
    .select2-search--dropdown .select2-search__field {
        padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 0.5rem; margin: 6px; width: calc(100% - 12px); font-size: 0.875rem; transition: all 0.2s;
    }
    .select2-search--dropdown .select2-search__field:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }

    /* Options */
    .select2-results__option { padding: 8px 10px; border-radius: 0.375rem; font-size: 0.875rem; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #eff6ff; color: #1d4ed8; }
    .select2-container--default .select2-results__option[aria-selected=true] { background-color: #f9fafb; color: #111827; }

    /* Focus & Clear */
    .select2-container--default.select2-container--focus .select2-selection--single { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
    .select2-container--default .select2-selection--single .select2-selection__clear { margin-right: 24px; color: #9ca3af; font-size: 1rem; }
</style>

@push('scripts')
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

        // ✅ Chart Update Function (Combined)
        function updateCombinedChart(noCatalog) {
            if (!noCatalog) return;
            const chartCanvas = document.getElementById('combinedReagentChart');
            chartCanvas.style.opacity = '0.5';

            Promise.all([
                $.get("{{ url('/chart-data') }}", { noCatalog: noCatalog }),
                $.get("{{ url('/logbook-chart-data') }}", { noCatalog: noCatalog })
            ]).then(function([reagenInData, reagenOutData]) {
                chartCanvas.style.opacity = '1';
                const ctx = chartCanvas.getContext('2d');
                if (combinedChartInstance) combinedChartInstance.destroy();

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
                        }, {
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
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: { top: 10, bottom: 10 } },
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 25,
                                    font: { size: 13, weight: '600', family: "'Inter', sans-serif" },
                                    color: '#4b5563'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1f2937',
                                titleFont: { size: 14, weight: 'bold', family: "'Inter', sans-serif" },
                                bodyFont: { size: 13, family: "'Inter', sans-serif" },
                                padding: 16,
                                cornerRadius: 12,
                                displayColors: true,
                                boxPadding: 8,
                                callbacks: {
                                    label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y} units`
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#9ca3af', font: { size: 12, weight: '500' }, padding: 10 }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f3f4f6', drawBorder: false },
                                ticks: { precision: 0, color: '#9ca3af', font: { size: 12, weight: '500' }, padding: 10 }
                            }
                        }
                    }
                });
            }).catch(err => {
                chartCanvas.style.opacity = '1';
                console.error(err);
            });
        }

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

        // 🔥 LOGIKA GRAFIK DEVIASI PEMAKAIAN REAGEN
        (function() {
            const actualUsageData = @json($dailyActualUsage ?? []);
            const daysInMonth = actualUsageData.length > 0 ? actualUsageData.length : 30;
            const daysLabels = Array.from({length: daysInMonth}, (_, i) => `Hari ${i + 1}`);
            let deviationChartInstance = null;

            function renderDeviationChart(target) {
                const ctx = document.getElementById('deviationChart')?.getContext('2d');
                if (!ctx) return;
                
                const deviations = actualUsageData.map(val => val - target);

                if (deviationChartInstance) {
                    deviationChartInstance.data.datasets[0].data = deviations;
                    deviationChartInstance.update('active');
                    return;
                }

                deviationChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: daysLabels,
                        datasets: [{
                            label: 'Deviasi',
                            data: deviations,
                            backgroundColor: ctx => ctx.raw >= 0 ? 'rgba(239, 68, 68, 0.75)' : 'rgba(16, 185, 129, 0.75)',
                            borderColor: ctx => ctx.raw >= 0 ? 'rgba(239, 68, 68, 1)' : 'rgba(16, 185, 129, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 500, easing: 'easeOutQuart' },
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#111827',
                                titleFont: { size: 13, weight: '600' },
                                bodyFont: { size: 12 },
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    title: items => `📅 ${items[0].label}`,
                                    label: function(context) {
                                        const target = parseInt(document.getElementById('targetSlider')?.value || 50);
                                        const actual = actualUsageData[context.dataIndex];
                                        const dev = context.raw;
                                        const isPositive = dev >= 0;
                                        
                                        return [
                                            `Pemakaian Aktual: ${actual} unit`,
                                            `Target Saat Ini: ${target} unit`,
                                            `Deviasi: ${isPositive ? '+' : ''}${dev} unit`,
                                            `📌 Kesimpulan: ${isPositive ? `⚠️ Boros sebanyak ${Math.abs(dev)}` : `✅ Hemat sebanyak ${Math.abs(dev)}`}`
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#9ca3af', font: { size: 11 }, maxTicksLimit: 15 }
                            },
                            y: {
                                beginAtZero: false,
                                grid: {
                                    color: ctx => ctx.tick.value === 0 ? 'rgba(17, 24, 39, 0.4)' : '#f3f4f6',
                                    lineWidth: ctx => ctx.tick.value === 0 ? 2.5 : 1,
                                    borderDash: ctx => ctx.tick.value === 0 ? [] : [2, 4]
                                },
                                ticks: {
                                    color: '#9ca3af',
                                    font: { size: 11, weight: '500' },
                                    callback: val => val === 0 ? 'TARGET (0)' : (val > 0 ? `+${val}` : `${val}`)
                                }
                            }
                        }
                    }
                });
            }

            const slider = document.getElementById('targetSlider');
            const display = document.getElementById('targetValueDisplay');

            if (slider && document.getElementById('deviationChart')) {
                renderDeviationChart(parseInt(slider.value));
                slider.addEventListener('input', function() {
                    const newTarget = parseInt(this.value);
                    if (display) display.textContent = newTarget;
                    renderDeviationChart(newTarget);
                });
            }
        })();
    });
</script>
@endpush

    {{-- 📊 Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
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

{{-- 📉 Grafik Deviasi Pemakaian Reagen (NEW) --}}
<div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm mt-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Grafik Deviasi Pemakaian Reagen</h2>
            <p class="text-sm text-gray-500 mt-1">Analisis selisih pemakaian aktual vs target harian</p>
        </div>
        
        {{-- Slider Kontrol Target --}}
        <div class="w-full sm:w-72 bg-gray-50 p-4 rounded-xl border border-gray-100">
            <label for="targetSlider" class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Target Pemakaian Harian</label>
            <div class="flex items-center justify-between mt-3">
                <span class="text-xs text-gray-400">0</span>
                <div class="text-center">
                    <span id="targetValueDisplay" class="text-2xl font-extrabold text-emerald-600">50</span>
                    <span class="text-xs text-gray-500 ml-1">unit</span>
                </div>
                <span class="text-xs text-gray-400">100</span>
            </div>
            <input type="range" id="targetSlider" min="0" max="100" value="50" step="1" 
                   class="w-full mt-2 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
        </div>
    </div>

    {{-- Canvas Grafik --}}
    <div class="h-96 w-full relative">
        <canvas id="deviationChart"></canvas>
    </div>
</div>

{{-- 📋 Tables Section --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
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
@endsection
