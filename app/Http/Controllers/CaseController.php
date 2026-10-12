<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Models\MedicalCase;
use App\Services\PublicHolidayService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CaseController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of medical cases with pagination, search, and filters.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', MedicalCase::class);

        $search = $request->query('search');
        $status = $request->query('status');
        $priority = $request->query('priority');
        $sort = $request->query('sort', 'created_at');
        $direction = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = [
            'case_number',
            'patient_reference',
            'surgeon_name',
            'implant_type',
            'status',
            'priority',
            'surgery_date',
            'created_at',
        ];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        $cases = MedicalCase::with('creator')
            ->search($search)
            ->filterStatus($status)
            ->filterPriority($priority)
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('cases.index', compact('cases', 'search', 'status', 'priority', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new medical case.
     */
    public function create(PublicHolidayService $holidayService): View
    {
        $this->authorize('create', MedicalCase::class);

        $case = new MedicalCase([
            'status' => MedicalCase::STATUS_DRAFT,
            'priority' => MedicalCase::PRIORITY_MEDIUM,
        ]);

        $holidays = $holidayService->getUpcomingHolidays(4);

        return view('cases.create', compact('case', 'holidays'));
    }

    /**
     * Store a newly created medical case in storage.
     */
    public function store(StoreCaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;

        $case = MedicalCase::create($validated);

        return redirect()
            ->route('cases.show', $case)
            ->with('success', "Medical case {$case->case_number} was created successfully.");
    }

    /**
     * Display the specified medical case.
     */
    public function show(MedicalCase $case): View
    {
        $this->authorize('view', $case);

        $case->load('creator');

        return view('cases.show', compact('case'));
    }

    /**
     * Show the form for editing the specified medical case.
     */
    public function edit(MedicalCase $case, PublicHolidayService $holidayService): View
    {
        $this->authorize('update', $case);

        $holidays = $holidayService->getUpcomingHolidays(4);

        return view('cases.edit', compact('case', 'holidays'));
    }

    /**
     * Update the specified medical case in storage.
     */
    public function update(UpdateCaseRequest $request, MedicalCase $case): RedirectResponse
    {
        $this->authorize('update', $case);

        $case->update($request->validated());

        return redirect()
            ->route('cases.show', $case)
            ->with('success', "Medical case {$case->case_number} was updated successfully.");
    }

    /**
     * Remove the specified medical case from storage (Admin only).
     */
    public function destroy(MedicalCase $case): RedirectResponse
    {
        $this->authorize('delete', $case);

        $caseNumber = $case->case_number;
        $case->delete();

        return redirect()
            ->route('cases.index')
            ->with('success', "Medical case {$caseNumber} was deleted successfully.");
    }
}
