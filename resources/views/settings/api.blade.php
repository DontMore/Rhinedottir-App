@extends('layout.main')
@section('title', 'API Settings')
@section('container')
<div class="flex flex-col md:flex-row gap-8">
    {{-- Navigation Sidebar --}}
    @include('settings.partials.navigation')
    
    {{-- Main Content --}}
    <div class="flex-1 space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-xl font-semibold text-gray-800">API Configuration</h2>
            <p class="text-sm text-gray-500 mt-1">Configure external API connections, including Google Apps Script (GAS) for data synchronization.</p>
        </div>

        <!-- ✅ FORM CARD: API Settings (Main Form) -->
        <form action="{{ route('settings.api.update') }}" method="POST" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            @csrf
            <div class="p-6 space-y-6">
                
                <!-- Status Toggle (Pull Method) -->
                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <div>
                        <h3 class="text-sm font-semibold text-blue-900">Enable GAS Integration (Pull Method)</h3>
                        <p class="text-xs text-blue-700 mt-0.5">Allows Google Sheets to fetch data from this application.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $settings->is_active ?? false) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Push Method Section -->
                <div class="pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-lg border border-emerald-100 mb-6">
                        <div>
                            <h3 class="text-sm font-semibold text-emerald-900">Enable Auto-Push Method</h3>
                            <p class="text-xs text-emerald-700 mt-0.5">Automatically push data changes to your Google Sheet on a schedule.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="push_is_active" value="1" class="sr-only peer" {{ old('push_is_active', $settings->push_is_active ?? false) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">GAS Web App URL (Push Endpoint)</label>
                            <input type="url" name="gas_web_app_url" value="{{ old('gas_web_app_url', $settings->gas_web_app_url ?? '') }}" 
                               placeholder="https://script.google.com/macros/s/.../exec"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            
                            <!-- ✅ Error Display: URL -->
                            @error('gas_web_app_url')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                            
                            <p class="mt-1.5 text-xs text-gray-500">The URL of your deployed Google Apps Script Web App.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Push Interval</label>
                            <select name="push_interval" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="everyMinute" {{ ($settings->push_interval ?? '') == 'everyMinute' ? 'selected' : '' }}>Every Minute (Testing)</option>
                                <option value="everyFiveMinutes" {{ ($settings->push_interval ?? '') == 'everyFiveMinutes' ? 'selected' : '' }}>Every 5 Minutes</option>
                                <option value="everyTenMinutes" {{ ($settings->push_interval ?? '') == 'everyTenMinutes' ? 'selected' : '' }}>Every 10 Minutes</option>
                                <option value="everyThirtyMinutes" {{ ($settings->push_interval ?? '') == 'everyThirtyMinutes' ? 'selected' : '' }}>Every 30 Minutes</option>
                                <option value="hourly" {{ ($settings->push_interval ?? '') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                <option value="daily" {{ ($settings->push_interval ?? '') == 'daily' ? 'selected' : '' }}>Daily</option>
                            </select>

                            <!-- ✅ Error Display: Interval -->
                            @error('push_interval')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                            
                           @if($settings->last_push_at ?? false)
                                <p class="mt-1.5 text-xs text-gray-500">Last successful push: <span class="font-medium">{{ \Carbon\Carbon::parse($settings->last_push_at)->diffForHumans() }}</span></p>
                           @endif
                        </div>
                    </div>

                    <!-- ✅ Table Selection UI -->
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">Selected Tables to Sync</h3>
                            <p class="text-xs text-gray-500 mt-1">Check the tables you want to send to Google Sheets. Leave all unchecked to send everything.</p>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                           @php
                               $tableList = [
                                   'reagens' => 'Reagens Master',
                                   'logbook_reagens' => 'Logbook Reagens',
                                   'stock_reagens' => 'Stock Reagens',
                                   'reagens_in' => 'Reagens In',
                                   'orders' => 'Orders',
                                   'stock_opnames' => 'Stock Opname'
                               ];
                               $selectedTables = old('selected_tables', $settings->selected_tables ?? []);
                               if (!is_array($selectedTables)) { $selectedTables = []; }
                           @endphp
                           
                           @foreach($tableList as $key => $label)
                               <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors {{ in_array($key, $selectedTables) ? 'bg-blue-50 border-blue-300 ring-1 ring-blue-300' : '' }}">
                                   <input type="checkbox" name="selected_tables[]" value="{{ $key }}" 
                                          class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                          {{ in_array($key, $selectedTables) ? 'checked' : '' }}>
                                   <span class="ml-3 text-sm text-gray-700 font-medium">{{ $label }}</span>
                               </label>
                           @endforeach
                        </div>
                        
                        <!-- ✅ Error Display: Selected Tables -->
                        @error('selected_tables')
                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        @error('selected_tables.*')
                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- API Token Section -->
                <div class="pt-4 border-t border-gray-100">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">API Authentication Token</label>
                    <div class="flex items-center space-x-3">
                        <div class="relative flex-1">
                            <input type="text" readonly value="{{ $settings->api_token ?? 'Token not generated yet' }}" 
                               class="block w-full rounded-lg border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-600 font-mono focus:ring-0">
                        </div>
                        <button type="button" onclick="document.getElementById('regenerate-token-form').submit()" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Regenerate
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        This token is used for both Pull and Push methods to ensure secure data transfer.
                    </p>
                </div>

                <!-- Info Box & GAS Sample Script -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                   {{-- Pull Method Script --}}
                    <div class="bg-gray-50 rounded-lg border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 flex items-start">
                            <svg class="h-5 w-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                            </svg>
                            <div class="text-xs text-gray-600 leading-relaxed">
                                <span class="font-semibold text-gray-700 block mb-1">Pull Method Script (Client-side)</span>
                                Use this in your Google Sheet to <strong>fetch</strong> data from Rhinedottir.
                            </div>
                        </div>
                        <div class="p-4 bg-gray-900 overflow-x-auto">
                            <pre class="text-[10px] text-green-400 font-mono leading-relaxed"><code>function syncRhinedottirData() {
var apiUrl = "{{ url('/api/export/logbook_reagens') }}";
var token = "{{ $settings->api_token ?? 'YOUR_API_TOKEN' }}";
var options = {
"method": "get",
"headers": { "Authorization": "Bearer " + token }
};
try {
var response = UrlFetchApp.fetch(apiUrl, options);
var json = JSON.parse(response.getContentText());
if(json.success) {
Logger.log("Success! Found " + json.count + " rows.");
}
} catch (e) { Logger.log("Error: " + e.toString()); }
}</code></pre>
                        </div>
                    </div>
                    {{-- Push Method Script --}}
                    <div class="bg-gray-50 rounded-lg border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 flex items-start">
                            <svg class="h-5 w-5 text-emerald-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <div class="text-xs text-gray-600 leading-relaxed">
                                <span class="font-semibold text-gray-700 block mb-1">Push Method Script (Server-side)</span>
                                Deploy this as a <strong>Web App</strong> in GAS to <strong>receive</strong> data from Rhinedottir.
                            </div>
                        </div>
                        <div class="p-4 bg-gray-900 overflow-x-auto">
                            <pre class="text-[10px] text-emerald-400 font-mono leading-relaxed"><code>function doPost(e) {
var token = "{{ $settings->api_token ?? 'YOUR_API_TOKEN' }}";
var contents = JSON.parse(e.postData.contents);
if (contents.token !== token) {
return ContentService.createTextOutput("Unauthorized").setMimeType(ContentService.MimeType.TEXT);
}
var ss = SpreadsheetApp.getActiveSpreadsheet();
var tableName = contents.table || "Sync_Data";
var sheet = ss.getSheetByName(tableName) || ss.insertSheet(tableName);
var data = contents.payload;
if (data && data.length > 0) {
sheet.clear();
var headers = Object.keys(data[0]);
sheet.appendRow(headers);
var rows = data.map(function(item) {
return headers.map(function(header) { return item[header]; });
});
sheet.getRange(2, 1, rows.length, headers.length).setValues(rows);
sheet.getRange(1, 1, 1, headers.length).setFontWeight("bold").setBackground("#f3f3f3");
sheet.setFrozenRows(1);
sheet.autoResizeColumns(1, headers.length);
}
return ContentService.createTextOutput("Success: " + (data ? data.length : 0) + " rows synced").setMimeType(ContentService.MimeType.TEXT);
}</code></pre>
                        </div>
                    </div>
                </div>

                <!-- Local Development Help -->
                <div class="bg-amber-50 rounded-lg border border-amber-100 p-4">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-amber-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-amber-900 mb-1">Local Development Connectivity</h4>
                            <p class="text-xs text-amber-800 leading-relaxed mb-3">
                                Google Apps Script cannot access <code>localhost</code> directly. If you are developing locally (e.g., using Laragon), you must expose your application to the internet.
                            </p>
                            <div class="space-y-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Steps for Ngrok:</span>
                                <ol class="list-decimal list-inside text-xs text-amber-800 space-y-1">
                                    <li>Install <strong>Ngrok</strong> from <a href="https://ngrok.com" target="_blank" class="underline font-medium">ngrok.com</a></li>
                                    <li>Run <code>ngrok http 80</code> in your terminal.</li>
                                    <li>Copy the <strong>Forwarding URL</strong> (e.g., <code>https://...ngrok-free.app</code>).</li>
                                    <li>Use that URL instead of <code>{{ url('/') }}</code> in your Google Apps Script.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save API Settings
                </button>
            </div>
        </form>

        <!-- ✅ SCHEDULER MONITOR: Moved OUTSIDE the main form -->
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <span id="scheduler-dot" class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
                    Scheduler Status
                </h4>
                <div class="flex gap-2">
                    <form action="{{ route('settings.scheduler.start') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs px-3 py-1.5 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition">▶ Start</button>
                    </form>
                    <form action="{{ route('settings.scheduler.stop') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700 transition"> Stop</button>
                    </form>
                </div>
            </div>

            <p id="scheduler-last-run" class="text-xs text-gray-500 mb-2">Checking status...</p>

            <!-- Log Viewer -->
            <details class="text-xs bg-white border border-gray-200 rounded p-2">
                <summary class="cursor-pointer font-medium text-gray-600 hover:text-gray-800">📜 Live Logs (Last 8 entries)</summary>
                <div id="scheduler-logs" class="mt-2 font-mono text-gray-700 space-y-1 max-h-40 overflow-y-auto">
                    <p class="text-gray-400 italic">Logs will appear here...</p>
                </div>
            </details>
        </div>

        <script>
           async function fetchSchedulerStatus() {
               try {
                   const res = await fetch('{{ route("settings.scheduler.status") }}');
                   const data = await res.json();

                   const dot = document.getElementById('scheduler-dot');
                   dot.className = `w-2.5 h-2.5 rounded-full ${data.running ? 'bg-green-500 animate-pulse' : 'bg-red-500'}`;

                   document.getElementById('scheduler-last-run').textContent = data.running ? 
                       `✅ Running • Last sync: ${data.last_run}` : 
                       `❌ Stopped • Last sync: ${data.last_run}`;

                   const logBox = document.getElementById('scheduler-logs');
                   if (data.logs && data.logs.length > 0) {
                       logBox.innerHTML = data.logs.map(log => `<div class="border-b border-gray-100 pb-1">${log}</div>`).join('');
                       logBox.scrollTop = logBox.scrollHeight;
                   }
               } catch (e) {
                   console.error("Failed to fetch scheduler status", e);
               }
           }

           setInterval(fetchSchedulerStatus, 15000);
           document.addEventListener('DOMContentLoaded', fetchSchedulerStatus);
        </script>

        <!-- Hidden form for regenerating token -->
        <form id="regenerate-token-form" action="{{ route('settings.api.regenerate-token') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</div>
@endsection