@extends('layout.main')

@section('container')
<div class="max-w-2xl mx-auto py-8 px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Organization Details</h1>
        <a href="{{ route('superadmin.index') }}" class="text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Organization Name</label>
                <p class="mt-1 text-sm text-gray-900">{{ $organization->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Note</label>
                <p class="mt-1 text-sm text-gray-900">{{ $organization->note }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
