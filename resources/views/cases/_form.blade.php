<div class="row g-4">
    <!-- Case Number -->
    <div class="col-md-6">
        <label for="case_number" class="form-label">
            Case Number <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted font-monospace"><i class="bi bi-hash"></i></span>
            <input type="text" 
                   name="case_number" 
                   id="case_number" 
                   value="{{ old('case_number', $case->case_number) }}" 
                   class="form-control font-monospace @error('case_number') is-invalid @enderror" 
                   placeholder="e.g. CASE-1045" 
                   required>
            @error('case_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="text-muted d-block mt-1">Unique clinical tracking identifier across registry.</small>
    </div>

    <!-- Patient Reference -->
    <div class="col-md-6">
        <label for="patient_reference" class="form-label">
            Patient Reference Code <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted font-monospace"><i class="bi bi-person-badge"></i></span>
            <input type="text" 
                   name="patient_reference" 
                   id="patient_reference" 
                   value="{{ old('patient_reference', $case->patient_reference) }}" 
                   class="form-control font-monospace @error('patient_reference') is-invalid @enderror" 
                   placeholder="e.g. PAT-0042" 
                   required>
            @error('patient_reference')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="text-muted d-block mt-1">De-identified dummy code only (privacy compliant; no real patient names).</small>
    </div>

    <!-- Surgeon Name -->
    <div class="col-md-6">
        <label for="surgeon_name" class="form-label">
            Lead Operating Surgeon <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
            <input type="text" 
                   name="surgeon_name" 
                   id="surgeon_name" 
                   value="{{ old('surgeon_name', $case->surgeon_name) }}" 
                   class="form-control @error('surgeon_name') is-invalid @enderror" 
                   placeholder="e.g. Dr. Eleanor Vance" 
                   required>
            @error('surgeon_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="text-muted d-block mt-1">Full clinical title and surgeon name.</small>
    </div>

    <!-- Implant Type -->
    <div class="col-md-6">
        <label for="implant_type" class="form-label">
            Medical Implant Device <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-gear-wide-connected"></i></span>
            <input type="text" 
                   name="implant_type" 
                   id="implant_type" 
                   value="{{ old('implant_type', $case->implant_type) }}" 
                   class="form-control @error('implant_type') is-invalid @enderror" 
                   placeholder="e.g. Titanium Hip Stem / Spinal Cage" 
                   required>
            @error('implant_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="text-muted d-block mt-1">Manufacturer device model or medical prosthetic specification.</small>
    </div>

    <!-- Status Dropdown -->
    <div class="col-md-6">
        <label for="status" class="form-label">
            Case Status <span class="text-danger">*</span>
        </label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach(App\Models\MedicalCase::STATUSES as $statusOption)
                <option value="{{ $statusOption }}" {{ old('status', $case->status) === $statusOption ? 'selected' : '' }}>
                    {{ $statusOption }}
                </option>
            @endforeach
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted d-block mt-1">Lifecycle phase of the surgical implant procedure.</small>
    </div>

    <!-- Priority Dropdown -->
    <div class="col-md-6">
        <label for="priority" class="form-label">
            Priority Level <span class="text-danger">*</span>
        </label>
        <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
            @foreach(App\Models\MedicalCase::PRIORITIES as $priorityOption)
                <option value="{{ $priorityOption }}" {{ old('priority', $case->priority) === $priorityOption ? 'selected' : '' }}>
                    {{ $priorityOption }}
                </option>
            @endforeach
        </select>
        @error('priority')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted d-block mt-1">Clinical urgency priority (Low, Medium, High).</small>
    </div>

    <!-- Optional Surgery Date -->
    <div class="col-md-6">
        <label for="surgery_date" class="form-label">
            Scheduled Surgery Date <span class="text-muted">(Optional)</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-calendar3"></i></span>
            <input type="date" 
                   name="surgery_date" 
                   id="surgery_date" 
                   value="{{ old('surgery_date', $case->surgery_date ? $case->surgery_date->format('Y-m-d') : '') }}" 
                   class="form-control @error('surgery_date') is-invalid @enderror">
            @error('surgery_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="text-muted d-block mt-1">Planned operation date in operating theater.</small>
    </div>

    <!-- Bonus: Upcoming Public Holidays Integration Card -->
    @if(isset($holidays) && count($holidays) > 0)
        <div class="col-12">
            <div class="card bg-light border-0 p-3 rounded-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-calendar-event text-teal text-primary"></i>
                    <span class="small fw-bold text-dark">Upcoming Public Holidays (Calendar Advisory via Nager.Date API):</span>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($holidays as $holiday)
                        <span class="badge bg-white text-dark border py-1.5 px-2">
                            <i class="bi bi-flag me-1 text-muted"></i>
                            <strong>{{ $holiday['date'] ?? '' }}:</strong> {{ $holiday['name'] ?? $holiday['localName'] ?? 'Public Holiday' }}
                        </span>
                    @endforeach
                </div>
                <small class="text-muted mt-2" style="font-size: 0.72rem;">
                    * Check operating theater availability when scheduling surgical procedures near public holidays.
                </small>
            </div>
        </div>
    @endif
</div>

<!-- Form Submission Action Row -->
<div class="d-flex align-items-center justify-content-between mt-4 pt-3 border-top">
    <a href="{{ $case->exists ? route('cases.show', $case) : route('cases.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle me-1"></i> Cancel
    </a>
    <button type="submit" class="btn btn-medical-primary px-4 shadow-sm">
        <i class="bi bi-check2-circle me-1"></i> {{ $case->exists ? 'Update Case Specifications' : 'Save & Register Case' }}
    </button>
</div>
