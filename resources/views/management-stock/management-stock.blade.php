@extends('layout.main')
@section('title', 'Management Stock')
@section('container')
<style>
/* ==========================================
   🎨 Soft UI Global Styles
   ========================================== */
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap');

.soft-ui-page {
    font-family: 'Nunito', 'Poppins', sans-serif;
    background-color: #F8FAFC;
    color: #334155;
    min-height: 100vh;
    padding: 24px;
}

.soft-ui-page h1, .soft-ui-page h2, .soft-ui-page h3, .soft-ui-page h4 {
    color: #1E293B;
    font-weight: 800;
    letter-spacing: -0.02em;
}

/* Card Container */
.soft-card {
    background: #FFFFFF;
    border-radius: 28px;
    box-shadow: 0px 15px 35px rgba(0,0,0,0.04);
    padding: 24px;
    margin-bottom: 24px;
    border: none;
    transition: all 0.3s ease;
}

/* Buttons - Pill shaped */
.soft-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
    font-family: 'Nunito', sans-serif;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    text-decoration: none;
    box-shadow: 0px 4px 15px rgba(0,0,0,0.08);
}

.soft-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 20px rgba(0,0,0,0.12);
}

.soft-btn-primary {
    background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
    color: white;
}

.soft-btn-indigo {
    background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
    color: white;
}

.soft-btn-purple {
    background: linear-gradient(135deg, #A855F7 0%, #9333EA 100%);
    color: white;
}

.soft-btn-emerald {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    color: white;
}

.soft-btn-amber {
    background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
    color: white;
}

/* Input Fields - Pill shaped, soft background */
.soft-input {
    background: #F1F5F9;
    border: none !important;
    border-radius: 50px;
    padding: 12px 20px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #334155;
    width: 100%;
    transition: all 0.3s ease;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    font-family: 'Nunito', sans-serif;
}

.soft-input:focus {
    outline: none;
    background: #FFFFFF;
    box-shadow: 0 0 0 4px rgba(99,102,241,0.1), inset 0 2px 4px rgba(0,0,0,0);
}

select.soft-input {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 16px center;
    background-repeat: no-repeat;
    background-size: 16px;
    padding-right: 40px;
}

/* Table Styles */
.soft-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.soft-table thead th {
    padding: 16px 20px;
    text-align: left;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #94A3B8;
    border-bottom: 2px solid #F1F5F9;
}

.soft-table tbody tr {
    transition: all 0.2s ease;
}

.soft-table tbody tr:hover {
    background: #F8FAFC;
}

.soft-table tbody td {
    padding: 16px 20px;
    border-bottom: 1px solid #F1F5F9;
    font-size: 0.9rem;
    color: #334155;
}

/* Status Badges - Pill shaped */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
}

.status-active {
    background: #DCFCE7;
    color: #166534;
}

.status-inactive {
    background: #F1F5F9;
    color: #64748B;
}

/* Action Buttons in Table */
.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 8px 14px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    transition: all 0.2s ease;
    text-decoration: none;
    border: none;
    cursor: pointer;
}

.action-btn:hover {
    transform: translateY(-1px);
}

.action-btn-view {
    background: #DBEAFE;
    color: #1E40AF;
}

.action-btn-view:hover {
    background: #BFDBFE;
}

.action-btn-edit {
    background: #E0E7FF;
    color: #3730A3;
}

.action-btn-edit:hover {
    background: #C7D2FE;
}

.action-btn-stock {
    background: #D1FAE5;
    color: #065F46;
}

.action-btn-stock:hover {
    background: #A7F3D0;
}

.action-btn-toggle {
    background: #FEF3C7;
    color: #92400E;
    padding: 8px;
}

.action-btn-toggle:hover {
    background: #FDE68A;
}

.action-btn-delete {
    background: #FEE2E2;
    color: #991B1B;
    padding: 8px;
}

.action-btn-delete:hover {
    background: #FECACA;
}

/* Modal Styles */
.soft-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.3);
    backdrop-filter: blur(4px);
    z-index: 50;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding-top: 96px;
}

.soft-modal-overlay.hidden {
    display: none !important;
}

