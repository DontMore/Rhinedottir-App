@extends('layout.main')

@section('container')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
        <a href="{{ route('user.register') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
            <i class="bi bi-plus-circle mr-2"></i> Create User
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
        <div class="p-5">
            <form method="GET" action="{{ route('user.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Search</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50 focus:bg-white transition-colors" 
                               placeholder="Username, Name, or Email..." 
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Filter by Role</label>
                    <select name="role" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md text-sm shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('role') == 'all' || !request('role') ? 'selected' : '' }}>All Roles</option>
                        <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Analis" {{ request('role') == 'Analis' ? 'selected' : '' }}>Analis</option>
                        <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Filter by Status</label>
                    <select name="status" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md text-sm shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-colors">
                        <i class="bi bi-search mr-1"></i> Search
                    </button>
                    <a href="{{ route('user.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors" title="Reset filters">
                        <i class="bi bi-x-circle text-lg leading-none"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">#</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Username</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $index => $user)
                    <tr class="hover:bg-gray-50 transition-colors {{ !$user->is_active ? 'bg-gray-50 opacity-80' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ $user->username }}</span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $user->name }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ Str::limit($user->email, 30) }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $roleClass = match(strtolower($user->role)) {
                                    'admin' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'analis' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                    'superadmin' => 'bg-red-100 text-red-800 border-red-200',
                                    default => 'bg-gray-100 text-gray-800 border-gray-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $roleClass }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <form action="{{ route('user.toggle-status', $user->guid) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="focus:outline-none transition-transform hover:scale-110 {{ $user->is_active ? 'text-green-500' : 'text-gray-400' }}"
                                        title="{{ $user->is_active ? 'Click to deactivate' : 'Click to activate' }}"
                                        onclick="return confirm('Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this user?')">
                                    @if($user->is_active)
                                        <i class="bi bi-toggle-on text-2xl block leading-none"></i>
                                        <span class="text-xs font-medium text-green-600 mt-1 block">Active</span>
                                    @else
                                        <i class="bi bi-toggle-off text-2xl block leading-none"></i>
                                        <span class="text-xs font-medium text-gray-500 mt-1 block">Inactive</span>
                                    @endif
                                </button>
                            </form>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('user.edit', $user->guid) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 rounded bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-900 transition-colors" 
                                   title="Edit User">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('user.delete', $user->guid) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            class="delete-btn inline-flex items-center justify-center w-8 h-8 rounded bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-900 transition-colors" 
                                            title="Delete User">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i class="bi bi-inbox text-5xl text-gray-300 block mb-3"></i>
                            <p class="text-gray-500 text-sm mb-1">No users found.</p>
                            @if(request()->hasAny(['search', 'role', 'status']))
                                <p class="text-xs text-gray-400">Try adjusting your search or filters.</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
            <span class="text-sm text-gray-700">
                Showing <span class="font-medium">{{ $users->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $users->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $users->total() }}</span> results
            </span>
            <div class="w-full sm:w-auto">
                {{ $users->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');

            Swal.fire({
                title: 'Delete User?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Tailwind red-500
                cancelButtonColor: '#6b7280', // Tailwind gray-500
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection