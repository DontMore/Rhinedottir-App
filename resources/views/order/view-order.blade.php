@extends('layout.main')
@section('container')

<div class="max-w-4xl mx-auto px-4 py-6">
    {{-- Header Section --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Order Detail</h1>
        <a href="{{ route('order.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-gray-200">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    {{-- Main Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8 space-y-6">
            
            {{-- Info Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Catalog No</label>
                    <div class="px-3 py-2 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-sm font-medium text-gray-900 font-mono">{{ $order->noCatalog ?? '-' }}</p>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Reagent Name</label>
                    <div class="px-3 py-2 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-sm font-medium text-gray-900">{{ $order->nameReagen ?? '-' }}</p>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Brand</label>
                    <div class="px-3 py-2 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-sm font-medium text-gray-900">{{ $order->merk ?? '-' }}</p>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pack Size</label>
                    <div class="px-3 py-2 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-sm font-medium text-gray-900">{{ $order->packSize ?? '-' }}</p>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Quantity</label>
                    <div class="px-3 py-2 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-sm font-medium text-gray-900">{{ $order->quantity ?? '-' }}</p>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</label>
                    <div class="px-3 py-2 bg-gray-50 rounded-lg border border-gray-100">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status == 0 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $order->status == 0 ? 'Pending' : 'Completed' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Metadata Section --}}
            <div class="pt-4 border-t border-gray-100 flex flex-wrap gap-4 text-sm text-gray-500">
                <div>
                    <span class="font-medium text-gray-700">Created:</span> {{ $order->created_at ? $order->created_at->format('d M Y, H:i') : '-' }}
                </div>
                <div class="hidden sm:block text-gray-300">•</div>
                <div>
                    <span class="font-medium text-gray-700">Days Passed:</span> <span class="font-semibold text-gray-900">{{ $daysPassed ?? 0 }}</span>
                </div>
            </div>

            {{-- Update Status Section (Modern Segmented Control) --}}
            @if($order->status == 0)
            <div class="pt-4 border-t border-gray-100">
                <form action="{{ route('order.update', $order->guid) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-3">Update Status</label>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        {{-- Pending Option --}}
                        <label class="relative flex-1 cursor-pointer group">
                            <input type="radio" name="status" value="0" class="peer sr-only" {{ $order->status == 0 ? 'checked' : '' }}>
                            <div class="flex items-center justify-center px-4 py-3 border-2 border-gray-200 rounded-lg text-gray-500 font-medium transition-all duration-200 hover:bg-gray-50 peer-checked:border-amber-500 peer-checked:text-amber-700 peer-checked:bg-amber-50">
                                <span class="w-2.5 h-2.5 mr-2 rounded-full bg-gray-300 peer-checked:bg-amber-500 transition-colors"></span>
                                Pending
                            </div>
                        </label>
                        
                        {{-- Completed Option --}}
                        <label class="relative flex-1 cursor-pointer group">
                            <input type="radio" name="status" value="1" class="peer sr-only">
                            <div class="flex items-center justify-center px-4 py-3 border-2 border-gray-200 rounded-lg text-gray-500 font-medium transition-all duration-200 hover:bg-gray-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 peer-checked:bg-emerald-50">
                                <span class="w-2.5 h-2.5 mr-2 rounded-full bg-gray-300 peer-checked:bg-emerald-500 transition-colors"></span>
                                Completed
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:scale-95 transform duration-150">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection