<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    public const SITUATION_EN_STOCK = 'en_stock';
    public const SITUATION_EN_UTILISATION = 'en_utilisation';
    public const SITUATION_EN_PANNE = 'en_panne';

    public const SITUATIONS = [
        self::SITUATION_EN_STOCK => 'En stock',
        self::SITUATION_EN_UTILISATION => 'En utilisation',
        self::SITUATION_EN_PANNE => 'En panne',
    ];

    protected $fillable = [
        'produit_id',
        'emplacement_id',
        'situation',
        'quantite',
    ];

    protected function casts(): array
    {
        return [
            'quantite' => 'integer',
        ];
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }

    public function emplacement(): BelongsTo
    {
        return $this->belongsTo(Emplacement::class);
    }

    public function getSituationLabelAttribute(): string
    {
        return self::SITUATIONS[$this->situation ?? self::SITUATION_EN_STOCK] ?? 'En stock';
    }

    public function scopeDisponible(Builder $query): Builder
    {
        return $query->where('quantite', '>', 0);
    }

    public function scopeRupture(Builder $query): Builder
    {
        return $query->where('quantite', '<=', 0);
    }

    public function scopeProduit(Builder $query, int|string $produitId): Builder
    {
        return $query->where('produit_id', $produitId);
    }

    public function scopeEmplacement(Builder $query, int|string $emplacementId): Builder
    {
        return $query->where('emplacement_id', $emplacementId);
    }

    public function scopeSituation(Builder $query, string $situation): Builder
    {
        return $query->where('situation', $situation);
    }

    public function scopeEnStock(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('situation', self::SITUATION_EN_STOCK)
              ->orWhereNull('situation');
        });
    }

    public function scopeEnUtilisation(Builder $query): Builder
    {
        return $query->where('situation', self::SITUATION_EN_UTILISATION);
    }

    public function scopeEnPanne(Builder $query): Builder
    {
        return $query->where('situation', self::SITUATION_EN_PANNE);
    }
}
