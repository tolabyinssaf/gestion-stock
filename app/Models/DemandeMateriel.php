<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeMateriel extends Model
{
    use HasFactory;

    protected $table = 'demandes_materiel';

    public const PRIORITE_NORMALE = 'normale';
    public const PRIORITE_IMPORTANTE = 'importante';
    public const PRIORITE_URGENTE = 'urgente';

    public const PRIORITES = [
        self::PRIORITE_NORMALE,
        self::PRIORITE_IMPORTANTE,
        self::PRIORITE_URGENTE,
    ];

    public const STATUT_BROUILLON = 'brouillon';
    public const STATUT_PRETE = 'prete';
    public const STATUT_OUVERTURE_GMAIL = 'ouverture_gmail';
    public const STATUT_ENVOYEE = 'envoyee';

    public const STATUTS_PREPARATION = [
        self::STATUT_BROUILLON,
        self::STATUT_PRETE,
        self::STATUT_OUVERTURE_GMAIL,
        self::STATUT_ENVOYEE,
    ];

    protected $fillable = [
        'reference',
        'produit_id',
        'user_id',
        'quantite',
        'priorite',
        'motif',
        'commentaire',
        'statut_preparation',
        'produit_designation',
        'produit_reference',
        'produit_categorie',
        'stock_disponible_snapshot',
        'destinataire_nom',
        'email_destinataire',
        'objet_email',
        'corps_email',
        'date_ouverture_gmail',
        'est_archive',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'stock_disponible_snapshot' => 'integer',
        'date_ouverture_gmail' => 'datetime',
        'est_archive' => 'boolean',
    ];

    /**
     * Generate unique reference: DEM-YYYYMMDD-XXXX
     */
    public static function generateReference(): string
    {
        $datePart = now()->format('Ymd');
        $random = strtoupper(bin2hex(random_bytes(2)));
        $ref = 'DEM-' . $datePart . '-' . $random;

        while (static::where('reference', $ref)->exists()) {
            $random = strtoupper(bin2hex(random_bytes(2)));
            $ref = 'DEM-' . $datePart . '-' . $random;
        }

        return $ref;
    }

    /**
     * Relationship with Produit.
     */
    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    /**
     * Relationship with User (author).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('est_archive', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('est_archive', true);
    }

    public function scopeBrouillon($query)
    {
        return $query->where('statut_preparation', self::STATUT_BROUILLON);
    }

    public function scopePrete($query)
    {
        return $query->where('statut_preparation', self::STATUT_PRETE);
    }

    public function scopeOuvertureGmail($query)
    {
        return $query->where('statut_preparation', self::STATUT_OUVERTURE_GMAIL);
    }

    public function scopeEnvoyee($query)
    {
        return $query->where('statut_preparation', self::STATUT_ENVOYEE);
    }

    /**
     * Human-friendly labels
     */
    public function getPrioriteLabelAttribute(): string
    {
        return match ($this->priorite) {
            self::PRIORITE_NORMALE => 'Normale',
            self::PRIORITE_IMPORTANTE => 'Importante',
            self::PRIORITE_URGENTE => 'Urgente',
            default => ucfirst($this->priorite),
        };
    }

    public function getStatutPreparationLabelAttribute(): string
    {
        return match ($this->statut_preparation) {
            self::STATUT_BROUILLON => 'Brouillon',
            self::STATUT_PRETE => 'Préparée',
            self::STATUT_OUVERTURE_GMAIL => 'Ouverte dans Gmail',
            self::STATUT_ENVOYEE => 'Envoyée (déclaratif)',
            default => ucfirst($this->statut_preparation),
        };
    }

    /**
     * Compose Gmail compose URL
     */
    public function getGmailComposeUrlAttribute(): string
    {
        return 'https://mail.google.com/mail/?view=cm&fs=1'
            . '&to=' . urlencode($this->email_destinataire)
            . '&su=' . urlencode($this->objet_email)
            . '&body=' . urlencode($this->corps_email);
    }
}
