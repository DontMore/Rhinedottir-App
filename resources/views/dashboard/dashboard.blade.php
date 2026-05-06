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

            // ✅ Pick random reagent on load
            if (data.length > 0) {
                const randomReagent = data[Math.floor(Math.random() * data.length)];
                select.val(randomReagent.noCatalog).trigger('change');
            }
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
                deviationChartModule.update(noCatalog);
                batchExpiryChartModule.update(noCatalog);
            } else {
                if (combinedChartInstance) {
                    combinedChartInstance.destroy();
                    combinedChartInstance = null;
                }
            }
        });

        // 🔥 LOGIKA GRAFIK DEVIASI PEMAKAIAN REAGEN
        const deviationChartModule = (function() {
            let deviationChartInstance = null;
            let currentUsageData = @json($monthlyActualUsage ?? []);
            let currentLabels = @json($monthLabels ?? []);
            
            function renderDeviationChart(usageData = null, labels = null) {
                const ctx = document.getElementById('deviationChart')?.getContext('2d');
                if (!ctx) return;
                
                if (usageData) currentUsageData = usageData;
                if (labels) currentLabels = labels;
                
                const count = currentUsageData.length;
                
                // Hitung Rata-rata (Mean)
                const sum = currentUsageData.reduce((a, b) => a + b, 0);
                const average = sum / (count || 1);
                
                // Hitung Deviasi dari Rata-rata (Baseline 0)
                const deviations = currentUsageData.map(val => (val - average).toFixed(2));

                if (deviationChartInstance) {
                    deviationChartInstance.data.labels = currentLabels;
                    deviationChartInstance.data.datasets[0].data = deviations;
                    deviationChartInstance.update('active');
                    return;
                }

                deviationChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: currentLabels,
                        datasets: [
                            {
                                label: 'Deviasi Pemakaian',
                                data: deviations,
                                borderColor: '#9ca3af',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: {
                                    target: 'origin',
                                    above: 'rgba(239, 68, 68, 0.15)', // Merah jika > rata-rata (Boros)
                                    below: 'rgba(16, 185, 129, 0.15)'  // Hijau jika < rata-rata (Hemat)
                                },
                                pointBackgroundColor: context => context.raw >= 0 ? '#ef4444' : '#10b981',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 800, easing: 'easeOutQuart' },
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#111827',
                                padding: 15,
                                cornerRadius: 10,
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    title: items => `📅 ${items[0].label}`,
                                    label: function(context) {
                                        const dev = context.raw;
                                        const actual = currentUsageData[context.dataIndex];
                                        const isAbove = dev >= 0;
                                        
                                        return [
                                            `Pemakaian: ${actual} unit`,
                                            `Rata-rata: ${average.toFixed(2)} unit`,
                                            `Deviasi: ${isAbove ? '+' : ''}${dev} unit dari rata-rata`,
                                            `📌 Status: ${isAbove ? '⚠️ Di Atas Rata-rata' : '✅ Di Bawah Rata-rata'}`
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#9ca3af', font: { size: 11 }, maxTicksLimit: 12 }
                            },
                            y: {
                                beginAtZero: false,
                                grid: {
                                    color: (ctx) => ctx.tick.value === 0 ? '#111827' : '#f3f4f6',
                                    lineWidth: (ctx) => ctx.tick.value === 0 ? 2 : 1,
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#9ca3af',
                                    font: { size: 11, weight: '500' },
                                    callback: function(value) {
                                        if (value === 0) return 'MEAN (0)';
                                        return (value > 0 ? '+' : '') + value;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            return {
                update: function(noCatalog) {
                    const chartCanvas = document.getElementById('deviationChart');
                    chartCanvas.style.opacity = '0.5';

                    $.get("{{ url('/deviation-chart-data') }}", { noCatalog: noCatalog }, function(data) {
                        chartCanvas.style.opacity = '1';
                        renderDeviationChart(data.values, data.labels);
                    });
                },
                init: function() {
                    renderDeviationChart();
                }
            };
        })();

        deviationChartModule.init();

        // 🔥 LOGIKA GRAFIK BATCH EXPIRY TIMELINE
        const batchExpiryChartModule = (function() {
            let chartInstance = null;

            return {
                update: function(noCatalog) {
                    const chartCanvas = document.getElementById('batchExpiryChart');
                    const warningAlert = document.getElementById('expiryWarningAlert');
                    if (!chartCanvas) return;
                    
                    chartCanvas.style.opacity = '0.5';

                    $.get("{{ url('/batch-expiry-data') }}", { noCatalog: noCatalog }, function(data) {
                        chartCanvas.style.opacity = '1';
                        const ctx = chartCanvas.getContext('2d');
                        
                        if (chartInstance) chartInstance.destroy();
                        
                        if (data.length === 0) {
                            warningAlert?.classList.add('hidden');
                            return;
                        }

                        let hasWarning = false;
                        const labels = data.map(item => `Batch: ${item.batch}`);
                        const values = data.map(item => {
                            if (item.monthsRemaining <= 3) hasWarning = true;
                            return Math.max(0, item.monthsRemaining);
                        });
                        
                        if (hasWarning) warningAlert?.classList.remove('hidden');
                        else warningAlert?.classList.add('hidden');

                        chartInstance = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Bulan Tersisa',
                                    data: values,
                                    backgroundColor: (context) => {
                                        const val = context.raw;
                                        const g = ctx.createLinearGradient(0, 0, 400, 0);
                                        if (val <= 3) {
                                            g.addColorStop(0, '#f87171');
                                            g.addColorStop(1, '#ef4444');
                                        } else {
                                            g.addColorStop(0, '#34d399');
                                            g.addColorStop(1, '#10b981');
                                        }
                                        return g;
                                    },
                                    borderRadius: 20,
                                    barThickness: 12,
                                    maxBarThickness: 15,
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: { duration: 1000, easing: 'easeOutQuart' },
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: '#111827',
                                        padding: 12,
                                        cornerRadius: 10,
                                        titleFont: { size: 12, weight: 'bold' },
                                        bodyFont: { size: 11 },
                                        displayColors: false,
                                        callbacks: {
                                            label: (context) => {
                                                const item = data[context.dataIndex];
                                                return [
                                                    `⏳ Masa Berlaku: ${item.monthsRemaining} bulan`,
                                                    `📅 Tanggal ED: ${item.formattedED}`,
                                                    `📦 Stok: ${item.quantity} unit`,
                                                    `📌 Status: ${item.status}`
                                                ];
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        grid: { color: '#f3f4f6', drawBorder: false },
                                        ticks: { 
                                            color: '#9ca3af',
                                            font: { size: 10 },
                                            callback: val => val + ' bln'
                                        }
                                    },
                                    y: {
                                        grid: { display: false },
                                        ticks: { 
                                            color: '#4b5563',
                                            font: { weight: '600', size: 11 }
                                        }
                                    }
                                }
                            }
                        });
                    });
                }
            };
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
            <p class="text-sm text-gray-500 mt-1">Analisis selisih pemakaian aktual terhadap rata-rata bulanan</p>
        </div>
    </div>

    <div class="h-96 w-full relative">
        <canvas id="deviationChart"></canvas>
    </div>
</div>

{{-- 🗓️ Batch Expiry Timeline (Gantt-style) --}}
<div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm mt-8 transition-all duration-300 hover:shadow-md">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Batch Expiry Timeline</h2>
            <p class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                <i class="fas fa-history text-blue-500"></i>
                Estimasi masa kadaluarsa berdasarkan nomor batch reagen
            </p>
        </div>
        <div class="flex items-center gap-4 bg-gray-50 px-4 py-2 rounded-2xl border border-gray-100">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-gradient-to-r from-red-400 to-red-600 shadow-sm shadow-red-200"></span>
                <span class="text-xs font-bold text-gray-600 tracking-wide uppercase">< 3 Bulan</span>
            </div>
            <div class="w-px h-4 bg-gray-200"></div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 shadow-sm shadow-emerald-200"></span>
                <span class="text-xs font-bold text-gray-600 tracking-wide uppercase">Aman</span>
            </div>
        </div>
    </div>

    <div id="batchExpiryContainer" class="h-80 w-full relative">
        <canvas id="batchExpiryChart"></canvas>
    </div>
    
    <div id="expiryWarningAlert" class="hidden mt-6 p-5 bg-gradient-to-r from-red-50 to-white border border-red-100 rounded-3xl flex items-center gap-5 shadow-sm shadow-red-50 transition-all duration-500">
        <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center text-red-500 flex-shrink-0 shadow-sm border border-red-50">
            <i class="fas fa-bell animate-bounce text-xl"></i>
        </div>
        <div class="flex-grow">
            <h4 class="text-base font-black text-red-900 tracking-tight">Peringatan Stok Mendekati ED!</h4>
            <p class="text-sm text-red-600/80 mt-0.5 leading-relaxed">Beberapa batch akan kadaluarsa dalam waktu dekat. Pastikan ketersediaan stok dengan memesan reagen baru sebelum masa ED berakhir.</p>
        </div>
        <div class="hidden sm:block">
            <a href="{{ route('order.index') }}" class="px-5 py-2.5 bg-red-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 transition-colors">Order Now</a>
        </div>
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
