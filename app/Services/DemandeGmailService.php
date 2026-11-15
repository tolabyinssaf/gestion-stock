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
        string $motif,
        ?string $commentaire,
        User $responsableIt,
        ?string $destinataireNom = null,
        ?string $emailDestinataire = null
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
        $senderEmail = $responsableIt->email;
        $dateEnvoi = now()->format('d/m/Y à H:i');

        $categorie = $produit->category->nom ?? ($produit->categorie->nom ?? 'Matériel informatique');
        $designation = $produit->designation ?? $produit->nom;

        $lines = [
            '==================================================',
            'DE (Expéditeur)   : ' . $senderNom . ' <' . $senderEmail . '> (Gestionnaire de stock)',
            'À  (Destinataire) : ' . $destNom . ' <' . $destEmail . '>',
            'DATE              : ' . $dateEnvoi,
            'OBJET             : ' . $this->genererObjet($produit),
            '==================================================',
            '',
            'Bonjour ' . $destNom . ',',
            '',
            'Je vous soumets une demande de matériel informatique pour les besoins de l\'entreprise.',
            '',
            '■ MATÉRIEL DEMANDÉ :',
            '- Produit : ' . $designation,
            '- Référence : ' . $produit->reference,
            '- Catégorie : ' . $categorie,
            '- Quantité demandée : ' . $quantite . ' unité(s)',
            '- Priorité : ' . $prioriteLabel,
            '',
            '■ SITUATION DU STOCK ACTUEL :',
            '- En stock (réserve disponible) : ' . $produit->quantite_en_stock . ' unité(s)',
            '- En utilisation (postes de travail) : ' . $produit->quantite_en_utilisation . ' unité(s)',
            '- En panne (hors service) : ' . $produit->quantite_en_panne . ' unité(s)',
            '- Total suivi : ' . $produit->total_stock . ' unité(s)',
        ];

        if ($produit->seuil_minimum > 0) {
            $lines[] = '- Seuil d\'alerte stock minimum : ' . $produit->seuil_minimum . ' unité(s)';
        }

        $lines[] = '';
        $lines[] = '■ MOTIF DE LA DEMANDE :';
        $lines[] = trim($motif);

        if (!empty($commentaire)) {
            $lines[] = '';
            $lines[] = '■ OBSERVATIONS COMPLÉMENTAIRES :';
            $lines[] = trim($commentaire);
        }

        $lines[] = '';
        $lines[] = 'Je vous remercie de bien vouloir étudier cette demande.';
        $lines[] = '';
        $lines[] = 'Cordialement,';
        $lines[] = '';
        $lines[] = '--------------------------------------------------';
        $lines[] = $senderNom;
        $lines[] = 'Gestionnaire de stock — Service Informatique';
        $lines[] = 'VAR du Lassureur';
        $lines[] = 'E-mail : ' . $senderEmail;

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
