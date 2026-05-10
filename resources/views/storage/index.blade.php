@extends('layout.main')
@section('title', 'Storage Locations')
@section('container')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Storage Locations</h1>
            <p class="text-sm text-gray-500 mt-1">Manage lab storage cabinets organized by physical state & hazard classification.</p>
        </div>
        <button onclick="openModal('add')" class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add Location
        </button>
    </div>

    <!-- Stats / Quick Filter -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $solidCount = $locations->where('state_type', 'solid')->count();
            $liquidCount = $locations->where('state_type', 'liquid')->count();
        @endphp
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase">Padatan (Solid)</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $solidCount }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase">Cairan (Liquid)</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $liquidCount }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm col-span-2">
            <p class="text-xs text-gray-500 uppercase">Total Capacity Usage</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $locations->sum('reagen_in_count') }} <span class="text-sm font-normal text-gray-400">/ {{ $locations->sum('capacity') ?: '∞' }} slots</span></p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Allowed Hazards</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Usage</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($locations as $loc)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-sm font-medium text-gray-900">{{ $loc->code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $loc->name }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $loc->state_type == 'solid' ? 'bg-purple-50 text-purple-700 ring-1 ring-purple-600/20' : 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20' }}">
                                {{ $loc->state_type == 'solid' ? 'Padatan' : 'Cairan' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach(($loc->allowed_hazards ?? []) as $h)
                                    <span class="text-[10px] px-2 py-0.5 bg-gray-100 text-gray-600 rounded border border-gray-200">{{ $h }}</span>
                                @endforeach
                                @if(empty($loc->allowed_hazards))
                                    <span class="text-xs text-gray-400 italic">All</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600">
                            {{ $loc->reagen_in_count }} / {{ $loc->capacity ?? '∞' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="openModal('edit', '{{ $loc->guid }}', '{{ $loc->code }}', '{{ $loc->name }}', '{{ $loc->state_type }}', '{{ $loc->capacity }}', '{{ json_encode($loc->allowed_hazards ?? []) }}', '{{ $loc->description }}')" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</button>
                                <form action="{{ route('storage.delete', $loc->guid) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No storage locations configured yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($locations->hasPages())
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">{{ $locations->links() }}</div>
        @endif
    </div>
</div>

<!-- ✅ ADD/EDIT MODAL -->
<div id="storageModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-800">Add Storage Location</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form id="storageForm" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State Type <span class="text-red-500">*</span></label>
                    <select name="state_type" id="state_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="solid">Padatan (Solid)</option>
                        <option value="liquid">Cairan (Liquid)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacity (Slots)</label>
                    <input type="number" name="capacity" id="capacity" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Leave empty for unlimited">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Allowed Hazards</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($hazardOptions as $h)
                    <label class="flex items-center space-x-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="allowed_hazards[]" value="{{ $h }}" id="haz_{{ $loop->index }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span>{{ $h }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
            </div>
            <div class="pt-2 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm">Save</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openModal(mode, guid = '', code = '', name = '', type = 'solid', cap = '', hazards = '[]', desc = '') {
    document.getElementById('modalTitle').textContent = mode === 'add' ? 'Add Storage Location' : 'Edit Storage Location';
    document.getElementById('formMethod').value = mode === 'add' ? 'POST' : 'PUT';
    document.getElementById('storageForm').action = mode === 'add' ? '{{ route('storage.store') }}' : '{{ url('storage-location') }}/' + guid;
    
    document.getElementById('code').value = code;
    document.getElementById('name').value = name;
    document.getElementById('state_type').value = type;
    document.getElementById('capacity').value = cap === 'null' ? '' : cap;
    document.getElementById('description').value = desc;

    // Reset & set hazards
    document.querySelectorAll('input[name="allowed_hazards[]"]').forEach(cb => cb.checked = false);
    if (hazards !== '[]') {
        JSON.parse(hazards).forEach(h => {
            const cb = document.querySelector(`input[name="allowed_hazards[]"][value="${h}"]`);
            if (cb) cb.checked = true;
        });
    }

    document.getElementById('storageModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('storageModal').classList.add('hidden');
}
</script>
@endpush
@endsection