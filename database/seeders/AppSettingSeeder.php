<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Seed default app settings.
     */
    public function run(): void
    {
        AppSetting::set(
            'directeur_nom',
            'Direction Générale',
            'email',
            'Nom du directeur',
            'Nom complet du destinataire des demandes de matériel'
        );

        AppSetting::set(
            'directeur_email',
            'direction@itmanager.local',
            'email',
            'E-mail du directeur',
            'Adresse e-mail utilisée pour les demandes de matériel via Gmail'
        );

        AppSetting::set(
            'nom_entreprise',
            'IT Manager Pro Corp',
            'general',
            'Nom de l\'entreprise',
            'Nom officiel de l\'entreprise affiché dans les communications'
        );
    }
}
