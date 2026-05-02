@extends('layout.main')
@section('title', 'Audit Trail Log')

@section('container')
<div class="space-y-6">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Audit Trail Log</h2>
            <p class="text-sm text-gray-500 mt-1">Track system changes, user activities, and data modifications.</p>
        </div>
        <a href="{{ route('audit-logs.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Reset Filters
        </a>
    </div>

    <!-- Filter Form Card -->
    <form method="GET" action="{{ route('audit-logs.index') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label for="model" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Model</label>
                <select name="model" id="model" class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    <option value="">All Models</option>
                    @foreach($models as $model)
                        <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>
                            {{ class_basename($model) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="event" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Event</label>
                <select name="event" id="event" class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    <option value="">All Events</option>
                    @foreach($events as $event)
                        <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                            {{ ucfirst($event) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="user_id" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">User</label>
                <select name="user_id" id="user_id" class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    <option value="">All Users</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Filter
                </button>
            </div>
        </div>
    </form>

    <!-- Data Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model & ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Changes</th>
                        <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP / Agent</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($audits as $audit)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $audit->created_at?->format('d M Y, H:i') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $audit->user?->name ?? 'System / API' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="font-medium text-gray-900">{{ class_basename($audit->auditable_type) }}</span>
                            <span class="block text-xs text-gray-400 font-mono mt-0.5">{{ Str::limit($audit->auditable_id, 10, '...') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        </td>
                        <td class="px-6 py-4 text-sm max-w-md">
                            @php
                                $old = is_array($audit->old_values) ? $audit->old_values : json_decode($audit->old_values ?? '{}', true);
                                $new = is_array($audit->new_values) ? $audit->new_values : json_decode($audit->new_values ?? '{}', true);
                                $fields = array_unique(array_keys(array_merge($old ?? [], $new ?? [])));
                                $ignored = ['updated_at', 'created_at', 'guid'];
                                $fields = array_diff($fields, $ignored);
                            @endphp
                            @if(count($fields) > 0)
                                <div class="space-y-1">
                                    @foreach(array_slice($fields, 0, 3) as $field)
                                        <div class="text-xs">
                                            <span class="text-gray-500 font-medium">{{ $field }}:</span>
                                            @if(isset($old[$field]))
                                                <span class="text-red-600 line-through decoration-red-300">{{ $old[$field] }}</span>
                                            @endif
                                            @if(isset($new[$field]))
                                                <span class="text-emerald-700 font-medium ml-1">{{ $new[$field] }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if(count($fields) > 3)
                                        <span class="text-xs text-gray-400">+{{ count($fields) - 3 }} more changes</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">No changes recorded</span>
                            @endif
                        </td>
                        <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                            <div class="font-medium">{{ $audit->ip_address ?? '-' }}</div>
                            <div class="truncate max-w-[140px] text-gray-400">{{ Str::limit($audit->user_agent ?? '-', 30) }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="font-medium text-gray-900">No audit logs found</p>
                            <p class="mt-1">Try adjusting your filters or check back later.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($audits->hasPages())
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $audits->links() }}
        </div>
        @endif
    </div>
</div>
@endsection