<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h1 class="h4 fw-bold text-dark mb-1">Clinical Case Dashboard</h1>
                <p class="text-muted small mb-0">Real-time implant scheduling and medical case tracking overview</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list-ul me-1"></i> View All Cases
                </a>
                @can('create', App\Models\MedicalCase::class)
                    <a href="{{ route('cases.create') }}" class="btn btn-jajal-teal btn-sm shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Create Case
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <!-- Welcome / Role Context Banner -->
    <div class="card jm-card mb-4 border-0 shadow-sm" style="background: linear-gradient(90deg, #f8fafc 0%, #edf2f7 100%);">
        <div class="card-body p-3 p-md-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="jm-brand-icon" style="width: 48px; height: 48px; font-size: 1.5rem;">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-bold text-dark">Welcome, {{ Auth::user()->name }}</h5>
                    <p class="mb-0 text-muted small">
                        @if(Auth::user()->isAdmin())
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle me-1">Admin Access</span>
                            Full authority to create, review, update statuses, and delete surgical cases.
                        @elseif(Auth::user()->isEngineer())
                            <span class="badge bg-info-subtle text-info border border-info-subtle me-1">Engineer Access</span>
                            Technical specialist permission to create, edit, and transition case statuses. Deletion restricted.
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle me-1">Viewer Access</span>
                            Read-only clinical auditing mode: Browse, search, filter, and inspect detailed case specifications.
                        @endif
                    </p>
                </div>
            </div>
            <div class="text-md-end">
                <span class="text-muted small">
                    <i class="bi bi-calendar3 me-1"></i> System Date: <strong>{{ now()->format('F d, Y') }}</strong>
                </span>
            </div>
        </div>
    </div>

    <!-- Required Statistic Cards (Total, Planning, In Review, Approved, Completed) -->
    <div class="row g-3 mb-4">
        <!-- Total Cases -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card jm-card jm-card-stat border-total h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Cases</span>
                        <div class="jm-stat-icon-wrapper bg-light text-dark">
                            <i class="bi bi-folder2"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-dark mb-1">{{ number_format($counts['total']) }}</div>
                    <a href="{{ route('cases.index') }}" class="small text-decoration-none text-primary">
                        Registry directory &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Planning -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card jm-card jm-card-stat border-planning h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-warning-emphasis small fw-semibold text-uppercase" style="font-size: 0.72rem;">Planning</span>
                        <div class="jm-stat-icon-wrapper bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-dark mb-1">{{ number_format($counts['planning']) }}</div>
                    <a href="{{ route('cases.index', ['status' => 'Planning']) }}" class="small text-decoration-none text-warning-emphasis">
                        Filter planning &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- In Review -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card jm-card jm-card-stat border-review h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-primary small fw-semibold text-uppercase" style="font-size: 0.72rem;">In Review</span>
                        <div class="jm-stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-clipboard2-pulse"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-dark mb-1">{{ number_format($counts['in_review']) }}</div>
                    <a href="{{ route('cases.index', ['status' => 'In Review']) }}" class="small text-decoration-none text-primary">
                        Filter in review &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Approved -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card jm-card jm-card-stat border-approved h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-indigo small fw-semibold text-uppercase" style="font-size: 0.72rem; color: #4f46e5;">Approved</span>
                        <div class="jm-stat-icon-wrapper bg-indigo bg-opacity-10" style="background-color: rgba(99, 102, 241, 0.1); color: #4f46e5;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-dark mb-1">{{ number_format($counts['approved']) }}</div>
                    <a href="{{ route('cases.index', ['status' => 'Approved']) }}" class="small text-decoration-none" style="color: #4f46e5;">
                        Filter approved &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="col-6 col-md-4 col-xl">
            <div class="card jm-card jm-card-stat border-completed h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-success small fw-semibold text-uppercase" style="font-size: 0.72rem;">Completed</span>
                        <div class="jm-stat-icon-wrapper bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-dark mb-1">{{ number_format($counts['completed']) }}</div>
                    <a href="{{ route('cases.index', ['status' => 'Completed']) }}" class="small text-decoration-none text-success">
                        Filter completed &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Status Summary Bar -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card jm-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge badge-status-draft px-2 py-1">Draft</span>
                        <span class="text-muted small">Initial preparation &amp; pending specs</span>
                    </div>
                    <span class="fw-bold text-dark">{{ $counts['draft'] ?? 0 }} cases</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card jm-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge badge-status-cancelled px-2 py-1">Cancelled</span>
                        <span class="text-muted small">Aborted or deferred procedures</span>
                    </div>
                    <span class="fw-bold text-dark">{{ $counts['cancelled'] ?? 0 }} cases</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Cases Card -->
    <div class="card jm-card">
        <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
            <div>
                <h5 class="card-title h6 fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history me-1 text-primary"></i> Recently Created &amp; Updated Cases
                </h5>
                <small class="text-muted">Latest surgical implants registered in Jajal Medical database</small>
            </div>
            <a href="{{ route('cases.index') }}" class="btn btn-sm btn-outline-secondary">
                View All Directory
            </a>
        </div>

        <div class="table-responsive">
            <table class="table jm-table align-middle">
                <thead>
                    <tr>
                        <th>Case Number</th>
                        <th>Patient Ref</th>
                        <th>Surgeon Name</th>
                        <th>Implant Device</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Surgery Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCases as $case)
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
                            <td class="text-end">
                                <a href="{{ route('cases.show', $case) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="View Details">
                                    <i class="bi bi-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                    <h6 class="fw-semibold">No medical cases recorded yet.</h6>
                                    <p class="small text-muted mb-3">Begin by seeding the database or registering a new implant case.</p>
                                    @can('create', App\Models\MedicalCase::class)
                                        <a href="{{ route('cases.create') }}" class="btn btn-sm btn-jajal-teal">
                                            <i class="bi bi-plus-circle me-1"></i> Create First Case
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
