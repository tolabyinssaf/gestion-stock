<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'produit_id',
        'emplacement_id',
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
}
