<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\DemandeMateriel;
use App\Models\Produit;
use App\Models\User;

class DemandeGmailService
{
    /**
     * Get the configured director email settings.
     *
     * @return array{nom: string|null, email: string|null}
     */
    public function getDirecteurConfig(): array
    {
        $email = AppSetting::get('directeur_email');
        $nom = AppSetting::get('directeur_nom', 'Direction Générale');

        return [
            'nom' => $nom,
            'email' => $email,
        ];
    }

    /**
     * Check if director email is properly configured.
     */
    public function isConfigured(): bool
    {
        $config = $this->getDirecteurConfig();
        return !empty($config['email']) && filter_var($config['email'], FILTER_VALIDATE_EMAIL);
    }

    /**
     * Generate the professional email subject.
     */
    public function genererObjet(Produit $produit): string
    {
        return 'Demande de matériel informatique — ' . ($produit->designation ?? $produit->nom);
    }

    /**
     * Generate the professional email body in French.
     */
    public function genererCorps(
        Produit $produit,
        int $quantite,
        string $priorite,
        string $motif,
        ?string $commentaire,
        User $responsableIt
    ): string {
        $prioriteLabel = match ($priorite) {
            DemandeMateriel::PRIORITE_IMPORTANTE => 'Importante',
            DemandeMateriel::PRIORITE_URGENTE => 'Urgente',
            default => 'Normale',
        };

        $disponibilite = $produit->total_stock;
        $uniteTexte = $disponibilite . ' ' . ($disponibilite > 1 ? 'unités' : 'unité');

        $categorie = $produit->category->nom ?? ($produit->categorie->nom ?? 'Matériel informatique');
        $designation = $produit->designation ?? $produit->nom;

        $lines = [
            'Bonjour,',
            '',
            'Je souhaite vous soumettre une demande de matériel informatique pour les besoins de l\'entreprise.',
            '',
            'Matériel demandé :',
            '- Produit : ' . $designation,
            '- Référence : ' . $produit->reference,
            '- Catégorie : ' . $categorie,
            '- Quantité demandée : ' . $quantite,
            '- Priorité : ' . $prioriteLabel,
            '',
            'Situation actuelle :',
            '- Disponibilité en stock : ' . $uniteTexte,
        ];

        if ($produit->seuil_minimum > 0) {
            $lines[] = '- Seuil d\'alerte : ' . $produit->seuil_minimum . ' unités';
        }

        $lines[] = '';
        $lines[] = 'Motif de la demande :';
        $lines[] = trim($motif);

        if (!empty($commentaire)) {
            $lines[] = '';
            $lines[] = 'Observations complémentaires :';
            $lines[] = trim($commentaire);
        }

        $lines[] = '';
        $lines[] = 'Je vous remercie de bien vouloir étudier cette demande.';
        $lines[] = '';
        $lines[] = 'Cordialement,';
        $lines[] = '';
        $lines[] = $responsableIt->name;
        $lines[] = 'Service informatique — IT Manager Pro';

        return implode("\r\n", $lines);
    }

    /**
     * Build the Gmail compose URL with RFC 3986 encoding.
     */
    public function buildGmailUrl(string $email, string $sujet, string $corps): string
    {
        return 'https://mail.google.com/mail/?view=cm&fs=1'
            . '&to=' . rawurlencode($email)
            . '&su=' . rawurlencode($sujet)
            . '&body=' . rawurlencode($corps);
    }
}
