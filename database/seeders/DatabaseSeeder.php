<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Initialiser les comptes par défaut
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('123456'),
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'respo@gmail.com'],
            [
                'name' => 'Responsable IT',
                'password' => Hash::make('123456'),
                'role' => User::ROLE_RESPONSABLE_IT,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        // 2. Initialiser les 10 catégories informatiques de base
        $this->call(CategorySeeder::class);

        // 3. Initialiser les 5 emplacements d'entreprise de base
        $this->call(EmplacementSeeder::class);

        // 4. Initialiser le stock et l'historique des mouvements
        $this->call(StockMovementSeeder::class);

        // 5. Initialiser les paramètres système (e-mail direction)
        $this->call(AppSettingSeeder::class);
    }
}
