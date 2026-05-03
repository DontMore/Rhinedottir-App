@extends('layout.main')
@section('title', 'Audit Trail Detail')

@section('container')
<div class="space-y-6">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Audit Trail Detail</h2>
            <p class="text-sm text-gray-500 mt-1">View complete information for this audit log entry.</p>
        </div>
        <a href="{{ route('audit-logs.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Logs
        </a>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Event Information</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Time</h4>
                <p class="text-sm text-gray-900 font-medium">{{ $audit->created_at?->format('d M Y, H:i:s') ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">User</h4>
                <p class="text-sm text-gray-900 font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ $audit->user?->name ?? 'System / API' }}
                </p>
            </div>
            <div>
                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Model</h4>
                <p class="text-sm text-gray-900 font-medium">{{ class_basename($audit->auditable_type) }}</p>
            </div>
            <div>
                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Record ID</h4>
                <p class="text-sm text-gray-900 font-mono">{{ $audit->auditable_id }}</p>
            </div>
            <div>
                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Event</h4>
                @php
                    $badge = match($audit->event) {
                        'created' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                        'updated' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                        'deleted' => 'bg-red-50 text-red-700 ring-red-600/20',
                        default => 'bg-gray-50 text-gray-700 ring-gray-600/20'
                    };
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $badge }}">
                    {{ ucfirst($audit->event) }}
                </span>
            </div>
            <div>
                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">IP Address & User Agent</h4>
                <p class="text-sm text-gray-900 font-medium">{{ $audit->ip_address ?? '-' }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $audit->user_agent ?? '-' }}</p>
            </div>
            <div class="col-span-1 md:col-span-2">
                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">URL</h4>
                <p class="text-sm text-gray-900 truncate">{{ $audit->url ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Changes Data -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Data Changes</h3>
        </div>
        <div class="p-6">
            @php
                $old = is_array($audit->old_values) ? $audit->old_values : json_decode($audit->old_values ?? '{}', true);
                $new = is_array($audit->new_values) ? $audit->new_values : json_decode($audit->new_values ?? '{}', true);
                $fields = array_unique(array_keys(array_merge($old ?? [], $new ?? [])));
            @endphp
            
            @if(count($fields) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Field</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Old Value</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">New Value</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($fields as $field)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 bg-gray-50">{{ $field }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 break-words">
                                        @if(isset($old[$field]))
                                            @if(is_array($old[$field]) || is_object($old[$field]))
                                                <pre class="text-xs bg-gray-100 p-2 rounded whitespace-pre-wrap">{{ json_encode($old[$field], JSON_PRETTY_PRINT) }}</pre>
                                            @else
                                                <span class="text-red-600 line-through decoration-red-300">{{ $old[$field] }}</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400 italic">null</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 break-words">
                                        @if(isset($new[$field]))
                                            @if(is_array($new[$field]) || is_object($new[$field]))
                                                <pre class="text-xs bg-gray-100 p-2 rounded whitespace-pre-wrap">{{ json_encode($new[$field], JSON_PRETTY_PRINT) }}</pre>
                                            @else
                                                <span class="text-emerald-700 font-medium">{{ $new[$field] }}</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400 italic">null</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-sm">No data changes recorded for this event.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
