<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cases.index') }}" class="text-decoration-none">Cases</a></li>
                        <li class="breadcrumb-item active" aria-current="page">New Case</li>
                    </ol>
                </nav>
                <h1 class="h4 fw-bold text-dark mb-1">Register New Medical Case</h1>
                <p class="text-muted small mb-0">Record implant device specifications and clinical schedule</p>
            </div>
            <div>
                <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Directory
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">
            <div class="card jm-card shadow-sm">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle-fill text-primary"></i>
                        <h5 class="card-title h6 fw-bold mb-0 text-dark">Clinical Case Specifications</h5>
                    </div>
                    <small class="text-muted">All fields marked with an asterisk (<span class="text-danger">*</span>) are mandatory.</small>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('cases.store') }}">
                        @csrf
                        @include('cases._form', ['case' => $case, 'holidays' => $holidays])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
