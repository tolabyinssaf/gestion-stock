<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Categorie extends Model
{
    use HasFactory;

    protected $table = 'categories';

    public const STATUT_ACTIF = 'actif';
    public const STATUT_ARCHIVE = 'archive';

    public const STATUTS = [
        self::STATUT_ACTIF,
        self::STATUT_ARCHIVE,
    ];

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'icon_name',
        'statut',
    ];

    /**
     * Auto-generate slug on saving if empty.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($categorie) {
            if (empty($categorie->slug)) {
                $categorie->slug = Str::slug($categorie->nom);
            }
        });
    }

    /**
     * Relationship: Produits
     */
    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class, 'categorie_id');
    }

    /**
     * Status helpers
     */
    public function isActif(): bool
    {
        return $this->statut === self::STATUT_ACTIF;
    }

    public function isArchive(): bool
    {
        return $this->statut === self::STATUT_ARCHIVE;
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_ACTIF => 'Actif',
            self::STATUT_ARCHIVE => 'Archivé',
            default => 'Inconnu',
        };
    }

    /**
     * Scopes
     */
    public function scopeActif(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_ACTIF);
    }

    public function scopeArchive(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_ARCHIVE);
    }

    public function scopeRecherche(Builder $query, ?string $terme): Builder
    {
        if (!$terme) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($terme) {
            $q->where('nom', 'like', "%{$terme}%")
              ->orWhere('description', 'like', "%{$terme}%");
        });
    }
}
