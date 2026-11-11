<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLoggerService
{
    /**
     * Forbidden fields that MUST NEVER be recorded in logs.
     */
    protected const SENSITIVE_FIELDS = [
        'password',
        'password_confirmation',
        'remember_token',
        'token',
        'secret',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Sanitize array to prevent logging passwords or secrets.
     */
    protected function sanitizeData(?array $data): ?array
    {
        if (!$data) {
            return null;
        }

        $sanitized = [];
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), self::SENSITIVE_FIELDS, true)) {
                continue;
            }
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeData($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Record a system activity event.
     *
     * @param string $module Module name (utilisateurs, catalogue, equipements, stock, mouvements, demandes, parametres)
     * @param string $actionType Action type (creation, modification, archivage, activation, desactivation, entree, sortie, etc.)
     * @param string $description Clear human-readable summary
     * @param string|null $entityType Model or Entity class name
     * @param int|null $entityId Model ID
     * @param string|null $entityName Designation or Reference
     * @param array|null $oldValues Previous values
     * @param array|null $newValues Updated values
     * @param User|null $user Custom author if not Auth::user()
     * @return ActivityLog
     */
    public function log(
        string $module,
        string $actionType,
        string $description,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $entityName = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?User $user = null
    ): ActivityLog {
        $author = $user ?? Auth::user();

        return ActivityLog::create([
            'user_id' => $author?->id,
            'user_name' => $author?->name ?? 'Système',
            'module' => $module,
            'action_type' => $actionType,
            'description' => $description,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'entity_name' => $entityName,
            'old_values' => $this->sanitizeData($oldValues),
            'new_values' => $this->sanitizeData($newValues),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
