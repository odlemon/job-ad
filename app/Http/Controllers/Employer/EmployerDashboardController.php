<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Services\Employer\EmployerDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EmployerDashboardController extends Controller
{
    public function __construct(
        private EmployerDashboardService $service
    ) {
    }

    /**
     * Get employer dashboard data.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user || $user->user_type !== 'employer') {
            return response()->json([
                'message' => 'Unauthorized. Employer access required.',
            ], 403);
        }

        $employer = $user->employer;
        
        if (!$employer) {
            return response()->json([
                'message' => 'Employer profile not found.',
            ], 404);
        }

        $dashboardData = $this->service->getDashboardData($employer);

        return response()->json([
            'data' => $dashboardData,
        ]);
    }
}
