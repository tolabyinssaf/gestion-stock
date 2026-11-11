<?php

namespace Database\Seeders;

use App\Models\Emplacement;
use Illuminate\Database\Seeder;

class EmplacementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emplacements = [
            [
                'nom' => 'Réserve IT',
                'code' => 'LOC-RES-IT',
                'description' => 'Local technique sécurisé de stockage et de préparation du matériel informatique.',
                'statut' => Emplacement::STATUT_ACTIF,
            ],
            [
                'nom' => 'Bureau direction',
                'code' => 'LOC-DIR-01',
                'description' => 'Bureaux de la direction générale et des membres du comité de direction.',
                'statut' => Emplacement::STATUT_ACTIF,
            ],
            [
                'nom' => 'Salle informatique',
                'code' => 'LOC-SRV-01',
                'description' => 'Salle climatisée hébergeant les baies réseaux, routeurs et serveurs physiques.',
                'statut' => Emplacement::STATUT_ACTIF,
            ],
            [
                'nom' => 'Salle de réunion',
                'code' => 'LOC-REU-01',
                'description' => 'Espaces de réunion collaboratifs équipés en visioconférence et écrans interactifs.',
                'statut' => Emplacement::STATUT_ACTIF,
            ],
            [
                'nom' => 'Bureau administratif',
                'code' => 'LOC-ADM-01',
                'description' => 'Postes de travail des services administratifs, RH et comptabilité.',
                'statut' => Emplacement::STATUT_ACTIF,
            ],
        ];

        foreach ($emplacements as $item) {
            Emplacement::updateOrCreate(
                ['code' => $item['code']],
                [
                    'nom' => $item['nom'],
                    'description' => $item['description'],
                    'statut' => $item['statut'],
                ]
            );
        }
    }
}
