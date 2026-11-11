<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false; // Only created_at is used and immutable

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'action_type',
        'module',
        'entity_type',
        'entity_id',
        'entity_name',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relationship with author.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope filter by module.
     */
    public function scopeModule($query, ?string $module)
    {
        if ($module) {
            $query->where('module', $module);
        }
        return $query;
    }

    /**
     * Scope filter by action type.
     */
    public function scopeActionType($query, ?string $actionType)
    {
        if ($actionType) {
            $query->where('action_type', $actionType);
        }
        return $query;
    }

    /**
     * Human-friendly action badge variant.
     */
    public function getActionBadgeVariantAttribute(): string
    {
        return match ($this->action_type) {
            'creation', 'entree' => 'emerald',
            'modification', 'mise_a_jour', 'transfert', 'correction' => 'primary',
            'archivage', 'desactivation', 'sortie', 'suppression' => 'rose',
            'ouverture_gmail' => 'accent',
            default => 'slate',
        };
    }
}
