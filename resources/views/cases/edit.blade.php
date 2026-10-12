<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cases.index') }}" class="text-decoration-none">Cases</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cases.show', $case) }}" class="text-decoration-none">{{ $case->case_number }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
                <h1 class="h4 fw-bold text-dark mb-1">Edit Medical Case: <span class="font-monospace text-primary">{{ $case->case_number }}</span></h1>
                <p class="text-muted small mb-0">Update surgical status, implant specs, and scheduled surgery date</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('cases.show', $case) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Case Details
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">
            <div class="card jm-card shadow-sm">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-pencil-square text-primary"></i>
                            <h5 class="card-title h6 fw-bold mb-0 text-dark">Modify Case Details</h5>
                        </div>
                        <small class="text-muted">Record ID #{{ $case->id }} &bull; Created {{ $case->created_at->format('M d, Y') }} by {{ $case->creator?->name }}</small>
                    </div>
                    <span class="badge bg-light text-dark border font-monospace">{{ $case->patient_reference }}</span>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('cases.update', $case) }}">
                        @csrf
                        @method('PUT')
                        @include('cases._form', ['case' => $case, 'holidays' => $holidays])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
