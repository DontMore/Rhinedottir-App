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

        <!-- Form Card -->
        <form action="{{ route('settings.api.update') }}" method="POST" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            @csrf
            <div class="p-6 space-y-6">
                <!-- Status Toggle -->
                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <div>
                        <h3 class="text-sm font-semibold text-blue-900">Enable GAS Integration</h3>
                        <p class="text-xs text-blue-700 mt-0.5">Toggle the data synchronization to Google Sheets.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $settings->is_active ?? false) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- API Token Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">API Authentication Token</label>
                    <div class="flex items-center space-x-3">
                        <div class="relative flex-1">
                            <input type="text" readonly value="{{ $settings->api_token ?? 'Token not generated yet' }}" 
                                class="block w-full rounded-lg border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-600 font-mono focus:ring-0">
                        </div>
                        <button type="submit" form="regenerate-token-form" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Regenerate
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        Use this Bearer token in your Google Apps Script to authenticate requests to the Rhinedottir API.
                    </p>
                </div>

                <!-- Info Box & GAS Sample Script -->
                <div class="bg-gray-50 rounded-lg border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 flex items-start">
                        <svg class="h-5 w-5 text-gray-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                        <div class="text-xs text-gray-600 leading-relaxed">
                            <span class="font-semibold text-gray-700 block mb-1">Google Apps Script Integration (Pull Method)</span>
                            This API allows your Google Sheets to fetch data directly from Rhinedottir. Copy the code below into your Apps Script editor.
                        </div>
                    </div>
                    <div class="p-4 bg-gray-900 overflow-x-auto">
                        <pre class="text-xs text-green-400 font-mono leading-relaxed"><code>function syncRhinedottirData() {
  // Config
  var apiUrl = "{{ url('/api/export/logbook_reagens') }}"; // Change 'logbook_reagens' to your desired table
  var token = "{{ $settings->api_token ?? 'YOUR_API_TOKEN' }}";
  
  var options = {
    "method": "get",
    "headers": {
      "Authorization": "Bearer " + token
    }
  };
  
  try {
    var response = UrlFetchApp.fetch(apiUrl, options);
    var json = JSON.parse(response.getContentText());
    
    if(json.success) {
      Logger.log("Data fetched successfully! Found " + json.count + " rows.");
      // Add your logic to write json.data to the active Google Sheet here
    } else {
      Logger.log("Error: " + json.message);
    }
  } catch (e) {
    Logger.log("Request failed: " + e.toString());
  }
}</code></pre>
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

        <!-- Hidden form for regenerating token -->
        <form id="regenerate-token-form" action="{{ route('settings.api.regenerate-token') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</div>
@endsection
