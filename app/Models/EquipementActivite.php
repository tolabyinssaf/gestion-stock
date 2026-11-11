<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipementActivite extends Model
{
    public $timestamps = false;

    protected $table = 'equipement_activites';

    protected $fillable = [
        'equipement_id',
        'user_id',
        'action',
        'description',
        'anciennes_valeurs',
        'nouvelles_valeurs',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'anciennes_valeurs' => 'array',
            'nouvelles_valeurs' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function equipement(): BelongsTo
    {
        return $this->belongsTo(Equipement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
