<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Services\ReportingService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected ReportingService $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
    }

    /**
     * Display the IT dashboard with complete real-time KPIs and chart data.
     */
    public function index(): View
    {
        $stats = $this->reportingService->getItDashboardStats();

        return view('it.dashboard', $stats);
    }
}
