<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Equipement extends Model
{
    use HasFactory;

    // États physiques
    public const ETAT_BON = 'bon_etat';
    public const ETAT_A_VERIFIER = 'a_verifier';
    public const ETAT_EN_PANNE = 'en_panne';
    public const ETAT_HORS_SERVICE = 'hors_service';

    public const ETATS_PHYSIQUES = [
        self::ETAT_BON => 'Bon état',
        self::ETAT_A_VERIFIER => 'À vérifier',
        self::ETAT_EN_PANNE => 'En panne',
        self::ETAT_HORS_SERVICE => 'Hors service',
    ];

    // Statuts d'utilisation
    public const STATUT_DISPONIBLE = 'disponible';
    public const STATUT_UTILISATION = 'en_utilisation';
    public const STATUT_RETIRE = 'retire_du_service';

    public const STATUTS_UTILISATION = [
        self::STATUT_DISPONIBLE => 'Disponible',
        self::STATUT_UTILISATION => 'En utilisation',
        self::STATUT_RETIRE => 'Retiré du service',
    ];

    // Statut d'archivage
    public const STATUT_ACTIF = 'actif';
    public const STATUT_ARCHIVE = 'archive';

    public const STATUTS = [
        self::STATUT_ACTIF => 'Actif',
        self::STATUT_ARCHIVE => 'Archivé',
    ];

    protected $fillable = [
        'produit_id',
        'code_inventaire',
        'numero_serie',
        'etat_physique',
        'statut_utilisation',
        'statut',
        'emplacement_id',
        'date_entree',
        'observations',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'date_entree' => 'date',
        ];
    }

    // -------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }

    public function emplacement(): BelongsTo
    {
        return $this->belongsTo(Emplacement::class);
    }

    public function activites(): HasMany
    {
        return $this->hasMany(EquipementActivite::class)->orderBy('created_at', 'desc');
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modificateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // -------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------

    public function getEtatPhysiqueLabelAttribute(): string
    {
        return self::ETATS_PHYSIQUES[$this->etat_physique] ?? ucfirst($this->etat_physique ?? '');
    }

    /**
     * Alias for etat_physique_label
     */
    public function getEtatLabelAttribute(): string
    {
        return $this->etat_physique_label;
    }

    public function getStatutUtilisationLabelAttribute(): string
    {
        return self::STATUTS_UTILISATION[$this->statut_utilisation] ?? ucfirst($this->statut_utilisation ?? '');
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS_UTILISATION[$this->statut_utilisation] ?? self::STATUTS[$this->statut] ?? ucfirst($this->statut ?? '');
    }

    // -------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------

    public function isActif(): bool
    {
        return $this->statut === self::STATUT_ACTIF;
    }

    public function isArchive(): bool
    {
        return $this->statut === self::STATUT_ARCHIVE;
    }

    public function isDisponible(): bool
    {
        return $this->statut_utilisation === self::STATUT_DISPONIBLE;
    }

    public function isEnUtilisation(): bool
    {
        return $this->statut_utilisation === self::STATUT_UTILISATION;
    }

    public function isRetire(): bool
    {
        return $this->statut_utilisation === self::STATUT_RETIRE;
    }

    public function isBonEtat(): bool
    {
        return $this->etat_physique === self::ETAT_BON;
    }

    public function isEnPanne(): bool
    {
        return $this->etat_physique === self::ETAT_EN_PANNE;
    }

    public function isHorsService(): bool
    {
        return $this->etat_physique === self::ETAT_HORS_SERVICE;
    }

    /**
     * Generate an unambiguous, unique inventory code.
     * Format: EQ-YYYY-XXXXX (e.g. EQ-2026-00001)
     */
    public static function generateCodeInventaire(): string
    {
        $year = date('Y');
        $prefix = "EQ-{$year}-";

        // Find highest existing sequence for this prefix
        $lastCode = self::where('code_inventaire', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('code_inventaire');

        $sequence = 1;
        if ($lastCode && preg_match('/(\d+)$/', $lastCode, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        do {
            $code = $prefix . str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
            $sequence++;
        } while (self::where('code_inventaire', $code)->exists());

        return $code;
    }

    // -------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------

    public function scopeActif(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_ACTIF);
    }

    public function scopeArchive(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_ARCHIVE);
    }

    public function scopeDisponible(Builder $query): Builder
    {
        return $query->where('statut_utilisation', self::STATUT_DISPONIBLE);
    }

    public function scopeEnUtilisation(Builder $query): Builder
    {
        return $query->where('statut_utilisation', self::STATUT_UTILISATION);
    }

    public function scopeEnPanne(Builder $query): Builder
    {
        return $query->where('etat_physique', self::ETAT_EN_PANNE);
    }

    public function scopeHorsService(Builder $query): Builder
    {
        return $query->where('etat_physique', self::ETAT_HORS_SERVICE);
    }

    public function scopeRecherche(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('code_inventaire', 'like', "%{$search}%")
                ->orWhere('numero_serie', 'like', "%{$search}%")
                ->orWhereHas('produit', function ($prodQuery) use ($search) {
                    $prodQuery->where('nom', 'like', "%{$search}%")
                        ->orWhere('marque', 'like', "%{$search}%")
                        ->orWhere('modele', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%");
                });
        });
    }

    public function scopeProduit(Builder $query, int|string $produitId): Builder
    {
        return $query->where('produit_id', $produitId);
    }

    public function scopeCategorie(Builder $query, int|string $categorieId): Builder
    {
        return $query->whereHas('produit', fn ($q) => $q->where('categorie_id', $categorieId));
    }

    public function scopeEmplacement(Builder $query, int|string $emplacementId): Builder
    {
        return $query->where('emplacement_id', $emplacementId);
    }

    public function scopeEtatPhysique(Builder $query, string $etat): Builder
    {
        return $query->where('etat_physique', $etat);
    }

    public function scopeStatutUtilisation(Builder $query, string $statut): Builder
    {
        return $query->where('statut_utilisation', $statut);
    }
}
