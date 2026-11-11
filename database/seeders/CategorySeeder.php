<?php

namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nom' => 'Ordinateurs portables',
                'description' => 'Laptops, ultrabooks et stations de travail mobiles pour collaborateurs.',
                'icon_name' => 'laptop',
            ],
            [
                'nom' => 'Ordinateurs fixes',
                'description' => 'Unités centrales, tours et ordinateurs tout-en-un fixes.',
                'icon_name' => 'monitor',
            ],
            [
                'nom' => 'Écrans & Affichage',
                'description' => 'Moniteurs bureautiques, écrans professionnels et dalles d\'affichage.',
                'icon_name' => 'monitor',
            ],
            [
                'nom' => 'Imprimantes & Scanners',
                'description' => 'Imprimantes réseau, multifonctions noir & blanc et couleur, scanners.',
                'icon_name' => 'file-text',
            ],
            [
                'nom' => 'Claviers & Souris',
                'description' => 'Ensembles claviers, souris ergonomiques, pavés numériques et tapis.',
                'icon_name' => 'layers',
            ],
            [
                'nom' => 'Casques & Équipements audio',
                'description' => 'Casques audio avec micro, pieuvres de conférence et haut-parleurs.',
                'icon_name' => 'activity',
            ],
            [
                'nom' => 'Équipements réseau',
                'description' => 'Switchs réseau, routeurs, bornes Wi-Fi, pare-feu et points d\'accès.',
                'icon_name' => 'hard-drive',
            ],
            [
                'nom' => 'Stockage informatique',
                'description' => 'Disques durs externes, SSD portables, clés USB et solutions NAS.',
                'icon_name' => 'hard-drive',
            ],
            [
                'nom' => 'Câbles & Adaptateurs',
                'description' => 'Câbles HDMI, DisplayPort, RJ45, adaptateurs USB-C, chargeurs secteurs.',
                'icon_name' => 'boxes',
            ],
            [
                'nom' => 'Autres accessoires',
                'description' => 'Supports PC portables, sacoches de transport, verrous antivol Kensington.',
                'icon_name' => 'package',
            ],
        ];

        foreach ($categories as $cat) {
            Categorie::updateOrCreate(
                ['nom' => $cat['nom']],
                [
                    'slug' => Str::slug($cat['nom']),
                    'description' => $cat['description'],
                    'icon_name' => $cat['icon_name'],
                    'statut' => Categorie::STATUT_ACTIF,
                ]
            );
        }
    }
}
