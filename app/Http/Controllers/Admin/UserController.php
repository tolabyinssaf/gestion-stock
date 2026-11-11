<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users with KPI indicators and search/filters.
     */
    public function index(Request $request): View
    {
        // 1. Calculate KPI Metrics
        $totalUsers = User::count();
        $activeUsers = User::where('status', User::STATUS_ACTIVE)->count();
        $inactiveUsers = User::where('status', User::STATUS_INACTIVE)->count();
        $itUsersCount = User::where('role', User::ROLE_RESPONSABLE_IT)->count();

        // 2. Query with search and filters
        $search = $request->string('search')->trim()->value();
        $role = $request->string('role')->trim()->value();
        $status = $request->string('status')->trim()->value();

        $users = User::query()
            ->when($search, fn ($q) => $q->search($search))
            ->when($role, fn ($q) => $q->role($role))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'itUsersCount',
            'search',
            'role',
            'status'
        ));
    }

    protected \App\Services\ActivityLoggerService $activityLogger;

    public function __construct(\App\Services\ActivityLoggerService $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        $this->activityLogger->log(
            module: 'utilisateurs',
            actionType: 'creation',
            description: "Création du compte utilisateur {$user->name} ({$user->email})",
            entityType: 'User',
            entityId: $user->id,
            entityName: $user->name,
            oldValues: null,
            newValues: ['name' => $user->name, 'email' => $user->email, 'role' => $user->role, 'status' => $user->status]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'L\'utilisateur ' . $request->name . ' a été créé avec succès.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        // Security Rule: Guard against demoting or deactivating the last active administrator
        if ($user->isAdmin()) {
            $otherActiveAdmins = User::where('role', User::ROLE_ADMIN)
                ->where('status', User::STATUS_ACTIVE)
                ->where('id', '!=', $user->id)
                ->count();

            if ($otherActiveAdmins === 0) {
                if ($request->role !== User::ROLE_ADMIN) {
                    return back()->with('error', 'Action impossible : le système doit conserver au moins un Administrateur actif.');
                }
                if ($request->status === User::STATUS_INACTIVE) {
                    return back()->with('error', 'Action impossible : vous ne pouvez pas désactiver le dernier Administrateur actif.');
                }
            }
        }

        $oldValues = ['name' => $user->name, 'email' => $user->email, 'role' => $user->role, 'status' => $user->status];

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $this->activityLogger->log(
            module: 'utilisateurs',
            actionType: 'modification',
            description: "Mise à jour du compte utilisateur {$user->name}",
            entityType: 'User',
            entityId: $user->id,
            entityName: $user->name,
            oldValues: $oldValues,
            newValues: ['name' => $user->name, 'email' => $user->email, 'role' => $user->role, 'status' => $user->status]
        );

        // If user was made inactive, invalidate active sessions immediately
        if ($user->isInactive()) {
            $this->terminateUserSessions($user->id);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Le compte de ' . $user->name . ' a été mis à jour avec succès.');
    }

    /**
     * Toggle user status (actif <-> inactif).
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        // Security Rule: cannot deactivate last active administrator
        if ($user->isAdmin() && $user->isActive()) {
            $otherActiveAdmins = User::where('role', User::ROLE_ADMIN)
                ->where('status', User::STATUS_ACTIVE)
                ->where('id', '!=', $user->id)
                ->count();

            if ($otherActiveAdmins === 0) {
                return back()->with('error', 'Action impossible : vous ne pouvez pas désactiver le dernier Administrateur actif du système.');
            }
        }

        $oldStatus = $user->status;
        $newStatus = $user->isActive() ? User::STATUS_INACTIVE : User::STATUS_ACTIVE;
        $user->update(['status' => $newStatus]);

        $action = $newStatus === User::STATUS_ACTIVE ? 'activation' : 'desactivation';
        $statusLabel = $newStatus === User::STATUS_ACTIVE ? 'activé' : 'désactivé';

        $this->activityLogger->log(
            module: 'utilisateurs',
            actionType: $action,
            description: "Changement de statut du compte {$user->name} vers {$newStatus}",
            entityType: 'User',
            entityId: $user->id,
            entityName: $user->name,
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => $newStatus]
        );

        // If account deactivated, purge active sessions
        if ($newStatus === User::STATUS_INACTIVE) {
            $this->terminateUserSessions($user->id);
        }

        return back()->with('success', "Le compte de {$user->name} a été {$statusLabel} avec succès.");
    }

    /**
     * Reset user password by administrator.
     */
    public function resetPassword(ResetPasswordRequest $request, User $user): RedirectResponse
    {
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', "Le mot de passe de {$user->name} a été réinitialisé avec succès.");
    }

    /**
     * Terminate all active sessions for a user.
     */
    protected function terminateUserSessions(int $userId): void
    {
        try {
            DB::table('sessions')->where('user_id', $userId)->delete();
        } catch (\Throwable $e) {
            // Ignore if sessions table is not using database driver
        }
    }
}
