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
     * Relationship: Stocks (quantities by location and situation)
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
     * Quantity available in stock (réserve).
     */
    public function getQuantiteEnStockAttribute(): int
    {
        if ($this->relationLoaded('stocks')) {
            return (int) $this->stocks->filter(function ($s) {
                return $s->situation === Stock::SITUATION_EN_STOCK || empty($s->situation);
            })->sum('quantite');
        }

        return (int) $this->stocks()->enStock()->sum('quantite');
    }

    /**
     * Quantity currently in utilization (bureaux, production, services).
     */
    public function getQuantiteEnUtilisationAttribute(): int
    {
        if ($this->relationLoaded('stocks')) {
            return (int) $this->stocks->where('situation', Stock::SITUATION_EN_UTILISATION)->sum('quantite');
        }

        return (int) $this->stocks()->enUtilisation()->sum('quantite');
    }

    /**
     * Quantity currently broken (en panne / hors service).
     */
    public function getQuantiteEnPanneAttribute(): int
    {
        if ($this->relationLoaded('stocks')) {
            return (int) $this->stocks->where('situation', Stock::SITUATION_EN_PANNE)->sum('quantite');
        }

        return (int) $this->stocks()->enPanne()->sum('quantite');
    }

    /**
     * Total quantity across all situations: En stock + En utilisation + En panne.
     */
    public function getTotalStockAttribute(): int
    {
        if ($this->relationLoaded('stocks')) {
            return (int) $this->stocks->sum('quantite');
        }

        return (int) $this->stocks()->sum('quantite');
    }

    /**
     * Alias for total quantity
     */
    public function getQuantiteTotaleAttribute(): int
    {
        return $this->total_stock;
    }

    /**
     * Stock status based strictly on units 'En stock' vs 'seuil_minimum' (default 1).
     * - 0 unit: Épuisé (rupture)
     * - 0 < qty <= seuil: Stock faible (faible)
     * - qty > seuil: Disponible (normal)
     */
    public function getStockStatusAttribute(): string
    {
        $enStock = $this->quantite_en_stock;
        $seuil = ($this->seuil_minimum !== null && $this->seuil_minimum > 0) ? (int) $this->seuil_minimum : 1;

        if ($enStock <= 0) {
            return 'rupture';
        }
        if ($enStock <= $seuil) {
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
            'rupture' => 'Épuisé',
            'faible' => 'Stock faible',
            default => 'Disponible',
        };
    }

    /**
     * Photo URL accessor – returns a route path guaranteed to work on Windows
     * even without a valid public/storage symlink.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }

        // Normalise: strip leading 'storage/' or '/' if present
        $cleanPath = ltrim(str_replace(['\\', 'storage/'], ['/', ''], $this->photo), '/');

        // Check the file actually exists on disk
        $diskPath = storage_path('app/public/' . $cleanPath);
        if (file_exists($diskPath)) {
            // Use /media/ route which is handled 100 % by Laravel (no static-file interception)
            return '/media/' . $cleanPath;
        }

        // Fallback: maybe already in public/storage via symlink
        if (file_exists(public_path('storage/' . $cleanPath))) {
            return '/media/' . $cleanPath;
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
            self::TYPE_INDIVIDUEL => 'Individuel',
            self::TYPE_QUANTITE => 'Par quantité',
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

    public function scopeEnSituation(Builder $query, ?string $situation): Builder
    {
        if (!$situation || $situation === 'tous') {
            return $query;
        }

        return $query->whereHas('stocks', function (Builder $q) use ($situation) {
            if ($situation === Stock::SITUATION_EN_STOCK) {
                $q->where(function ($sub) {
                    $sub->where('situation', Stock::SITUATION_EN_STOCK)
                        ->orWhereNull('situation');
                })->where('quantite', '>', 0);
            } else {
                $q->where('situation', $situation)->where('quantite', '>', 0);
            }
        });
    }
}
