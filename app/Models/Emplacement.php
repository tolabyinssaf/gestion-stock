<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Emplacement extends Model
{
    use HasFactory;

    public const STATUT_ACTIF = 'actif';
    public const STATUT_ARCHIVE = 'archive';

    protected $fillable = [
        'nom',
        'code',
        'description',
        'statut',
    ];

    /**
     * Get the equipments in this location.
     */
    public function equipements(): HasMany
    {
        return $this->hasMany(Equipement::class);
    }

    /**
     * Get the quantity stock records in this location.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Get movements originating from this location.
     */
    public function mouvementsSource(): HasMany
    {
        return $this->hasMany(Mouvement::class, 'emplacement_source_id');
    }

    /**
     * Get movements ending at this location.
     */
    public function mouvementsDestination(): HasMany
    {
        return $this->hasMany(Mouvement::class, 'emplacement_destination_id');
    }

    /**
     * Scope to only active locations.
     */
    public function scopeActif(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_ACTIF);
    }

    /**
     * Scope to search by name or code.
     */
    public function scopeRecherche(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        });
    }

    public function isActif(): bool
    {
        return $this->statut === self::STATUT_ACTIF;
    }
}
