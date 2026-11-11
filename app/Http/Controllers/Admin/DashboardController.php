<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected ReportingService $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
    }

    /**
     * Display the admin supervision dashboard.
     */
    public function index(): View
    {
        $stats = $this->reportingService->getAdminDashboardStats();

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        $lastActivity = $currentUser && $currentUser->last_login_at
            ? $currentUser->last_login_at->format('d/m/Y à H:i')
            : 'Session active';

        return view('admin.dashboard', array_merge($stats, [
            'lastActivity' => $lastActivity,
        ]));
    }
}
