@extends('layout.main')
@section('title', 'Random Usage Generator')

@section('container')
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

        <form action="{{ route('stock.random-usage.process') }}" method="POST" class="p-8 space-y-6">
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
                    <label for="excluded_dates" class="block text-sm font-semibold text-gray-700 mb-2">Excluded Dates (Days)</label>
                    <input type="text" id="excluded_dates" name="excluded_dates" value="{{ old('excluded_dates') }}"
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 transition-all"
                        placeholder="e.g. 1, 17, 25">
                    <p class="mt-2 text-xs text-gray-500 italic">Comma-separated days to skip. Weekends are skipped automatically.</p>
                </div>

                <!-- Excluded Users -->
                <div class="md:col-span-2">
                    <label for="excluded_users" class="block text-sm font-semibold text-gray-700 mb-2">Excluded Analysts (Users who won't take reagents)</label>
                    <select id="excluded_users" name="excluded_users[]" multiple class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 transition-all h-32">
                        @foreach($allUsers as $u)
                            <option value="{{ $u->guid }}" {{ is_array(old('excluded_users')) && in_array($u->guid, old('excluded_users')) ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->username }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-gray-500 italic">Hold Ctrl/Cmd to select multiple users who should NOT be included in the random generation.</p>
                </div>
            </div>

            <!-- Warning Box -->
            <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div class="text-sm text-amber-800">
                    <p class="font-bold">Important Notice:</p>
                    <p>This action will decrease actual stock quantities and generate permanent logbook entries. These logs will be marked as <strong>[System Generated]</strong>.</p>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-all shadow-lg shadow-blue-500/25">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Generate Random Logs
                </button>
            </div>
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

    async function fetchStockSummary() {
        const month = monthSelect.value;
        const year = yearSelect.value;
        
        loadingIndicator.classList.remove('hidden');
        summaryBody.innerHTML = `<tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Loading data...</td></tr>`;

        try {
            const response = await fetch(`/api/stock-history-summary?month=${month}&year=${year}`);
            const data = await response.json();

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

    monthSelect.addEventListener('change', fetchStockSummary);
    yearSelect.addEventListener('change', fetchStockSummary);

    // Initial load
    fetchStockSummary();
});
</script>
@endpush
@endsection
