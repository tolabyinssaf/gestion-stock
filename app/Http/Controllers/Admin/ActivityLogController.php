<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Ensure activity_logs table exists.
     */
    protected function ensureTableExists(): void
    {
        if (!Schema::hasTable('activity_logs')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                // Ignore
            }
        }
    }

    /**
     * Display the paginated and filtered activity logs list.
     */
    public function index(Request $request): View
    {
        $this->ensureTableExists();

        $query = ActivityLog::with('user');

        // 1. Search (description or entity_name)
        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('entity_name', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        // 2. Module filter
        if ($module = $request->string('module')->trim()->value()) {
            $query->where('module', $module);
        }

        // 3. Action type filter
        if ($actionType = $request->string('action_type')->trim()->value()) {
            $query->where('action_type', $actionType);
        }

        // 4. User filter
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        // 5. Date filters
        if ($dateDebut = $request->input('date_debut')) {
            $query->whereDate('created_at', '>=', $dateDebut);
        }

        if ($dateFin = $request->input('date_fin')) {
            $query->whereDate('created_at', '<=', $dateFin);
        }

        $logs = $query->latest('created_at')->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get();

        return view('admin.activities.index', compact('logs', 'users'));
    }

    /**
     * Display a specific log entry details.
     */
    public function show(ActivityLog $log): View
    {
        $this->ensureTableExists();
        $log->load('user');

        return view('admin.activities.show', compact('log'));
    }
}
