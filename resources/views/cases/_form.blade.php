<div class="space-y-6">
    <!-- Grid for Core Identification -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Case Number -->
        <div>
            <label for="case_number" class="block text-sm font-semibold text-gray-700">
                Case Number <span class="text-rose-600">*</span>
            </label>
            <div class="mt-1">
                <input type="text"
                       name="case_number"
                       id="case_number"
                       value="{{ old('case_number', $case->case_number) }}"
                       placeholder="e.g. CASE-2045"
                       class="w-full text-sm rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('case_number') border-rose-400 ring-rose-400 @enderror"
                       required />
            </div>
            <p class="mt-1 text-xs text-gray-500">Unique alphanumeric code identifying this implant procedure.</p>
            @error('case_number')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Patient Reference -->
        <div>
            <label for="patient_reference" class="block text-sm font-semibold text-gray-700">
                Patient Reference <span class="text-rose-600">*</span>
            </label>
            <div class="mt-1">
                <input type="text"
                       name="patient_reference"
                       id="patient_reference"
                       value="{{ old('patient_reference', $case->patient_reference) }}"
                       placeholder="e.g. PAT-AB892"
                       class="w-full text-sm rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('patient_reference') border-rose-400 ring-rose-400 @enderror"
                       required />
            </div>
            <p class="mt-1 text-xs text-gray-500">De-identified dummy code (medical privacy compliant; no real names).</p>
            @error('patient_reference')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Grid for Surgeon and Implant -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Surgeon Name -->
        <div>
            <label for="surgeon_name" class="block text-sm font-semibold text-gray-700">
                Surgeon Name <span class="text-rose-600">*</span>
            </label>
            <div class="mt-1">
                <input type="text"
                       name="surgeon_name"
                       id="surgeon_name"
                       value="{{ old('surgeon_name', $case->surgeon_name) }}"
                       placeholder="e.g. Dr. Jane Smith"
                       class="w-full text-sm rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('surgeon_name') border-rose-400 ring-rose-400 @enderror"
                       required />
            </div>
            <p class="mt-1 text-xs text-gray-500">Lead operating surgeon or clinician.</p>
            @error('surgeon_name')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Implant Type -->
        <div>
            <label for="implant_type" class="block text-sm font-semibold text-gray-700">
                Implant Type <span class="text-rose-600">*</span>
            </label>
            <div class="mt-1">
                <input type="text"
                       name="implant_type"
                       id="implant_type"
                       value="{{ old('implant_type', $case->implant_type) }}"
                       placeholder="e.g. Titanium Hip Stem / Spinal Interbody Cage"
                       class="w-full text-sm rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('implant_type') border-rose-400 ring-rose-400 @enderror"
                       required />
            </div>
            <p class="mt-1 text-xs text-gray-500">Device model or implant specification.</p>
            @error('implant_type')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Grid for Status and Priority -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Status -->
        <div>
            <label for="status" class="block text-sm font-semibold text-gray-700">
                Case Status <span class="text-rose-600">*</span>
            </label>
            <div class="mt-1">
                <select name="status"
                        id="status"
                        class="w-full text-sm rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-rose-400 ring-rose-400 @enderror"
                        required>
                    @foreach(App\Models\MedicalCase::STATUSES as $statusOption)
                        <option value="{{ $statusOption }}" {{ old('status', $case->status) === $statusOption ? 'selected' : '' }}>
                            {{ $statusOption }}
                        </option>
                    @endforeach
                </select>
            </div>
            <p class="mt-1 text-xs text-gray-500">Workflow stage from Draft through Completion.</p>
            @error('status')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Priority -->
        <div>
            <label for="priority" class="block text-sm font-semibold text-gray-700">
                Priority <span class="text-rose-600">*</span>
            </label>
            <div class="mt-1">
                <select name="priority"
                        id="priority"
                        class="w-full text-sm rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('priority') border-rose-400 ring-rose-400 @enderror"
                        required>
                    @foreach(App\Models\MedicalCase::PRIORITIES as $priorityOption)
                        <option value="{{ $priorityOption }}" {{ old('priority', $case->priority) === $priorityOption ? 'selected' : '' }}>
                            {{ $priorityOption }}
                        </option>
                    @endforeach
                </select>
            </div>
            <p class="mt-1 text-xs text-gray-500">Urgency level for clinical device logistics.</p>
            @error('priority')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Surgery Date & Bonus Public Holiday Integration -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        <!-- Surgery Date Input -->
        <div>
            <label for="surgery_date" class="block text-sm font-semibold text-gray-700">
                Surgery Date <span class="text-gray-400 font-normal">(Optional)</span>
            </label>
            <div class="mt-1">
                <input type="date"
                       name="surgery_date"
                       id="surgery_date"
                       value="{{ old('surgery_date', $case->surgery_date ? $case->surgery_date->format('Y-m-d') : '') }}"
                       class="w-full text-sm rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('surgery_date') border-rose-400 ring-rose-400 @enderror" />
            </div>
            <p class="mt-1 text-xs text-gray-500">Scheduled surgery date in YYYY-MM-DD format.</p>
            @error('surgery_date')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Bonus: Public Holidays Advisory (Nager.Date API) -->
        <div class="bg-indigo-50/60 border border-indigo-100 rounded-md p-3.5">
            <div class="flex items-center space-x-2 text-xs font-semibold text-indigo-900 mb-1">
                <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Hospital Calendar & Upcoming Holidays (Nager.Date API)</span>
            </div>
            <p class="text-xs text-gray-600 mb-2">
                Verify OT operating hours and logistics availability against public holidays:
            </p>

            @if(isset($holidays) && $holidays->isNotEmpty())
                <ul class="space-y-1 text-xs">
                    @foreach($holidays as $holiday)
                        <li class="flex items-center justify-between text-gray-700 bg-white/70 px-2 py-1 rounded border border-indigo-100">
                            <span class="font-medium truncate mr-2">{{ $holiday['name'] }} ({{ $holiday['countryCode'] }})</span>
                            <span class="font-mono text-indigo-700 flex-shrink-0">{{ $holiday['date'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-xs text-gray-500 italic">
                    External holiday service active. Operating room schedules are synchronized.
                </p>
            @endif
        </div>
    </div>
</div>
