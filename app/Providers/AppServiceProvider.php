<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use App\Models\Emplacement;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            // 1. Ensure new columns and unique index exist for Stock Situations
            if (Schema::hasTable('stocks')) {
                if (!Schema::hasColumn('stocks', 'situation')) {
                    Schema::table('stocks', function (Blueprint $table) {
                        $table->string('situation', 30)->default('en_stock')->after('emplacement_id')->index();
                    });
                }

                // Drop obsolete unique index on (produit_id, emplacement_id) so multiple situations can coexist
                try {
                    \Illuminate\Support\Facades\DB::statement('ALTER TABLE stocks DROP INDEX stocks_produit_id_emplacement_id_unique');
                } catch (\Throwable $e) {
                    // Ignore if already dropped
                }

                try {
                    \Illuminate\Support\Facades\DB::statement('ALTER TABLE stocks ADD UNIQUE KEY stocks_produit_emplacement_situation_unique (produit_id, emplacement_id, situation)');
                } catch (\Throwable $e) {
                    // Ignore if already exists
                }
            }

            if (Schema::hasTable('mouvements')) {
                if (!Schema::hasColumn('mouvements', 'situation_depart')) {
                    Schema::table('mouvements', function (Blueprint $table) {
                        $table->string('situation_depart', 30)->nullable()->after('type')->index();
                    });
                }
                if (!Schema::hasColumn('mouvements', 'situation_arrivee')) {
                    Schema::table('mouvements', function (Blueprint $table) {
                        $table->string('situation_arrivee', 30)->nullable()->after('situation_depart')->index();
                    });
                }
            }

            // 2. Ensure marque and modele are nullable on produits table
            if (Schema::hasTable('produits')) {
                try {
                    \Illuminate\Support\Facades\DB::statement('ALTER TABLE produits MODIFY marque VARCHAR(80) NULL DEFAULT NULL');
                    \Illuminate\Support\Facades\DB::statement('ALTER TABLE produits MODIFY modele VARCHAR(100) NULL DEFAULT NULL');
                } catch (\Throwable $e) {
                    // Ignore if already nullable or driver not supporting syntax
                }
            }

            // 2b. Ensure statut_preparation on demandes_materiel is a generous varchar
            if (Schema::hasTable('demandes_materiel')) {
                try {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE demandes_materiel MODIFY statut_preparation VARCHAR(50) NOT NULL DEFAULT 'brouillon'");
                } catch (\Throwable $e) {
                    // Ignore
                }
            }

            // 3. Ensure standard emplacements exist for situations
            if (Schema::hasTable('emplacements')) {
                if (!Emplacement::where('code', 'LOC-PROD-01')->exists()) {
                    Emplacement::create([
                        'nom' => 'Salle de production',
                        'code' => 'LOC-PROD-01',
                        'description' => 'Zone de travail et postes de production',
                        'statut' => Emplacement::STATUT_ACTIF,
                    ]);
                }
                if (!Emplacement::where('code', 'LOC-COM-01')->exists()) {
                    Emplacement::create([
                        'nom' => 'Service commercial',
                        'code' => 'LOC-COM-01',
                        'description' => 'Bureaux et équipements du service commercial',
                        'statut' => Emplacement::STATUT_ACTIF,
                    ]);
                }
            }

            // 4. Ensure default settings exist for directeur_email
            if (Schema::hasTable('app_settings')) {
                if (!\App\Models\AppSetting::where('cle', 'directeur_email')->exists()) {
                    \App\Models\AppSetting::set(
                        'directeur_email',
                        'direction@stockmateriel.local',
                        'email',
                        'E-mail du directeur',
                        'Adresse e-mail utilisée pour les demandes de matériel'
                    );
                }
            }

            // 5. Ensure public/storage symlink exists if possible
            if (!file_exists(public_path('storage'))) {
                try {
                    Artisan::call('storage:link');
                } catch (\Throwable $e) {
                    // Fallback handled transparently
                }
            }

            // 6. Synchronisation automatique des fichiers photos de stockage vers public/storage (solution robuste pour Windows)
            try {
                $storageProductsDir = storage_path('app/public/products');
                $publicProductsDir = public_path('storage/products');
                if (is_dir($storageProductsDir)) {
                    if (!is_dir($publicProductsDir)) {
                        @mkdir($publicProductsDir, 0777, true);
                    }
                    $files = @scandir($storageProductsDir) ?: [];
                    foreach ($files as $file) {
                        if ($file !== '.' && $file !== '..') {
                            $src = $storageProductsDir . DIRECTORY_SEPARATOR . $file;
                            $dst = $publicProductsDir . DIRECTORY_SEPARATOR . $file;
                            if (is_file($src) && (!file_exists($dst) || filesize($dst) !== filesize($src))) {
                                @copy($src, $dst);
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Ignore
            }
        } catch (\Throwable $e) {
            // Silently ignore if DB is not connected yet during console operations
        }
    }
}
