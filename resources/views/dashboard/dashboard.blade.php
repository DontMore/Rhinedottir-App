@extends('layout.main')
@section('container')
<style>
/* 🎨 Soft UI Global Styles */
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap');

.soft-ui-wrapper {
    font-family: 'Nunito', 'Poppins', sans-serif;
    background-color: #F8FAFC; /* Very light, soft off-white/gray background */
    color: #334155; /* Slate 700 - Soft dark text, no pure black */
    min-height: 100vh;
    padding: 24px;
    border-radius: 32px;
}

.soft-ui-wrapper h1, .soft-ui-wrapper h2, .soft-ui-wrapper h3, .soft-ui-wrapper h4 {
    color: #1E293B; /* Slate 800 - Deep gray for headings */
    font-weight: 800;
    letter-spacing: -0.02em;
}

/* 🎨 Soft UI Select2 Overrides */
.select2-container { width: 100% !important; }

/* Selection Box - Pill shaped, soft background, no hard border */
.select2-container--default .select2-selection--single {
    height: 52px; 
    padding: 0 24px; 
    border: none !important; 
    border-radius: 50px !important; 
    background: #F1F5F9 !important; /* Soft light gray */
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    transition: all 0.3s ease;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 52px; 
    padding-left: 0; 
    color: #334155; 
    font-size: 0.95rem; 
    font-weight: 600;
}
.select2-container--default .select2-selection--single .select2-selection__arrow { 
    height: 52px; 
    right: 24px; 
}
.select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #94A3B8 transparent transparent transparent; 
    border-width: 6px 5px 0 5px; 
    margin-left: -5px;
}

/* Dropdown - Soft shadow, rounded corners */
.select2-dropdown {
    border: none !important; 
    border-radius: 24px !important; 
    box-shadow: 0px 20px 45px rgba(0,0,0,0.08) !important; 
    margin-top: 12px; 
    overflow: hidden; 
    padding: 8px;
    background: #FFFFFF;
}
.select2-search--dropdown .select2-search__field {
    padding: 12px 20px; 
    border: none !important; 
    border-radius: 50px !important; 
    margin: 8px; 
    width: calc(100% - 16px); 
    font-size: 0.9rem; 
    background: #F8FAFC;
    transition: all 0.2s;
}
.select2-search--dropdown .select2-search__field:focus { 
    outline: none; 
    background: #FFFFFF;
    box-shadow: 0 0 0 4px rgba(99,102,241,0.1); /* Soft indigo glow */
}

/* Options */
.select2-results__option { 
    padding: 12px 16px; 
    border-radius: 16px; 
    font-size: 0.9rem; 
    font-weight: 500;
    color: #475569;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] { 
    background-color: #EEF2FF !important; 
    color: #4338CA !important; 
}
.select2-container--default .select2-results__option[aria-selected=true] { 
    background-color: #F8FAFC; 
    color: #1E293B; 
    font-weight: 700;
}

/* Focus & Clear */
.select2-container--default.select2-container--focus .select2-selection--single { 
    background: #FFFFFF;
    box-shadow: 0 0 0 4px rgba(99,102,241,0.1); 
}
.select2-container--default .select2-selection--single .select2-selection__clear { 
    margin-right: 30px; 
    color: #94A3B8; 
    font-size: 1.2rem; 
}

