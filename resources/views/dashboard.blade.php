<x-appLayout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Clinical Case Dashboard') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Real-time implant tracking and surgery schedule overview
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('cases.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none transition">
                    View All Cases
                </a>
                @can('create', App\Models\MedicalCase::class)
                    <a href="{{ route('cases.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 shadow-sm transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create New Case
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- User Role Notice / Banner -->
        <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">
                        Signed in as {{ Auth::user()->name }}
                    </h3>
                    <p class="text-xs text-gray-500">
                        @if(Auth::user()->isAdmin())
                            <span class="text-purple-700 font-medium">Administrator privileges:</span> Full access to create, edit, change statuses, and delete cases.
                        @elseif(Auth::user()->isEngineer())
                            <span class="text-blue-700 font-medium">Engineer privileges:</span> Authorized to create, edit, and transition case statuses. Deletion restricted to Admin.
                        @else
                            <span class="text-emerald-700 font-medium">Viewer privileges:</span> Read-only access to browse, search, and view case records.
                        @endif
                    </p>
                </div>
            </div>
            <div class="mt-3 sm:mt-0">
                <span class="text-xs text-gray-400">Current System Time: {{ now()->format('M d, Y') }}</span>
            </div>
        </div>

        <!-- Metric Stat Cards (Required: Total, Planning, In Review, Approved, Completed) -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <!-- Total Cases -->
            <div class="col-span-2 md:col-span-1 bg-white p-5 rounded-lg border border-gray-200 shadow-sm hover:shadow transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Cases</span>
                    <span class="p-1.5 bg-gray-100 rounded text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline">
                    <span class="text-3xl font-bold text-gray-900">{{ $counts['total'] }}</span>
                </div>
                <a href="{{ route('cases.index') }}" class="mt-2 block text-xs font-medium text-indigo-600 hover:text-indigo-800">
                    View registry &rarr;
                </a>
            </div>

            <!-- Planning -->
            <div class="bg-white p-5 rounded-lg border border-amber-200 shadow-sm hover:shadow transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-amber-700">Planning</span>
                    <span class="p-1.5 bg-amber-50 rounded text-amber-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline">
                    <span class="text-3xl font-bold text-amber-900">{{ $counts['planning'] }}</span>
                </div>
                <a href="{{ route('cases.index', ['status' => 'Planning']) }}" class="mt-2 block text-xs font-medium text-amber-600 hover:text-amber-800">
                    Filter planning &rarr;
                </a>
            </div>

            <!-- In Review -->
            <div class="bg-white p-5 rounded-lg border border-blue-200 shadow-sm hover:shadow transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-blue-700">In Review</span>
                    <span class="p-1.5 bg-blue-50 rounded text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline">
                    <span class="text-3xl font-bold text-blue-900">{{ $counts['in_review'] }}</span>
                </div>
                <a href="{{ route('cases.index', ['status' => 'In Review']) }}" class="mt-2 block text-xs font-medium text-blue-600 hover:text-blue-800">
                    Filter review &rarr;
                </a>
            </div>

            <!-- Approved -->
            <div class="bg-white p-5 rounded-lg border border-indigo-200 shadow-sm hover:shadow transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-indigo-700">Approved</span>
                    <span class="p-1.5 bg-indigo-50 rounded text-indigo-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline">
                    <span class="text-3xl font-bold text-indigo-900">{{ $counts['approved'] }}</span>
                </div>
                <a href="{{ route('cases.index', ['status' => 'Approved']) }}" class="mt-2 block text-xs font-medium text-indigo-600 hover:text-indigo-800">
                    Filter approved &rarr;
                </a>
            </div>

            <!-- Completed -->
            <div class="bg-white p-5 rounded-lg border border-emerald-200 shadow-sm hover:shadow transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Completed</span>
                    <span class="p-1.5 bg-emerald-50 rounded text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline">
                    <span class="text-3xl font-bold text-emerald-900">{{ $counts['completed'] }}</span>
                </div>
                <a href="{{ route('cases.index', ['status' => 'Completed']) }}" class="mt-2 block text-xs font-medium text-emerald-600 hover:text-emerald-800">
                    Filter completed &rarr;
                </a>
            </div>
        </div>

        <!-- Secondary Statuses Pill Row -->
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-xs font-medium text-gray-500">Other Statuses:</span>
            <a href="{{ route('cases.index', ['status' => 'Draft']) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200">
                Draft: {{ $counts['draft'] }}
            </a>
            <a href="{{ route('cases.index', ['status' => 'Cancelled']) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 hover:bg-rose-100">
                Cancelled: {{ $counts['cancelled'] }}
            </a>
        </div>

        <!-- Recent Medical Cases Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Recently Logged Implant Cases</h3>
                    <p class="text-xs text-gray-500">Latest medical implant procedures registered in the system</p>
                </div>
                <a href="{{ route('cases.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                    View Complete Case Directory &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left">Case Number</th>
                            <th class="px-6 py-3 text-left">Patient Ref</th>
                            <th class="px-6 py-3 text-left">Surgeon</th>
                            <th class="px-6 py-3 text-left">Implant Type</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Priority</th>
                            <th class="px-6 py-3 text-left">Surgery Date</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($recentCases as $case)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3.5 font-medium text-indigo-600 whitespace-nowrap">
                                    <a href="{{ route('cases.show', $case) }}" class="hover:underline">
                                        {{ $case->case_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-3.5 text-gray-600 font-mono text-xs whitespace-nowrap">
                                    {{ $case->patient_reference }}
                                </td>
                                <td class="px-6 py-3.5 text-gray-900 font-medium whitespace-nowrap">
                                    {{ $case->surgeon_name }}
                                </td>
                                <td class="px-6 py-3.5 text-gray-600 whitespace-nowrap">
                                    {{ $case->implant_type }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
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
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $case->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    @php
                                        $priorityClass = match($case->priority) {
                                            'High' => 'text-rose-700 bg-rose-50 border-rose-200',
                                            'Medium' => 'text-amber-700 bg-amber-50 border-amber-200',
                                            default => 'text-gray-700 bg-gray-50 border-gray-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border {{ $priorityClass }}">
                                        {{ $case->priority }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-gray-600 whitespace-nowrap text-xs">
                                    {{ $case->surgery_date ? $case->surgery_date->format('M d, Y') : '—' }}
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap text-xs space-x-2">
                                    <a href="{{ route('cases.show', $case) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                                        View
                                    </a>
                                    @can('update', $case)
                                        <a href="{{ route('cases.edit', $case) }}" class="font-medium text-gray-600 hover:text-gray-900">
                                            Edit
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    No medical cases logged yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-appLayout>
