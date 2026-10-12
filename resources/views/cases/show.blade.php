<x-appLayout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
            <div>
                <div class="flex items-center space-x-3">
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        Case: {{ $case->case_number }}
                    </h2>
                    @php
                        $statusClass = match($case->status) {
                            'Approved' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                            'Completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'Planning' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'In Review' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'Cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                            default => 'bg-gray-100 text-gray-800 border-gray-200',
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold border {{ $statusClass }}">
                        {{ $case->status }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-0.5">
                    Patient Reference: <span class="font-mono font-medium text-gray-700">{{ $case->patient_reference }}</span>
                </p>
            </div>

            <div class="flex items-center space-x-2">
                <a href="{{ route('cases.index') }}" class="inline-flex items-center px-3.5 py-2 border border-gray-300 rounded-md text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                    &larr; Back to Directory
                </a>

                @can('update', $case)
                    <a href="{{ route('cases.edit', $case) }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-600 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Edit Case
                    </a>
                @endcan

                @can('delete', $case)
                    <form method="POST" action="{{ route('cases.destroy', $case) }}" onsubmit="return confirm('Are you sure you want to permanently delete medical case {{ $case->case_number }}? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3.5 py-2 bg-rose-600 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-widest hover:bg-rose-700 transition">
                            Delete Case
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Main Case Details Card -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Clinical &amp; Device Specifications</h3>
                <span class="text-xs text-gray-400">Database Record ID #{{ $case->id }}</span>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                <!-- Case Number -->
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Case Number</dt>
                    <dd class="mt-1 text-base font-bold text-gray-900 font-mono">{{ $case->case_number }}</dd>
                </div>

                <!-- Patient Reference -->
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Patient Reference Code</dt>
                    <dd class="mt-1 text-base font-semibold text-gray-800 font-mono bg-gray-50 px-2 py-0.5 rounded inline-block border border-gray-200">
                        {{ $case->patient_reference }}
                    </dd>
                </div>

                <!-- Surgeon Name -->
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Operating Surgeon</dt>
                    <dd class="mt-1 text-base font-semibold text-gray-900 flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ $case->surgeon_name }}
                    </dd>
                </div>

                <!-- Implant Type -->
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Implant Device</dt>
                    <dd class="mt-1 text-base font-semibold text-gray-900 flex items-center">
                        <svg class="w-4 h-4 text-indigo-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        {{ $case->implant_type }}
                    </dd>
                </div>

                <!-- Status -->
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
                            {{ $case->status }}
                        </span>
                    </dd>
                </div>

                <!-- Priority -->
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Priority Level</dt>
                    <dd class="mt-1">
                        @php
                            $priorityClass = match($case->priority) {
                                'High' => 'text-rose-700 bg-rose-50 border-rose-200',
                                'Medium' => 'text-amber-700 bg-amber-50 border-amber-200',
                                default => 'text-gray-700 bg-gray-50 border-gray-200',
                            };
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded text-xs font-semibold border {{ $priorityClass }}">
                            {{ $case->priority }}
                        </span>
                    </dd>
                </div>

                <!-- Surgery Date -->
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Surgery Date</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900 flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        @if ($case->surgery_date)
                            {{ $case->surgery_date->format('l, F j, Y') }}
                            <span class="text-xs text-gray-400 ml-2">({{ $case->surgery_date->diffForHumans() }})</span>
                        @else
                            <span class="text-gray-400 italic">Not scheduled</span>
                        @endif
                    </dd>
                </div>
            </div>
        </div>

        <!-- Audit & Creator Metadata Card -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">
                Audit Trail &amp; Authorship
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <!-- Created By -->
                <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                    <span class="text-gray-500 block mb-1">Created By</span>
                    <span class="font-semibold text-gray-900 block text-sm">{{ $case->creator?->name ?? 'System' }}</span>
                    <span class="text-gray-500 block">{{ $case->creator?->email ?? 'N/A' }}</span>
                    <span class="inline-block mt-1 uppercase text-[10px] font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded">
                        Role: {{ $case->creator?->role ?? 'N/A' }}
                    </span>
                </div>

                <!-- Created At -->
                <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                    <span class="text-gray-500 block mb-1">Created At</span>
                    <span class="font-semibold text-gray-900 block text-sm">{{ $case->created_at->format('M d, Y H:i:s') }}</span>
                    <span class="text-gray-500 block">({{ $case->created_at->diffForHumans() }})</span>
                </div>

                <!-- Last Updated -->
                <div class="p-3 bg-gray-50 rounded-md border border-gray-100">
                    <span class="text-gray-500 block mb-1">Last Updated</span>
                    <span class="font-semibold text-gray-900 block text-sm">{{ $case->updated_at->format('M d, Y H:i:s') }}</span>
                    <span class="text-gray-500 block">({{ $case->updated_at->diffForHumans() }})</span>
                </div>
            </div>
        </div>
    </div>
</x-appLayout>