/* Custom Scrollbar for Soft UI */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
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
            gradientIn.addColorStop(0, 'rgba(16, 185, 129, 0.15)');
            gradientIn.addColorStop(1, 'rgba(16, 185, 129, 0)');

            const gradientOut = ctx.createLinearGradient(0, 0, 0, 400);
            gradientOut.addColorStop(0, 'rgba(239, 68, 68, 0.10)');
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
                        pointRadius: 5,
                        pointHoverRadius: 7
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
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { top: 20, bottom: 10, left: 10, right: 10 } },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 25,
                                font: { size: 13, weight: '600', family: "'Nunito', sans-serif" },
                                color: '#64748B'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            titleFont: { size: 14, weight: 'bold', family: "'Nunito', sans-serif" },
                            bodyFont: { size: 13, family: "'Nunito', sans-serif" },
                            padding: 16,
                            cornerRadius: 16,
                            displayColors: true,
                            boxPadding: 8
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94A3B8', font: { size: 12, weight: '600' }, padding: 10 }
                        },
                        y: { 
                            beginAtZero: true,
                            grid: { color: '#F1F5F9', drawBorder: false },
                            ticks: { precision: 0, color: '#94A3B8', font: { size: 12, weight: '600' }, padding: 10 }
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
        let currentAverage = 0; // ✅ TAMBAHKAN variable untuk menyimpan average

        function renderDeviationChart(usageData = null, labels = null) {
            const ctx = document.getElementById('deviationChart')?.getContext('2d');
            if (!ctx) return;

            if (usageData) currentUsageData = usageData;
            if (labels) currentLabels = labels;

            // ==========================================
            // 🔍 DEBUG: Tampilkan data di console
            // ==========================================
            console.group("📊 DEBUG: Grafik Deviasi");
            console.log("📅 Labels (12 bulan):", currentLabels);
            console.log("📦 Data Pemakaian:", currentUsageData);
            console.log("📊 Jumlah Bulan:", currentUsageData.length);
            
            const sum = currentUsageData.reduce((a, b) => a + b, 0);
            console.log("➕ Total Pemakaian:", sum);
            
            const count = currentUsageData.length; // Selalu 12
            const average = sum / (count || 1);
            
            // ✅ SIMPAN average ke variable global module
            currentAverage = average;
            
            console.log(" Rata-rata (Total / 12):", average.toFixed(2));
            console.log("   Perhitungan:", sum, "/ 12 =", average.toFixed(2));
            console.groupEnd();
            // ==========================================

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
                    datasets: [{
                        label: 'Deviasi Pemakaian',
                        data: deviations,
                        borderColor: '#94A3B8',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: {
                            target: 'origin',
                            above: 'rgba(239, 68, 68, 0.10)', 
                            below: 'rgba(16, 185, 129, 0.10)'  
                        },
                        pointBackgroundColor: context => context.raw >= 0 ? '#ef4444' : '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 800, easing: 'easeOutQuart' },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B', 
                            padding: 16,
                            cornerRadius: 16,
                            titleFont: { size: 13, weight: 'bold', family: "'Nunito', sans-serif" },
                            bodyFont: { size: 12, family: "'Nunito', sans-serif" },
                            callbacks: {
                                title: items => `📅 ${items[0].label}`,
                                label: function(context) {
                                    const dev = context.raw;
                                    const actual = currentUsageData[context.dataIndex];
                                    const isAbove = dev >= 0;
                                    return [
                                        `Pemakaian: ${actual} unit`,
                                        `Rata-rata: ${currentAverage.toFixed(2)} unit`, // ✅ GUNAKAN currentAverage
                                        `Deviasi: ${isAbove ? '+' : ''}${dev} unit`,
                                        `📌 Status: ${isAbove ? '⚠️ Di Atas Rata-rata' : '✅ Di Bawah Rata-rata'}`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94A3B8', font: { size: 11, weight: '600' }, maxTicksLimit: 12 }
                        },
                        y: {
                            beginAtZero: false,
                            grid: {
                                color: (ctx) => ctx.tick.value === 0 ? '#CBD5E1' : '#F1F5F9',
                                lineWidth: (ctx) => ctx.tick.value === 0 ? 2 : 1,
                                drawBorder: false
                            },
                            ticks: {
                                color: '#94A3B8',
                                font: { size: 11, weight: '600' },
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
            init: function() { renderDeviationChart(); }
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
                                        g.addColorStop(0, '#fca5a5'); g.addColorStop(1, '#ef4444');
                                    } else {
                                        g.addColorStop(0, '#6ee7b7'); g.addColorStop(1, '#10b981');
                                    }
                                    return g;
                                },
                                borderRadius: 20,
                                barThickness: 14,
                                maxBarThickness: 18,
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
                                    backgroundColor: '#1E293B',
                                    padding: 16,
                                    cornerRadius: 16,
                                    titleFont: { size: 12, weight: 'bold', family: "'Nunito', sans-serif" },
                                    bodyFont: { size: 11, family: "'Nunito', sans-serif" },
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
                                    grid: { color: '#F1F5F9', drawBorder: false },
                                    ticks: { color: '#94A3B8', font: { size: 11, weight: '600' }, callback: val => val + ' bln' }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: { color: '#475569', font: { weight: '700', size: 12, family: "'Nunito', sans-serif" } }
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

<!-- 🎨 SOFT UI WRAPPER -->
<div class="soft-ui-wrapper">

    {{-- 📊 Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @php
        $stats = [
            ['title' => 'Total Reagent', 'value' => $totalQuantity ?? 0, 'sub' => 'Current stock', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'icon' => 'bi-clipboard2-data'],
            ['title' => 'Reagent In', 'value' => $totalQuantityIn ?? 0, 'sub' => 'This month', 'bg' => 'bg-sky-50', 'text' => 'text-sky-600', 'icon' => 'bi-box-arrow-in-down'],
            ['title' => 'Reagent Taken', 'value' => $totalQuantityOut ?? 0, 'sub' => 'This month', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'icon' => 'bi-box-arrow-right'],
            ['title' => 'Reagent Variants', 'value' => $totalReagen ?? 0, 'sub' => 'Unique types', 'bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'icon' => 'bi-collection']
        ];
        @endphp
        @foreach($stats as $stat)
        <!-- Soft UI Card: No borders, large radius, soft diffused shadow -->
        <div class="bg-white p-7 rounded-[24px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] hover:shadow-[0px_20px_45px_rgba(0,0,0,0.06)] hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $stat['title'] }}</p>
                    <h3 class="text-3xl font-extrabold {{ $stat['text'] }} mt-2 tracking-tight">{{ number_format($stat['value']) }}</h3>
                    <p class="text-xs font-semibold text-slate-400 mt-2 flex items-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300 mr-2"></span>
                        {{ $stat['sub'] }}
                    </p>
                </div>
                <!-- Pill-shaped / heavily rounded icon container -->
                <div class="p-4 {{ $stat['bg'] }} rounded-[20px] group-hover:scale-110 transition-transform duration-300 shadow-sm">
                    <i class="bi {{ $stat['icon'] }} text-2xl {{ $stat['text'] }}"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 📈 Chart Section --}}
    <div class="bg-white p-8 rounded-[28px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-6">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800">Reagent Analysis</h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Comparison of reagent inflow and usage trends</p>
            </div>
            <div class="w-full sm:w-80">
                <select id="reagentSelect" class="w-full"></select>
            </div>
        </div>
        <div class="h-80 w-full rounded-[24px] p-2 bg-slate-50/50">
            <canvas id="combinedReagentChart"></canvas>
        </div>
    </div>

    {{-- 📉 Grafik Deviasi Pemakaian Reagen --}}
    <div class="bg-white p-8 rounded-[28px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800">Grafik Deviasi Pemakaian</h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Analisis selisih pemakaian aktual terhadap rata-rata bulanan</p>
            </div>
        </div>
        <div class="h-96 w-full relative rounded-[24px] p-2 bg-slate-50/50">
            <canvas id="deviationChart"></canvas>
        </div>
    </div>

    {{-- 🗓️ Batch Expiry Timeline --}}
    <div class="bg-white p-8 rounded-[28px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] mb-8 transition-all duration-300 hover:shadow-[0px_20px_45px_rgba(0,0,0,0.06)]">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-6">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Batch Expiry Timeline</h2>
                <p class="text-sm font-medium text-slate-500 mt-1 flex items-center gap-2">
                    <i class="fas fa-history text-indigo-400"></i>
                    Estimasi masa kadaluarsa berdasarkan nomor batch reagen
                </p>
            </div>
            <!-- Legend as soft pill tags -->
            <div class="flex items-center gap-3 bg-slate-50 px-5 py-2.5 rounded-full">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-gradient-to-r from-red-400 to-red-500 shadow-sm"></span>
                    <span class="text-xs font-bold text-slate-600 tracking-wide uppercase">< 3 Bulan</span>
                </div>
                <div class="w-px h-4 bg-slate-200"></div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-500 shadow-sm"></span>
                    <span class="text-xs font-bold text-slate-600 tracking-wide uppercase">Aman</span>
                </div>
            </div>
        </div>
        
        <div id="batchExpiryContainer" class="h-80 w-full relative rounded-[24px] p-2 bg-slate-50/50">
            <canvas id="batchExpiryChart"></canvas>
        </div>

        <!-- Soft UI Warning Alert -->
        <div id="expiryWarningAlert" class="hidden mt-8 p-6 bg-red-50/60 rounded-[24px] flex items-center gap-6 shadow-[0px_10px_25px_rgba(239,68,68,0.05)] transition-all duration-500">
            <div class="w-14 h-14 rounded-[18px] bg-white flex items-center justify-center text-red-500 flex-shrink-0 shadow-sm">
                <i class="fas fa-bell animate-bounce text-xl"></i>
            </div>
            <div class="flex-grow">
                <h4 class="text-base font-extrabold text-red-800 tracking-tight">Peringatan Stok Mendekati ED!</h4>
                <p class="text-sm text-red-600/80 mt-1 leading-relaxed font-medium">Beberapa batch akan kadaluarsa dalam waktu dekat. Pastikan ketersediaan stok dengan memesan reagen baru.</p>
            </div>
            <div class="hidden sm:block">
                <!-- Pill-shaped button -->
                <a href="{{ route('order.index') }}" class="px-6 py-3 bg-red-500 text-white text-xs font-bold rounded-full shadow-lg shadow-red-100 hover:bg-red-600 transition-colors">Order Now</a>
            </div>
        </div>
    </div>

    {{-- 📋 Tables Section (Converted to Floating Cards Layout) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
        
        {{-- Zero Stock --}}
        <div class="bg-white rounded-[28px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] overflow-hidden flex flex-col">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800">Zero Stock</h3>
                    <p class="text-xs font-semibold text-slate-400 mt-0.5">Out of stock reagents</p>
                </div>
                <span class="px-3 py-1 text-xs font-bold bg-red-50 text-red-500 rounded-full">{{ $zeroStockReagen->count() }}</span>
            </div>
            <div class="px-6 pb-6 overflow-y-auto max-h-80 custom-scrollbar space-y-3">
                @forelse($zeroStockReagen as $item)
                <!-- Row as a floating mini-card -->
                <div class="bg-slate-50/80 p-4 rounded-2xl flex items-center justify-between hover:bg-slate-100/80 transition-colors group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center">
                            <i class="bi bi-droplet text-slate-400"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 truncate max-w-[140px]">{{ $item->reagen->nameReagen }}</p>
                            <p class="text-[11px] text-slate-400 font-mono">{{ $item->noCatalog }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-[11px] font-bold bg-red-100/80 text-red-600 rounded-full">0</span>
                </div>
                @empty
                <div class="flex flex-col items-center py-10">
                    <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center mb-4">
                        <i class="bi bi-check2-circle text-2xl text-emerald-500"></i>
                    </div>
                    <p class="text-slate-500 font-semibold">All stocks available</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Reagent Orders --}}
        <div class="bg-white rounded-[28px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] overflow-hidden flex flex-col">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800">Recent Orders</h3>
                    <p class="text-xs font-semibold text-slate-400 mt-0.5">Pending procurement</p>
                </div>
                <span class="px-3 py-1 text-xs font-bold bg-blue-50 text-blue-500 rounded-full">{{ $reagenOrder->count() }}</span>
            </div>
            <div class="px-6 pb-6 overflow-y-auto max-h-80 custom-scrollbar space-y-3">
                @forelse($reagenOrder as $order)
                <div class="bg-slate-50/80 p-4 rounded-2xl flex items-center justify-between hover:bg-slate-100/80 transition-colors group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center">
                            <i class="bi bi-box-seam text-slate-400"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 truncate max-w-[140px]">{{ $order->nameReagen }}</p>
                            <p class="text-[11px] text-slate-400 font-mono">{{ $order->noCatalog }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-[11px] font-bold bg-blue-100/80 text-blue-600 rounded-full">{{ $order->quantity }}</span>
                </div>
                @empty
                <div class="flex flex-col items-center py-10">
                    <div class="w-14 h-14 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                        <i class="bi bi-box text-2xl text-slate-300"></i>
                    </div>
                    <p class="text-slate-500 font-semibold">No pending orders</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Expiring Soon --}}
        <div class="bg-white rounded-[28px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] overflow-hidden flex flex-col">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800">Expiring Soon</h3>
                    <p class="text-xs font-semibold text-slate-400 mt-0.5">Critical expiration dates</p>
                </div>
                <a href="{{ route('reagen.expired') }}" class="w-9 h-9 bg-slate-50 rounded-full flex items-center justify-center hover:bg-indigo-50 transition-colors group">
                    <i class="bi bi-arrow-right text-slate-400 group-hover:text-indigo-500"></i>
                </a>
            </div>
            <div class="px-6 pb-6 overflow-y-auto max-h-80 custom-scrollbar space-y-3">
                @forelse($reagenED as $expired)
                <div class="bg-slate-50/80 p-4 rounded-2xl flex items-center justify-between hover:bg-slate-100/80 transition-colors group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center">
                            <i class="bi bi-clock-history text-slate-400"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 truncate max-w-[140px]">{{ $expired->reagen ? $expired->reagen->nameReagen : 'Unknown' }}</p>
                            <p class="text-[11px] text-slate-400 font-mono">{{ $expired->noCatalog }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-[11px] font-bold bg-orange-50 text-orange-600 rounded-full border border-orange-100/50">
                        {{ \Carbon\Carbon::parse($expired->expiredDate)->format('d M Y') }}
                    </span>
                </div>
                @empty
                <div class="flex flex-col items-center py-10">
                    <div class="w-14 h-14 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                        <i class="bi bi-clock text-2xl text-slate-300"></i>
                    </div>
                    <p class="text-slate-500 font-semibold">No expiring reagents</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection