<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Models\MedicalCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CaseApiController extends Controller
{
    /**
     * List medical cases with pagination, search, and filters.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', MedicalCase::class);

        $search = $request->query('search');
        $status = $request->query('status');
        $priority = $request->query('priority');
        $perPage = (int) $request->query('per_page', 10);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 10;

        $cases = MedicalCase::with('creator:id,name,email,role')
            ->search($search)
            ->filterStatus($status)
            ->filterPriority($priority)
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Medical cases retrieved successfully.',
            'data' => $cases,
        ], 200);
    }

    /**
     * Create a new medical case.
     */
    public function store(StoreCaseRequest $request): JsonResponse
    {
        // StoreCaseRequest checks authorize() via CasePolicy@create
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;

        $case = MedicalCase::create($validated);
        $case->load('creator:id,name,email,role');

        return response()->json([
            'success' => true,
            'message' => "Medical case {$case->case_number} created successfully.",
            'data' => $case,
        ], 201);
    }

    /**
     * Retrieve a single medical case by ID.
     */
    public function show(Request $request, string|int $id): JsonResponse
    {
        $case = MedicalCase::with('creator:id,name,email,role')->find($id);

        if (!$case) {
            return response()->json([
                'success' => false,
                'message' => "Medical case not found with ID {$id}.",
                'data' => null,
            ], 404);
        }

        Gate::authorize('view', $case);

        return response()->json([
            'success' => true,
            'message' => 'Medical case details retrieved successfully.',
            'data' => $case,
        ], 200);
    }

    /**
     * Update an existing medical case by ID.
     */
    public function update(UpdateCaseRequest $request, string|int $id): JsonResponse
    {
        $case = MedicalCase::find($id);

        if (!$case) {
            return response()->json([
                'success' => false,
                'message' => "Medical case not found with ID {$id}.",
                'data' => null,
            ], 404);
        }

        // UpdateCaseRequest validates and checks policy update permission
        $case->update($request->validated());
        $case->load('creator:id,name,email,role');

        return response()->json([
            'success' => true,
            'message' => "Medical case {$case->case_number} updated successfully.",
            'data' => $case,
        ], 200);
    }

    /**
     * Delete a medical case by ID (Admin only).
     */
    public function destroy(Request $request, string|int $id): JsonResponse
    {
        $case = MedicalCase::find($id);

        if (!$case) {
            return response()->json([
                'success' => false,
                'message' => "Medical case not found with ID {$id}.",
                'data' => null,
            ], 404);
        }

        Gate::authorize('delete', $case);

        $caseNumber = $case->case_number;
        $case->delete();

        return response()->json([
            'success' => true,
            'message' => "Medical case {$caseNumber} deleted successfully.",
            'data' => null,
        ], 200);
    }
}