.soft-modal {
    background: #FFFFFF;
    border-radius: 28px;
    box-shadow: 0px 25px 60px rgba(0,0,0,0.15);
    overflow: hidden;
    max-width: 480px;
    width: 100%;
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.soft-modal-header {
    padding: 24px 28px;
    border-bottom: 1px solid #F1F5F9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.soft-modal-header h3 {
    font-size: 1.1rem;
    font-weight: 800;
    color: #1E293B;
}

.soft-modal-body {
    padding: 28px;
}

.soft-modal-footer {
    padding: 20px 28px;
    border-top: 1px solid #F1F5F9;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}
</style>

<div class="soft-ui-page">
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Reagent Inventory</h2>
            <p class="text-sm font-medium text-slate-500 mt-1">Manage stock, track movements, and organize reagents.</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <a href="{{ url('add-reagen') }}" class="soft-btn soft-btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add New
            </a>
            <button type="button" id="openGroupModal" class="soft-btn soft-btn-indigo">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14m7-7H5"/>
                </svg>
                + Group
            </button>
            <button type="button" id="openCategoryModal" class="soft-btn soft-btn-purple">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14m7-7H5"/>
                </svg>
                + Category
            </button>
            <a href="reagen-in" class="soft-btn soft-btn-emerald">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Stock In
            </a>
            <a href="reagen-out" class="soft-btn soft-btn-amber">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                </svg>
                Stock Out
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="soft-card mb-6">
        <form method="GET" action="{{ route('management-stock.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-1">
                <input type="text" name="keyword" value="{{ request('keyword') }}" 
                    placeholder="🔍 Search reagent..." 
                    class="soft-input">
            </div>
            <div>
                <select name="group_guid" class="soft-input">
                    <option value="">All Groups</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->guid }}" {{ request('group_guid') == $group->guid ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="category_guid" class="soft-input">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->guid }}" {{ request('category_guid') == $category->guid ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="soft-input">
                    <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>✅ Active Only</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>❌ Inactive Only</option>
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>📋 All Status</option>
                </select>
            </div>
            <div class="md:col-span-4 flex gap-3">
                <button type="submit" class="soft-btn soft-btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Apply Filter
                </button>
                <a href="{{ route('management-stock.index') }}" class="soft-btn" style="background: #F1F5F9; color: #475569;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Reagen List -->
    <div class="soft-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="soft-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Catalog</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Stock</th>
                        <th>MSDS</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reagens as $reagen)
                    <tr class="{{ !$reagen->is_active ? 'opacity-60' : '' }}">
                        <td>
                            <span class="status-badge {{ $reagen->is_active ? 'status-active' : 'status-inactive' }}">
                                <span class="w-2 h-2 rounded-full {{ $reagen->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $reagen->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="font-mono text-xs text-slate-500">{{ $reagen->noCatalog }}</td>
                        <td class="font-bold text-slate-800">
                            <div class="flex items-center gap-2">
                                <span>{{ $reagen->nameReagen }}</span>
                                @if(!$reagen->latestReagenMsds)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-100 text-red-700" title="Belum ada dokumen MSDS">
                                        NO MSDS
                                    </span>
                                @elseif($reagen->latestReagenMsds->needsReview())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 animate-pulse" title="MSDS perlu direview (>1 tahun)">
                                        ⚠️ REVIEW
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="text-slate-600">{{ $reagen->merk }}</td>
                        <td class="font-bold text-slate-800">{{ $reagen->stockReagen->quantity ?? 0 }}</td>
                        <td>
                            @php
                                $latestMsds = $reagen->latestReagenMsds;
                            @endphp
                            @if(!$latestMsds)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200" title="Belum ada file MSDS yang diupload">
                                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Missing
                                </span>
                            @elseif($latestMsds->needsReview())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300" title="Dokumen MSDS sudah berusia >1 tahun & perlu direview">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                    </span>
                                    Needs Review
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="MSDS Aktif & Valid">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    OK
                                </span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('reagen.toggle-status', $reagen->guid) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="action-btn action-btn-toggle"
                                            title="{{ $reagen->is_active ? 'Non-aktifkan' : 'Aktifkan' }}"
                                            onclick="return confirm('{{ $reagen->is_active ? 'Non-aktifkan' : 'Aktifkan' }} reagen ini?')">
                                        @if($reagen->is_active)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                                <a href="{{ route('data.view', $reagen->guid) }}" class="action-btn action-btn-view">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ route('data.edit', $reagen->guid) }}" class="action-btn action-btn-edit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                                <a href="{{ route('reagen.addstock', $reagen->guid) }}" class="action-btn action-btn-stock">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Stock
                                </a>
                                <form action="{{ route('data.delete', $reagen->guid) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="action-btn action-btn-delete confirm-button" title="Delete Reagent">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 48px 20px;">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4 text-slate-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7m16 0v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-5m16 0h-2.586a1 1 0 0 0-.707.293l-2.414 2.414a1 1 0 0 1-.707.293h-3.172a1 1 0 0 1-.707-.293l-2.414-2.414A1 1 0 0 0 6.586 13H4"/>
                                    </svg>
                                </div>
                                <p class="text-slate-500 font-bold">No reagents found</p>
                                <p class="text-sm text-slate-400 mt-1">Try adjusting your filters or add a new reagent.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        @if($reagens->hasPages())
        <div style="padding: 20px 24px; border-top: 1px solid #F1F5F9;">
            {{ $reagens->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Group Modal -->
<div id="groupModal" class="soft-modal-overlay hidden">
    <div class="soft-modal">
        <div class="soft-modal-header">
            <h3>Add Group</h3>
            <button type="button" class="text-slate-400 hover:text-slate-600" data-close-modal-group>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="groupForm" method="POST" action="{{ route('group.store') }}" class="soft-modal-body">
            @csrf
            <div>
                <label class="label-soft">Group Name <span class="text-red-400">*</span></label>
                <input type="text" name="name" required class="soft-input">
                @if($errors->has('name'))
                    <p class="text-sm text-red-600 mt-2">{{ $errors->first('name') }}</p>
                @endif
            </div>
        </form>
        <div class="soft-modal-footer">
            <button type="button" class="soft-btn" style="background: #F1F5F9; color: #475569;" data-close-modal-group>Cancel</button>
            <button type="submit" form="groupForm" class="soft-btn soft-btn-indigo">Save Group</button>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div id="categoryModal" class="soft-modal-overlay hidden">
    <div class="soft-modal">
        <div class="soft-modal-header">
            <h3>Add Category</h3>
            <button type="button" class="text-slate-400 hover:text-slate-600" data-close-modal-category>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="categoryForm" method="POST" action="{{ route('category.store') }}" class="soft-modal-body">
            @csrf
            <div>
                <label class="label-soft">Category Name <span class="text-red-400">*</span></label>
                <input type="text" name="name" required class="soft-input">
                @if($errors->has('name'))
                    <p class="text-sm text-red-600 mt-2">{{ $errors->first('name') }}</p>
                @endif
            </div>
        </form>
        <div class="soft-modal-footer">
            <button type="button" class="soft-btn" style="background: #F1F5F9; color: #475569;" data-close-modal-category>Cancel</button>
            <button type="submit" form="categoryForm" class="soft-btn soft-btn-purple">Save Category</button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Delete Confirmation
document.querySelectorAll('.confirm-button').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault();
        const form = this.closest('form');
        Swal.fire({
            title: 'Delete Reagent?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it',
            background: '#fff',
            customClass: { popup: 'rounded-3xl' }
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });
});

// Modal Logic
const openGroupBtn = document.getElementById('openGroupModal');
const openCategoryBtn = document.getElementById('openCategoryModal');
const groupModal = document.getElementById('groupModal');
const categoryModal = document.getElementById('categoryModal');

function openModal(modalEl) {
    if (!modalEl) return;
    modalEl.classList.remove('hidden');
}

function closeModal(modalEl) {
    if (!modalEl) return;
    modalEl.classList.add('hidden');
}

if (openGroupBtn) openGroupBtn.addEventListener('click', () => openModal(groupModal));
if (openCategoryBtn) openCategoryBtn.addEventListener('click', () => openModal(categoryModal));

document.querySelectorAll('[data-close-modal-group]').forEach(el => {
    el.addEventListener('click', () => closeModal(groupModal));
});

document.querySelectorAll('[data-close-modal-category]').forEach(el => {
    el.addEventListener('click', () => closeModal(categoryModal));
});

[groupModal, categoryModal].forEach(modal => {
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(modal);
        });
    }
});
</script>
@endpush
@endsection