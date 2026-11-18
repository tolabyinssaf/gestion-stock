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
     * Generate the professional email body in French with clear Sender (De) & Recipient (À) identification.
     */
    public function genererCorps(
        Produit $produit,
        int $quantite,
        string $priorite,
        ?string $motif,
        ?string $commentaire,
        User $responsableIt,
        ?string $destinataireNom = null,
        ?string $emailDestinataire = null,
        ?string $emailExpediteur = null,
        ?float $prixEstime = null
    ): string {
        $prioriteLabel = match ($priorite) {
            DemandeMateriel::PRIORITE_IMPORTANTE => 'Importante',
            DemandeMateriel::PRIORITE_URGENTE => 'Urgente',
            default => 'Normale',
        };

        $config = $this->getDirecteurConfig();
        $destNom = !empty($destinataireNom) ? $destinataireNom : ($config['nom'] ?? 'Direction Générale');
        $destEmail = !empty($emailDestinataire) ? $emailDestinataire : ($config['email'] ?? 'Non spécifié');

        $senderNom = $responsableIt->name;
        $senderEmail = !empty($emailExpediteur) ? $emailExpediteur : $responsableIt->email;
        $dateEnvoi = now()->format('d/m/Y à H:i');

        $categorie = $produit->category->nom ?? ($produit->categorie->nom ?? 'Matériel informatique');
        $designation = $produit->designation ?? $produit->nom;

        $lines = [
            'Bonjour ' . $destNom . ',',
            '',
            'Je vous soumets une demande de matériel informatique :',
            '',
            '■ MATÉRIEL DEMANDÉ :',
            '- Produit : ' . $designation,
            '- Référence : ' . $produit->reference,
            '- Catégorie : ' . $categorie,
            '- Quantité demandée : ' . $quantite . ' unité(s)',
            '- Priorité : ' . $prioriteLabel,
        ];

        if ($prixEstime !== null && $prixEstime > 0) {
            $lines[] = '- Estimation du prix unitaire : ' . number_format($prixEstime, 2, ',', ' ') . ' € HT';
            if ($quantite > 1) {
                $totalEstime = $prixEstime * $quantite;
                $lines[] = '- Montant total estimé : ' . number_format($totalEstime, 2, ',', ' ') . ' € HT';
            }
        }

        $lines[] = '';
        $lines[] = '■ SITUATION DU STOCK ACTUEL :';
        $lines[] = '- En stock : ' . $produit->quantite_en_stock . ' unité(s)';
        $lines[] = '- En utilisation : ' . $produit->quantite_en_utilisation . ' unité(s)';
        $lines[] = '- En panne : ' . $produit->quantite_en_panne . ' unité(s)';
        $lines[] = '- Total : ' . $produit->total_stock . ' unité(s)';

        if ($produit->seuil_minimum > 0) {
            $lines[] = '- Seuil d\'alerte : ' . $produit->seuil_minimum . ' unité(s)';
        }

        if (!empty(trim((string)$motif))) {
            $lines[] = '';
            $lines[] = '■ MOTIF DE LA DEMANDE :';
            $lines[] = trim($motif);
        }

        if (!empty(trim((string)$commentaire))) {
            $lines[] = '';
            $lines[] = '■ OBSERVATIONS :';
            $lines[] = trim($commentaire);
        }

        $lines[] = '';
        $lines[] = 'Je vous remercie de bien vouloir étudier cette demande.';
        $lines[] = '';
        $lines[] = 'Cordialement,';
        $lines[] = $senderNom;

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
