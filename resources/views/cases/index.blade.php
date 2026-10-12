<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h1 class="h4 fw-bold text-dark mb-1">Medical Case Directory</h1>
                <p class="text-muted small mb-0">Browse, search, and manage clinical implant records across the surgical network</p>
            </div>
            @can('create', App\Models\MedicalCase::class)
                <a href="{{ route('cases.create') }}" class="btn btn-jajal-teal btn-sm shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Create New Case
                </a>
            @endcan
        </div>
    </x-slot>

    <!-- Search & Filter Card -->
    <div class="card jm-card mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('cases.index') }}" class="row g-3 align-items-end">
                <!-- Search Term -->
                <div class="col-12 col-md-4">
                    <label for="search" class="form-label text-muted small fw-semibold">Search Cases</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}" 
                               class="form-control" 
                               placeholder="Search case # or surgeon name...">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="col-6 col-md-3">
                    <label for="status" class="form-label text-muted small fw-semibold">Case Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(App\Models\MedicalCase::STATUSES as $optStatus)
                            <option value="{{ $optStatus }}" {{ request('status') === $optStatus ? 'selected' : '' }}>
                                {{ $optStatus }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Priority Filter (Bonus) -->
                <div class="col-6 col-md-2">
                    <label for="priority" class="form-label text-muted small fw-semibold">Priority</label>
                    <select name="priority" id="priority" class="form-select">
                        <option value="">All Priorities</option>
                        @foreach(App\Models\MedicalCase::PRIORITIES as $optPriority)
                            <option value="{{ $optPriority }}" {{ request('priority') === $optPriority ? 'selected' : '' }}>
                                {{ $optPriority }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-jajal-primary flex-grow-1">
                        <i class="bi bi-funnel me-1"></i> Apply Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'priority', 'sort']))
                        <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary" title="Reset all filters">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Cases Table Card -->
    <div class="card jm-card overflow-hidden">
        <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
            <div>
                <h5 class="card-title h6 fw-bold mb-0 text-dark">
                    <i class="bi bi-table me-1 text-primary"></i> Case Registry
                </h5>
                <small class="text-muted">
                    Showing {{ $cases->firstItem() ?? 0 }} - {{ $cases->lastItem() ?? 0 }} of {{ $cases->total() }} recorded surgical cases
                </small>
            </div>
            @if(request()->hasAny(['search', 'status', 'priority']))
                <span class="badge bg-light text-dark border">
                    <i class="bi bi-filter me-1"></i> Active Filters Applied
                </span>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table jm-table align-middle">
                <thead>
                    @php
                        $sortUrl = function ($col) use ($sort, $direction) {
                            $nextDir = ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
                            return route('cases.index', array_merge(request()->query(), ['sort' => $col, 'direction' => $nextDir]));
                        };
                        $sortIcon = function ($col) use ($sort, $direction) {
                            if ($sort !== $col) return '<i class="bi bi-arrow-down-up text-muted ms-1 opacity-50" style="font-size: 0.75rem;"></i>';
                            return $direction === 'asc' 
                                ? '<i class="bi bi-sort-up text-primary ms-1"></i>' 
                                : '<i class="bi bi-sort-down text-primary ms-1"></i>';
                        };
                    @endphp
                    <tr>
                        <th>
                            <a href="{{ $sortUrl('case_number') }}" class="text-decoration-none text-dark">
                                Case Number {!! $sortIcon('case_number') !!}
                            </a>
                        </th>
                        <th>Patient Ref</th>
                        <th>
                            <a href="{{ $sortUrl('surgeon_name') }}" class="text-decoration-none text-dark">
                                Surgeon {!! $sortIcon('surgeon_name') !!}
                            </a>
                        </th>
                        <th>Implant Device</th>
                        <th>
                            <a href="{{ $sortUrl('status') }}" class="text-decoration-none text-dark">
                                Status {!! $sortIcon('status') !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ $sortUrl('priority') }}" class="text-decoration-none text-dark">
                                Priority {!! $sortIcon('priority') !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ $sortUrl('surgery_date') }}" class="text-decoration-none text-dark">
                                Surgery Date {!! $sortIcon('surgery_date') !!}
                            </a>
                        </th>
                        <th>Created By</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cases as $case)
                        <tr>
                            <td>
                                <a href="{{ route('cases.show', $case) }}" class="fw-bold text-primary font-monospace text-decoration-none">
                                    {{ $case->case_number }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace">{{ $case->patient_reference }}</span>
                            </td>
                            <td>
                                <i class="bi bi-person-fill text-muted me-1"></i>{{ $case->surgeon_name }}
                            </td>
                            <td>
                                <span class="text-dark">{{ $case->implant_type }}</span>
                            </td>
                            <td>
                                @php
                                    $statusBadge = match($case->status) {
                                        'Planning' => 'badge-status-planning',
                                        'In Review' => 'badge-status-review',
                                        'Approved' => 'badge-status-approved',
                                        'Completed' => 'badge-status-completed',
                                        'Cancelled' => 'badge-status-cancelled',
                                        default => 'badge-status-draft',
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge }} px-2 py-1">{{ $case->status }}</span>
                            </td>
                            <td>
                                @php
                                    $priorityBadge = match($case->priority) {
                                        'High' => 'badge-priority-high',
                                        'Low' => 'badge-priority-low',
                                        default => 'badge-priority-medium',
                                    };
                                @endphp
                                <span class="badge {{ $priorityBadge }} px-2 py-1">{{ $case->priority }}</span>
                            </td>
                            <td>
                                @if($case->surgery_date)
                                    <span class="small text-muted">
                                        <i class="bi bi-calendar-event me-1"></i>{{ $case->surgery_date->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-muted small fst-italic">Not scheduled</span>
                                @endif
                            </td>
                            <td>
                                <span class="small text-muted" title="{{ $case->creator?->email }}">
                                    {{ $case->creator?->name ?? 'System' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('cases.show', $case) }}" class="btn btn-outline-primary" title="View Case Details">
                                        <i class="bi bi-eye"></i> View
                                    </a>

                                    @can('update', $case)
                                        <a href="{{ route('cases.edit', $case) }}" class="btn btn-outline-secondary" title="Edit Case Specifications">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    @endcan

                                    @can('delete', $case)
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteCaseModal" 
                                                data-case-number="{{ $case->case_number }}" 
                                                data-action-url="{{ route('cases.destroy', $case) }}" 
                                                title="Delete Case">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="bi bi-search fs-1 text-muted d-block mb-2"></i>
                                    <h6 class="fw-semibold">No medical cases match your query.</h6>
                                    <p class="small text-muted mb-3">Try adjusting your search criteria, clearing active filters, or creating a new case.</p>
                                    @if(request()->hasAny(['search', 'status', 'priority']))
                                        <a href="{{ route('cases.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Active Filters
                                        </a>
                                    @endif
                                    @can('create', App\Models\MedicalCase::class)
                                        <a href="{{ route('cases.create') }}" class="btn btn-sm btn-jajal-teal">
                                            <i class="bi bi-plus-circle me-1"></i> Create New Case
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if($cases->hasPages())
            <div class="card-footer bg-white py-3 px-4 border-top d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2">
                <div class="small text-muted">
                    Page {{ $cases->currentPage() }} of {{ $cases->lastPage() }}
                </div>
                <div>
                    {{ $cases->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
