<x-appLayout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Implant Case Directory') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Search, filter, and track orthopedic and implant surgical cases
                </p>
            </div>
            @can('create', App\Models\MedicalCase::class)
                <a href="{{ route('cases.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Case
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Search and Filter Bar -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
            <form method="GET" action="{{ route('cases.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <!-- Search input -->
                <div class="md:col-span-5">
                    <label for="search" class="block text-xs font-semibold text-gray-700 mb-1">Search Cases</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Search by Case Number or Surgeon..."
                               class="w-full pl-9 pr-3 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="md:col-span-3">
                    <label for="status" class="block text-xs font-semibold text-gray-700 mb-1">Filter by Status</label>
                    <select name="status"
                            id="status"
                            class="w-full py-2 px-3 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Statuses</option>
                        @foreach(App\Models\MedicalCase::STATUSES as $optStatus)
                            <option value="{{ $optStatus }}" {{ request('status') === $optStatus ? 'selected' : '' }}>
                                {{ $optStatus }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Priority Filter (Bonus) -->
                <div class="md:col-span-2">
                    <label for="priority" class="block text-xs font-semibold text-gray-700 mb-1">Priority</label>
                    <select name="priority"
                            id="priority"
                            class="w-full py-2 px-3 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Priorities</option>
                        @foreach(App\Models\MedicalCase::PRIORITIES as $optPriority)
                            <option value="{{ $optPriority }}" {{ request('priority') === $optPriority ? 'selected' : '' }}>
                                {{ $optPriority }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="md:col-span-2 flex space-x-2">
                    <button type="submit"
                            class="flex-1 py-2 px-3 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700 focus:outline-none shadow-sm transition text-center">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'priority', 'sort']))
                        <a href="{{ route('cases.index') }}"
                           class="py-2 px-3 bg-gray-100 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-200 focus:outline-none transition text-center">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Cases Table Card -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    @php
                        // Helper for sort URLs
                        function sortUrl($column, $currentSort, $currentDirection) {
                            $direction = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';
                            $params = array_merge(request()->query(), ['sort' => $column, 'direction' => $direction]);
                            return route('cases.index', $params);
                        }
                    @endphp
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 text-left">
                                <a href="{{ sortUrl('case_number', $sort, $direction) }}" class="flex items-center space-x-1 hover:text-gray-700">
                                    <span>Case Number</span>
                                    @if($sort === 'case_number')
                                        <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3.5 text-left">Patient Ref</th>
                            <th class="px-6 py-3.5 text-left">
                                <a href="{{ sortUrl('surgeon_name', $sort, $direction) }}" class="flex items-center space-x-1 hover:text-gray-700">
                                    <span>Surgeon Name</span>
                                    @if($sort === 'surgeon_name')
                                        <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3.5 text-left">Implant Type</th>
                            <th class="px-6 py-3.5 text-left">
                                <a href="{{ sortUrl('status', $sort, $direction) }}" class="flex items-center space-x-1 hover:text-gray-700">
                                    <span>Status</span>
                                    @if($sort === 'status')
                                        <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3.5 text-left">
                                <a href="{{ sortUrl('priority', $sort, $direction) }}" class="flex items-center space-x-1 hover:text-gray-700">
                                    <span>Priority</span>
                                    @if($sort === 'priority')
                                        <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3.5 text-left">
                                <a href="{{ sortUrl('surgery_date', $sort, $direction) }}" class="flex items-center space-x-1 hover:text-gray-700">
                                    <span>Surgery Date</span>
                                    @if($sort === 'surgery_date')
                                        <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($cases as $case)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-semibold text-indigo-600 whitespace-nowrap">
                                    <a href="{{ route('cases.show', $case) }}" class="hover:underline">
                                        {{ $case->case_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-gray-700 font-mono text-xs whitespace-nowrap">
                                    {{ $case->patient_reference }}
                                </td>
                                <td class="px-6 py-4 text-gray-900 font-medium whitespace-nowrap">
                                    {{ $case->surgeon_name }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                    {{ $case->implant_type }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClass = match($case->status) {
                                            'Approved' => 'bg-indigo-100 text-indigo-800',
                                            'Completed' => 'bg-emerald-100 text-emerald-800',
                                            'Planning' => 'bg-amber-100 text-amber-800',
                                            'In Review' => 'bg-blue-100 text-blue-800',
                                            'Cancelled' => 'bg-rose-100 text-rose-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $case->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $priorityClass = match($case->priority) {
                                            'High' => 'text-rose-700 bg-rose-50 border-rose-200',
                                            'Medium' => 'text-amber-700 bg-amber-50 border-amber-200',
                                            default => 'text-gray-700 bg-gray-50 border-gray-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $priorityClass }}">
                                        {{ $case->priority }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap text-xs">
                                    {{ $case->surgery_date ? $case->surgery_date->format('M d, Y') : '—' }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap text-xs space-x-2">
                                    <!-- View button (Admin, Viewer, Engineer) -->
                                    <a href="{{ route('cases.show', $case) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 rounded text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                                        View
                                    </a>

                                    <!-- Edit button (Admin, Engineer) -->
                                    @can('update', $case)
                                        <a href="{{ route('cases.edit', $case) }}"
                                           class="inline-flex items-center px-2.5 py-1.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                            Edit
                                        </a>
                                    @endcan

                                    <!-- Delete button (Admin ONLY) -->
                                    @can('delete', $case)
                                        <form method="POST" action="{{ route('cases.destroy', $case) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to permanently delete case {{ $case->case_number }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center px-2.5 py-1.5 rounded text-xs font-medium bg-rose-50 text-rose-700 hover:bg-rose-100 transition">
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <div class="max-w-sm mx-auto">
                                        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm font-medium text-gray-900">No medical cases found</p>
                                        <p class="text-xs text-gray-500 mt-1">Try adjusting your search terms or filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar (10 per page) -->
            @if ($cases->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $cases->links() }}
                </div>
            @endif
        </div>
    </div>
</x-appLayout>
