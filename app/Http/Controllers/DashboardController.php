<?php

namespace App\Http\Controllers;

use App\Models\MedicalCase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the medical case management dashboard.
     */
    public function index(Request $request): View
    {
        $counts = [
            'total' => MedicalCase::count(),
            'planning' => MedicalCase::where('status', MedicalCase::STATUS_PLANNING)->count(),
            'in_review' => MedicalCase::where('status', MedicalCase::STATUS_IN_REVIEW)->count(),
            'approved' => MedicalCase::where('status', MedicalCase::STATUS_APPROVED)->count(),
            'completed' => MedicalCase::where('status', MedicalCase::STATUS_COMPLETED)->count(),
            'draft' => MedicalCase::where('status', MedicalCase::STATUS_DRAFT)->count(),
            'cancelled' => MedicalCase::where('status', MedicalCase::STATUS_CANCELLED)->count(),
        ];

        $recentCases = MedicalCase::with('creator')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact('counts', 'recentCases'));
    }
}
