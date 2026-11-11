<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Produit extends Model
{
    use HasFactory;

    protected $table = 'produits';

    public const TYPE_INDIVIDUEL = 'individuel';
    public const TYPE_QUANTITE = 'quantite';

    public const TYPES_SUIVI = [
        self::TYPE_INDIVIDUEL,
        self::TYPE_QUANTITE,
    ];

    public const STATUT_ACTIF = 'actif';
    public const STATUT_ARCHIVE = 'archive';

    public const STATUTS = [
        self::STATUT_ACTIF,
        self::STATUT_ARCHIVE,
    ];

    protected $fillable = [
        'categorie_id',
        'nom',
        'marque',
        'modele',
        'reference',
        'type_suivi',
        'description',
        'caracteristiques',
        'photo',
        'prix_indicatif',
        'seuil_minimum',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'prix_indicatif' => 'decimal:2',
            'seuil_minimum' => 'integer',
        ];
    }

    /**
     * Relationship: Categorie
     */
    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    /**
     * Alias for categorie relationship
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    /**
     * Accessor for designation alias of nom
     */
    public function getDesignationAttribute(): ?string
    {
        return $this->nom;
    }

    /**
     * Mutator for designation alias of nom
     */
    public function setDesignationAttribute($value): void
    {
        $this->attributes['nom'] = $value;
    }

    /**
     * Relationship: Equipements
     */
    public function equipements(): HasMany
    {
        return $this->hasMany(Equipement::class);
    }

    /**
     * Relationship: Stocks (quantities by location)
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Relationship: Mouvements
     */
    public function mouvements(): HasMany
    {
        return $this->hasMany(Mouvement::class);
    }

    /**
     * Total quantity available across all locations (for quantity products)
     */
    public function getTotalStockAttribute(): int
    {
        return (int) $this->stocks()->sum('quantite');
    }

    /**
     * Stock status: rupture (= 0), faible (= 1), normal (> 1)
     */
    public function getStockStatusAttribute(): string
    {
        $total = $this->total_stock;
        if ($total === 0) {
            return 'rupture';
        }
        if ($total === 1) {
            return 'faible';
        }
        return 'normal';
    }

    /**
     * Human-readable stock status label
     */
    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'rupture' => 'Rupture de stock',
            'faible' => 'Stock faible',
            default => 'Disponible',
        };
    }

    /**
     * Photo URL accessor
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return Storage::disk('public')->url($this->photo);
        }

        return null;
    }

    /**
     * Helpers for tracking type and status
     */
    public function isIndividuel(): bool
    {
        return $this->type_suivi === self::TYPE_INDIVIDUEL;
    }

    public function isQuantite(): bool
    {
        return $this->type_suivi === self::TYPE_QUANTITE;
    }

    public function isActif(): bool
    {
        return $this->statut === self::STATUT_ACTIF;
    }

    public function isArchive(): bool
    {
        return $this->statut === self::STATUT_ARCHIVE;
    }

    public function getTypeSuiviLabelAttribute(): string
    {
        return match ($this->type_suivi) {
            self::TYPE_INDIVIDUEL => 'Individuel (Sérialisé)',
            self::TYPE_QUANTITE => 'Par quantité (Vrac)',
            default => ucfirst($this->type_suivi),
        };
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

    public function scopeCategorie(Builder $query, $categorieId): Builder
    {
        return $categorieId ? $query->where('categorie_id', $categorieId) : $query;
    }

    public function scopeTypeSuivi(Builder $query, ?string $type): Builder
    {
        return $type ? $query->where('type_suivi', $type) : $query;
    }

    public function scopeRecherche(Builder $query, ?string $terme): Builder
    {
        if (!$terme) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($terme) {
            $q->where('nom', 'like', "%{$terme}%")
              ->orWhere('reference', 'like', "%{$terme}%")
              ->orWhere('marque', 'like', "%{$terme}%")
              ->orWhere('modele', 'like', "%{$terme}%");
        });
    }
}
