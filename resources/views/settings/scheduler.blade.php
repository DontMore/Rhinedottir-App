@extends('layout.main')
@section('title', 'Scheduler Monitor')
@section('container')
<div class="flex flex-col md:flex-row gap-8">
    {{-- Navigation Sidebar --}}
    @include('settings.partials.navigation')

    {{-- Main Content --}}
    <div class="flex-1 space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Laravel Scheduler Monitor</h2>
            <p class="text-sm text-gray-500 mt-1">Real-time monitoring & control for background data push tasks.</p>
        </div>

        <!-- Control Panel -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Scheduler Control</h3>
                <div class="flex gap-3">
                    <form action="{{ route('settings.scheduler.start') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Start Scheduler
                        </button>
                    </form>
                    <form action="{{ route('settings.scheduler.stop') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6v4H9z"/>
                            </svg>
                            Stop Scheduler
                        </button>
                    </form>
                </div>
            </div>

            <!-- Status Indicator -->
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 mb-4">
                <div class="flex items-center gap-3">
                    <span id="scheduler-dot" class="w-3 h-3 rounded-full bg-gray-400"></span>
                    <div>
                        <p id="scheduler-status-text" class="text-sm font-medium text-gray-700">Checking status...</p>
                        <p id="scheduler-last-run" class="text-xs text-gray-500">No data available yet.</p>
                    </div>
                </div>
            </div>

            <!-- Live Log Viewer -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-2 border-b border-gray-200 flex items-center justify-between">
                    <h4 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">📜 Execution Logs (Last 15)</h4>
                    <span class="text-xs text-gray-400">Auto-refresh every 10s</span>
                </div>
                <div id="scheduler-logs" class="p-4 bg-gray-900 font-mono text-xs text-green-400 max-h-96 overflow-y-auto space-y-1">
                    <p class="text-gray-500 italic">Loading logs...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function fetchSchedulerStatus() {
    try {
        const res = await fetch('{{ route("settings.scheduler.status") }}');
        const data = await res.json();
        
        // Update dot & status text
        const dot = document.getElementById('scheduler-dot');
        const statusText = document.getElementById('scheduler-status-text');
        const lastRun = document.getElementById('scheduler-last-run');
        
        if (data.running) {
            dot.className = 'w-3 h-3 rounded-full bg-green-500 animate-pulse';
            statusText.textContent = '✅ Scheduler is Running';
            statusText.className = 'text-sm font-medium text-green-700';
            lastRun.textContent = `Last sync: ${data.last_run}`;
        } else {
            dot.className = 'w-3 h-3 rounded-full bg-red-500';
            statusText.textContent = '❌ Scheduler is Stopped';
            statusText.className = 'text-sm font-medium text-red-700';
            lastRun.textContent = `Last sync: ${data.last_run}`;
        }
        
        // Update logs
        const logBox = document.getElementById('scheduler-logs');
        if (data.logs && data.logs.length > 0) {
            logBox.innerHTML = data.logs.map(log => `<div class="border-b border-gray-800 pb-1">${log}</div>`).join('');
            logBox.scrollTop = logBox.scrollHeight;
        } else {
            logBox.innerHTML = '<p class="text-gray-500 italic">No logs recorded yet.</p>';
        }
    } catch (e) {
        console.error("Failed to fetch scheduler status", e);
    }
}

// Poll setiap 10 detik
setInterval(fetchSchedulerStatus, 10000);
document.addEventListener('DOMContentLoaded', fetchSchedulerStatus);
</script>
@endsection