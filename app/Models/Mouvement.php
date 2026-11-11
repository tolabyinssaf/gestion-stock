<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mouvement extends Model
{
    use HasFactory;

    public const TYPE_ENTREE = 'entree';
    public const TYPE_SORTIE = 'sortie';
    public const TYPE_TRANSFERT = 'transfert';
    public const TYPE_CORRECTION = 'correction';

    public const TYPES = [
        self::TYPE_ENTREE => 'Entrée en stock',
        self::TYPE_SORTIE => 'Sortie de stock',
        self::TYPE_TRANSFERT => 'Transfert d\'emplacement',
        self::TYPE_CORRECTION => 'Correction d\'inventaire',
    ];

    protected $fillable = [
        'reference',
        'type',
        'produit_id',
        'equipement_id',
        'quantite',
        'emplacement_source_id',
        'emplacement_destination_id',
        'motif',
        'beneficiaire',
        'commentaire',
        'user_id',
        'date_mouvement',
    ];

    protected function casts(): array
    {
        return [
            'quantite' => 'integer',
            'date_mouvement' => 'date',
        ];
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }

    public function equipement(): BelongsTo
    {
        return $this->belongsTo(Equipement::class);
    }

    public function emplacementSource(): BelongsTo
    {
        return $this->belongsTo(Emplacement::class, 'emplacement_source_id');
    }

    public function emplacementDestination(): BelongsTo
    {
        return $this->belongsTo(Emplacement::class, 'emplacement_destination_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function isEntree(): bool
    {
        return $this->type === self::TYPE_ENTREE;
    }

    public function isSortie(): bool
    {
        return $this->type === self::TYPE_SORTIE;
    }

    public function isTransfert(): bool
    {
        return $this->type === self::TYPE_TRANSFERT;
    }

    public function isCorrection(): bool
    {
        return $this->type === self::TYPE_CORRECTION;
    }

    /**
     * Generate an unambiguous, collision-free movement reference.
     * Format: MVT-YYYYMMDD-XXXX (e.g. MVT-20261110-0001)
     */
    public static function generateReference(): string
    {
        $dateStr = date('Ymd');
        $prefix = "MVT-{$dateStr}-";

        $lastRef = self::where('reference', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('reference');

        $seq = 1;
        if ($lastRef && preg_match('/(\d+)$/', $lastRef, $matches)) {
            $seq = ((int) $matches[1]) + 1;
        }

        do {
            $ref = $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
            $seq++;
        } while (self::where('reference', $ref)->exists());

        return $ref;
    }

    // -------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------

    public function scopeEntrees(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_ENTREE);
    }

    public function scopeSorties(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_SORTIE);
    }

    public function scopeTransferts(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_TRANSFERT);
    }

    public function scopeCorrections(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_CORRECTION);
    }

    public function scopeRecherche(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('reference', 'like', "%{$search}%")
                ->orWhere('motif', 'like', "%{$search}%")
                ->orWhere('beneficiaire', 'like', "%{$search}%")
                ->orWhereHas('produit', fn ($p) => $p->where('nom', 'like', "%{$search}%")->orWhere('reference', 'like', "%{$search}%"))
                ->orWhereHas('equipement', fn ($e) => $e->where('code_inventaire', 'like', "%{$search}%")->orWhere('numero_serie', 'like', "%{$search}%"));
        });
    }

    public function scopeType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeProduit(Builder $query, int|string $produitId): Builder
    {
        return $query->where('produit_id', $produitId);
    }

    public function scopeEquipement(Builder $query, int|string $equipementId): Builder
    {
        return $query->where('equipement_id', $equipementId);
    }
}
