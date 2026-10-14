<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cases.index') }}" class="text-decoration-none">Cases</a></li>
                        <li class="breadcrumb-item active font-monospace" aria-current="page">{{ $case->case_number }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <h1 class="h4 fw-bold text-dark mb-0 font-monospace">{{ $case->case_number }}</h1>
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
                    <span class="badge {{ $statusBadge }} px-2.5 py-1 fs-6">{{ $case->status }}</span>
                </div>
            </div>

            <!-- Action Controls with Strict Authorization -->
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Cases
                </a>

                @can('update', $case)
                    <a href="{{ route('cases.edit', $case) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i> Edit Case
                    </a>
                @endcan

                @can('delete', $case)
                    <button type="button" 
                            class="btn btn-danger btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteCaseModal" 
                            data-case-number="{{ $case->case_number }}" 
                            data-action-url="{{ route('cases.destroy', $case) }}">
                        <i class="bi bi-trash3 me-1"></i> Delete Case
                    </button>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="row justify-content-center g-4">
        <div class="col-12 col-lg-8">
            <!-- Clinical Specifications Card -->
            <div class="card jm-card shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-medical text-primary"></i>
                        <h5 class="card-title h6 fw-bold mb-0 text-dark">Clinical &amp; Device Specifications</h5>
                    </div>
                    <span class="badge bg-light text-muted border">System Record #{{ $case->id }}</span>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Case Number -->
                        <div class="col-sm-6">
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Case Number</span>
                            <div class="h5 fw-bold text-dark font-monospace mb-0">{{ $case->case_number }}</div>
                        </div>

                        <!-- Patient Reference -->
                        <div class="col-sm-6">
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Patient Reference Code</span>
                            <span class="badge-patient-ref fs-6 px-3 py-1.5">
                                {{ $case->patient_reference }}
                            </span>
                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">* De-identified dummy tracking code (HIPAA / Privacy compliant)</small>
                        </div>

                        <!-- Surgeon Name -->
                        <div class="col-sm-6">
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Lead Operating Surgeon</span>
                            <div class="fw-semibold text-dark fs-6">
                                <i class="bi bi-person-circle text-muted me-1.5"></i>{{ $case->surgeon_name }}
                            </div>
                        </div>

                        <!-- Implant Type -->
                        <div class="col-sm-6">
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Implant Device Model</span>
                            <div class="fw-semibold text-dark fs-6">
                                <i class="bi bi-gear-wide-connected text-primary me-1.5"></i>{{ $case->implant_type }}
                            </div>
                        </div>

                        <!-- Status & Priority -->
                        <div class="col-sm-6">
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Current Status</span>
                            <span class="badge {{ $statusBadge }} px-3 py-1.5 fs-6">{{ $case->status }}</span>
                        </div>

                        <div class="col-sm-6">
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Urgency Priority</span>
                            @php
                                $priorityBadge = match($case->priority) {
                                    'High' => 'badge-priority-high',
                                    'Low' => 'badge-priority-low',
                                    default => 'badge-priority-medium',
                                };
                            @endphp
                            <span class="badge {{ $priorityBadge }} px-3 py-1.5 fs-6">{{ $case->priority }}</span>
                        </div>

                        <!-- Surgery Date -->
                        <div class="col-12">
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Scheduled Surgery Date</span>
                            @if($case->surgery_date)
                                <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-3">
                                    <div class="jm-stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-6">{{ $case->surgery_date->format('l, F j, Y') }}</div>
                                        <small class="text-muted">
                                            @if($case->surgery_date->isFuture())
                                                Scheduled in {{ $case->surgery_date->diffForHumans() }}
                                            @elseif($case->surgery_date->isToday())
                                                <span class="text-primary fw-bold">Scheduled for today</span>
                                            @else
                                                Operation conducted {{ $case->surgery_date->diffForHumans() }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @else
                                <div class="p-3 bg-light rounded-3 border text-muted small fst-italic">
                                    <i class="bi bi-calendar-x me-1 text-muted"></i> No surgery date has been assigned yet.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Trail & Creator Info Sidebar Card -->
        <div class="col-12 col-lg-4">
            <div class="card jm-card shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="card-title h6 fw-bold mb-0 text-dark">
                        <i class="bi bi-shield-check me-1 text-success"></i> Audit Log &amp; Attribution
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Creator Info -->
                    <div class="mb-3 pb-3 border-bottom">
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Registered By</span>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-secondary bg-opacity-25 text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ strtoupper(substr($case->creator?->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark small">{{ $case->creator?->name ?? 'System' }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $case->creator?->email }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Creation Timestamp -->
                    <div class="mb-3 pb-3 border-bottom">
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Created At</span>
                        <div class="small fw-semibold text-dark">{{ $case->created_at->format('M d, Y \a\t h:i A') }}</div>
                        <small class="text-muted" style="font-size: 0.72rem;">{{ $case->created_at->diffForHumans() }}</small>
                    </div>

                    <!-- Updated Timestamp -->
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Last Updated</span>
                        <div class="small fw-semibold text-dark">{{ $case->updated_at->format('M d, Y \a\t h:i A') }}</div>
                        <small class="text-muted" style="font-size: 0.72rem;">{{ $case->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>

            <!-- Role Action Summary -->
            <div class="card bg-light border-0 p-3 rounded-3 text-muted small">
                <div class="fw-bold text-dark mb-1"><i class="bi bi-info-circle me-1"></i> Security Note:</div>
                Clinical case modifications and deletions are strictly audited. Only authorized Administrators possess permission to remove records from the central database registry.
            </div>
        </div>
    </div>
</x-app-layout>
