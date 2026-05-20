@extends('layout.main')
@section('title', 'Random Usage Generator')

@section('container')
<!-- Flatpickr CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Random Usage Generator</h2>
            <p class="text-sm text-gray-500 mt-1">Generate simulated reagent usage logs for a specific period.</p>
        </div>
        <a href="{{ route('stock.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m7 7h12"/></svg>
            Back to List
        </a>
    </div>

    <!-- Generator Form -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Generation Settings</h3>
                    <p class="text-xs text-gray-500">Configure parameters for randomized logs.</p>
                </div>
            </div>
        </div>

        <form id="generator-form" class="p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Reagent Selection -->
                <div class="md:col-span-2">
                    <label for="reagen_guid" class="block text-sm font-semibold text-gray-700 mb-2">Reagent (Optional)</label>
                    <select id="reagen_guid" name="reagen_guid" class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 transition-all">
                        <option value="">-- All Reagents with Stock --</option>
                        @foreach($reagens as $reagen)
                            <option value="{{ $reagen->guid }}" {{ old('reagen_guid') == $reagen->guid ? 'selected' : '' }}>
                                {{ $reagen->noCatalog }} - {{ $reagen->nameReagen }} ({{ $reagen->stockReagen->quantity }} avail)
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-gray-500 italic">Leave as "All Reagents" to distribute usage randomly across all items.</p>
                </div>

                <!-- Month Selection -->
                <div>
                    <label for="month" class="block text-sm font-semibold text-gray-700 mb-2">Target Month</label>
                    <select id="month" name="month" required class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 transition-all">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ old('month', date('n')) == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Year Selection -->
                <div>
                    <label for="year" class="block text-sm font-semibold text-gray-700 mb-2">Target Year</label>
                    <select id="year" name="year" required class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 transition-all">
                        @foreach(range(date('Y')-2, date('Y')+1) as $y)
                            <option value="{{ $y }}" {{ old('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Record Count -->
                <div>
                    <label for="count" class="block text-sm font-semibold text-gray-700 mb-2">Number of Records</label>
                    <input type="number" id="count" name="count" value="{{ old('count', 20) }}" min="1" max="100" required
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 transition-all"
                        placeholder="e.g. 20">
                    <p class="mt-2 text-xs text-gray-500 italic">Recommended: 10-30 records per month.</p>
                </div>

                <!-- Excluded Dates -->
                <div>
                    <label for="excluded_dates" class="block text-sm font-semibold text-gray-700 mb-2">Excluded Dates</label>
                    <div class="relative">
                        <input type="text" id="excluded_dates" name="excluded_dates" value="{{ old('excluded_dates') }}" readonly
                            class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 transition-all cursor-pointer"
                            placeholder="Select dates to exclude...">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 italic">Click to select multiple dates to skip. Weekends are skipped automatically.</p>
                </div>

                <!-- Excluded Analysts (Checkboxes) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Excluded Analysts (Users who won't take reagents)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 bg-gray-50 p-4 rounded-xl border border-gray-200">
                        @foreach($allUsers as $u)
                            <label class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition-all cursor-pointer shadow-sm">
                                <input type="checkbox" name="excluded_users[]" value="{{ $u->guid }}" 
                                    {{ is_array(old('excluded_users')) && in_array($u->guid, old('excluded_users')) ? 'checked' : '' }}
                                    class="rounded text-blue-600 focus:ring-blue-500 border-gray-300 w-4 h-4">
                                <div class="text-sm">
                                    <p class="font-medium text-gray-700">{{ $u->name }}</p>
                                    <p class="text-xs text-gray-400">@ {{ $u->username }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-gray-500 italic">Check the analysts that should NOT be randomly assigned to reagent usage logs.</p>
                </div>
            </div>

            <!-- Warning Box -->
            <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div class="text-sm text-amber-800">
                    <p class="font-bold">Important Notice:</p>
                    <p>This action will decrease actual stock quantities and generate permanent logbook entries. The logbook entry notes will be left blank/empty.</p>
                </div>
            </div>

            <div class="pt-4">
                <button type="button" id="btn-generate-preview" class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-all shadow-lg shadow-blue-500/25">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Generate Preview Logs
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Container (Initially Hidden) -->
    <div id="preview-container" class="hidden bg-white rounded-2xl border border-gray-200 shadow-md overflow-hidden transition-all duration-300">
        <div class="p-6 border-b border-gray-100 bg-blue-50/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Preview Generated Logs</h3>
                    <p class="text-xs text-gray-500">Review the simulated logs before applying them to the database.</p>
                </div>
            </div>
            <span id="preview-badge" class="px-3 py-1 bg-blue-100 text-blue-800 font-bold rounded-full text-xs">0 records</span>
        </div>
        
        <div class="overflow-x-auto max-h-96">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reagent</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Catalog No</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Analyst</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Qty</th>
                    </tr>
                </thead>
                <tbody id="preview-table-body" class="divide-y divide-gray-100 text-sm">
                    <!-- Dynamic content -->
                </tbody>
            </table>
        </div>

        <!-- Confirm Form -->
        <form action="{{ route('stock.random-usage.process') }}" method="POST" id="confirm-form" class="p-6 bg-gray-50 border-t border-gray-100 flex gap-4">
            @csrf
            <input type="hidden" name="month" id="confirm-month">
            <input type="hidden" name="year" id="confirm-year">
            <input type="hidden" name="logs" id="confirm-logs">
            
            <button type="button" id="btn-cancel-preview" class="w-1/3 py-3.5 px-4 border border-gray-300 rounded-xl text-gray-700 font-bold hover:bg-gray-100 transition-all text-sm text-center">
                Cancel
            </button>
            <button type="submit" class="w-2/3 py-3.5 px-4 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-500/25 transition-all text-sm flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Confirm & Save to Database
            </button>
        </form>
    </div>

    <!-- Logic Description & Stock Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Logic Cards -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Random Dates</h4>
                <p class="text-sm text-gray-600 leading-relaxed">System will pick random weekdays within the target month. Saturdays and Sundays are excluded.</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Random Users</h4>
                <p class="text-sm text-gray-600 leading-relaxed">Records will be distributed among active users in your organization to simulate team activity.</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Stock Impact</h4>
                <p class="text-sm text-gray-600 leading-relaxed">Each transaction will be for **exactly 1 unit** to ensure natural usage patterns.</p>
            </div>
        </div>

        <!-- Stock Summary Table -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-sm font-semibold text-gray-800">Stock Context for Selected Period</h3>
                <span id="loading-indicator" class="hidden">
                    <svg class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
            </div>
            <div class="max-h-64 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Reagent</th>
                            <th class="px-4 py-2 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">In</th>
                            <th class="px-4 py-2 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">Out</th>
                            <th class="px-4 py-2 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ending Stock</th>
                        </tr>
                    </thead>
                    <tbody id="stock-summary-body" class="divide-y divide-gray-100 text-sm">
                        <!-- Populated via JS -->
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400 italic">Select month/year to see stock data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthSelect = document.getElementById('month');
    const yearSelect = document.getElementById('year');
    const summaryBody = document.getElementById('stock-summary-body');
    const loadingIndicator = document.getElementById('loading-indicator');

    // FLAT PICKER SETUP
    let fpInstance;

    function initFlatpickr() {
        const month = parseInt(monthSelect.value);
        const year = parseInt(yearSelect.value);
        
        // Construct minDate and maxDate dynamically
        const firstDay = `${year}-${String(month).padStart(2, '0')}-01`;
        const lastDayVal = new Date(year, month, 0).getDate();
        const lastDay = `${year}-${String(month).padStart(2, '0')}-${String(lastDayVal).padStart(2, '0')}`;

        if (fpInstance) {
            fpInstance.destroy();
        }

        fpInstance = flatpickr("#excluded_dates", {
            mode: "multiple",
            dateFormat: "Y-m-d",
            minDate: firstDay,
            maxDate: lastDay,
        });
    }

    monthSelect.addEventListener('change', function() {
        initFlatpickr();
        fetchStockSummary();
    });

    yearSelect.addEventListener('change', function() {
        initFlatpickr();
        fetchStockSummary();
    });

    // Initial load for flatpickr & stock summary
    initFlatpickr();
    fetchStockSummary();

    // STOCK SUMMARY FETCH
    async function fetchStockSummary() {
        const month = monthSelect.value;
        const year = yearSelect.value;
        
        loadingIndicator.classList.remove('hidden');
        summaryBody.innerHTML = `<tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Loading data...</td></tr>`;

        try {
            const response = await fetch(`{{ route('stock.history-summary') }}?month=${month}&year=${year}`);
            
            let data;
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                data = await response.json();
            } else {
                throw new Error("Server returned non-JSON response while fetching stock history.");
            }

            if (data.length === 0) {
                summaryBody.innerHTML = `<tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No stock history found for this period.</td></tr>`;
            } else {
                summaryBody.innerHTML = data.map(item => `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-2 font-medium text-gray-700">${item.reagen.nameReagen} <br><span class="text-[10px] text-gray-400">${item.noCatalog}</span></td>
                        <td class="px-4 py-2 text-center text-emerald-600 font-bold">${item.quantity_in}</td>
                        <td class="px-4 py-2 text-center text-rose-600 font-bold">${item.quantity_out}</td>
                        <td class="px-4 py-2 text-center font-bold text-gray-900">${item.quantity_actual}</td>
                    </tr>
                `).join('');
            }
        } catch (error) {
            summaryBody.innerHTML = `<tr><td colspan="4" class="px-4 py-8 text-center text-red-500">Failed to load data.</td></tr>`;
        } finally {
            loadingIndicator.classList.add('hidden');
        }
    }

    // PREVIEW AJAX GENERATION
    const btnPreview = document.getElementById('btn-generate-preview');
    const previewContainer = document.getElementById('preview-container');
    const previewTableBody = document.getElementById('preview-table-body');
    const previewBadge = document.getElementById('preview-badge');

    // Confirm inputs
    const confirmMonth = document.getElementById('confirm-month');
    const confirmYear = document.getElementById('confirm-year');
    const confirmLogs = document.getElementById('confirm-logs');

    btnPreview.addEventListener('click', async function() {
        // Gather checked analysts
        const checkedAnalysts = Array.from(document.querySelectorAll('input[name="excluded_users[]"]:checked'))
                                     .map(cb => cb.value);
        
        const payload = {
            _token: document.querySelector('input[name="_token"]').value,
            reagen_guid: document.getElementById('reagen_guid').value,
            month: monthSelect.value,
            year: yearSelect.value,
            count: document.getElementById('count').value,
            excluded_dates: document.getElementById('excluded_dates').value,
            excluded_users: checkedAnalysts
        };

        // Show spinner / loading indicator in button
        const originalBtnHTML = btnPreview.innerHTML;
        btnPreview.disabled = true;
        btnPreview.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Generating Simulation...
        `;

        try {
            const response = await fetch("{{ route('stock.random-usage.preview') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': payload._token
                },
                body: JSON.stringify(payload)
            });

            let data;
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                data = await response.json();
            } else {
                const text = await response.text();
                if (text.includes("<title>") && text.includes("</title>")) {
                    const titleMatch = text.match(/<title>(.*?)<\/title>/i);
                    throw new Error(titleMatch ? titleMatch[1] : "Server returned an HTML error.");
                }
                throw new Error("Server returned non-JSON response.");
            }

            if (!response.ok) {
                throw new Error(data.error || data.message || 'An error occurred during simulation.');
            }

            if (data.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Logs Generated',
                    text: 'Could not generate any logs. Please verify that there is enough stock available for the selected parameters.',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }

            // Populate table
            previewTableBody.innerHTML = data.map(log => {
                const logDate = new Date(log.date);
                const formattedDate = logDate.toLocaleString('en-US', { 
                    year: 'numeric', month: 'short', day: '2-digit',
                    hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
                });

                return `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 font-semibold text-gray-700">${formattedDate}</td>
                        <td class="px-6 py-3 text-gray-800 font-medium">${log.reagen_name}</td>
                        <td class="px-6 py-3 text-gray-400 font-mono text-xs">${log.noCatalog}</td>
                        <td class="px-6 py-3 text-gray-600 font-medium">${log.user_name} <span class="text-xs text-gray-400">(@${log.user_username})</span></td>
                        <td class="px-6 py-3 text-center"><span class="px-2 py-1 bg-amber-100 text-amber-800 font-bold rounded text-xs">1 unit</span></td>
                    </tr>
                `;
            }).join('');

            previewBadge.textContent = `${data.length} records`;

            // Prepare confirm form values
            confirmMonth.value = monthSelect.value;
            confirmYear.value = yearSelect.value;
            confirmLogs.value = JSON.stringify(data);

            // Show container
            previewContainer.classList.remove('hidden');
            previewContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });

        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Generation Failed',
                text: error.message,
                confirmButtonColor: '#ef4444'
            });
        } finally {
            btnPreview.disabled = false;
            btnPreview.innerHTML = originalBtnHTML;
        }
    });

    // CANCEL PREVIEW
    document.getElementById('btn-cancel-preview').addEventListener('click', function() {
        previewContainer.classList.add('hidden');
        document.getElementById('generator-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});
</script>
@endpush
@endsection
